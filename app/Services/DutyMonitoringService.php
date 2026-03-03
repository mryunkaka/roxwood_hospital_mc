<?php

namespace App\Services;

use App\Models\EmsSale;
use App\Models\MedicOperasiPlastik;
use App\Models\Sale;
use App\Models\UserRh;
use App\Models\UserFarmasiStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DutyMonitoringService
{
    public function __construct(
        private readonly DutyTimeCalculator $calc,
    ) {}

    /**
     * @return array{
     *   users: Collection<int, array{
     *     id:int,
     *     full_name:string,
     *     position:string,
     *     role:string,
     *     is_active:bool,
     *     status:string,
     *     last_activity_at: ?Carbon,
     *     auto_offline_at: ?Carbon,
     *     duty_seconds_total:int,
     *     duty_seconds_farmasi:int,
     *     duty_seconds_medis:int,
     *     trx_count_farmasi:int,
     *     trx_count_medis:int
     *   }>,
     *   meta: array{unmapped_ems_names: array<string,int>}
     * }
     */
    public function build(Carbon $start, Carbon $end, int $windowSeconds = 300): array
    {
        $users = UserRh::query()
            ->select(['id', 'full_name', 'position', 'role', 'is_active'])
            ->where('is_active', 1)
            ->orderBy('full_name')
            ->get();

        $users = $users->filter(function ($u) {
            $pos = Str::of((string) ($u->position ?? ''))->lower()->toString();
            return !str_contains($pos, 'trainee');
        })->values();

        $normalizeName = fn (string $v) => Str::of($v)->trim()->lower()->replaceMatches('/\\s+/', ' ')->toString();
        $nameToId = $users->mapWithKeys(fn ($u) => [$normalizeName((string) $u->full_name) => (int) $u->id])->all();

        $farmasiTimes = [];
        $medisTimes = [];
        $farmasiCounts = [];
        $medisCounts = [];

        // FARMASI: sales (punya medic_user_id)
        Sale::query()
            ->whereBetween('created_at', [$start, $end])
            ->select(['medic_user_id', 'created_at'])
            ->orderBy('created_at')
            ->chunk(2000, function ($rows) use (&$farmasiTimes, &$farmasiCounts) {
                foreach ($rows as $r) {
                    $uid = (int) ($r->medic_user_id ?? 0);
                    if ($uid <= 0 || empty($r->created_at)) {
                        continue;
                    }
                    $farmasiTimes[$uid][] = Carbon::parse($r->created_at);
                    $farmasiCounts[$uid] = (int) (($farmasiCounts[$uid] ?? 0) + 1);
                }
            });

        // MEDIS: EMS sales (punya medic_name saja -> map ke user_rh.full_name)
        $unmappedEms = [];
        EmsSale::query()
            ->whereBetween('created_at', [$start, $end])
            ->select(['medic_name', 'created_at'])
            ->orderBy('created_at')
            ->chunk(2000, function ($rows) use (&$medisTimes, &$medisCounts, &$unmappedEms, $nameToId, $normalizeName) {
                foreach ($rows as $r) {
                    $nameRaw = (string) ($r->medic_name ?? '');
                    $name = $normalizeName($nameRaw);
                    $uid = (int) ($nameToId[$name] ?? 0);
                    if ($uid <= 0) {
                        if ($nameRaw !== '') {
                            $unmappedEms[$nameRaw] = (int) (($unmappedEms[$nameRaw] ?? 0) + 1);
                        }
                        continue;
                    }
                    if (empty($r->created_at)) {
                        continue;
                    }
                    $medisTimes[$uid][] = Carbon::parse($r->created_at);
                    $medisCounts[$uid] = (int) (($medisCounts[$uid] ?? 0) + 1);
                }
            });

        // MEDIS: Operasi Plastik (created_at untuk pengaju)
        MedicOperasiPlastik::query()
            ->whereBetween('created_at', [$start, $end])
            ->select(['id_user', 'created_at'])
            ->orderBy('created_at')
            ->chunk(2000, function ($rows) use (&$medisTimes, &$medisCounts) {
                foreach ($rows as $r) {
                    $uid = (int) ($r->id_user ?? 0);
                    if ($uid <= 0 || empty($r->created_at)) {
                        continue;
                    }
                    $medisTimes[$uid][] = Carbon::parse($r->created_at);
                    $medisCounts[$uid] = (int) (($medisCounts[$uid] ?? 0) + 1);
                }
            });

        // MEDIS: Operasi Plastik (approved_at untuk penanggung jawab / approver)
        MedicOperasiPlastik::query()
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$start, $end])
            ->select(['approved_by', 'approved_at'])
            ->orderBy('approved_at')
            ->chunk(2000, function ($rows) use (&$medisTimes, &$medisCounts) {
                foreach ($rows as $r) {
                    $uid = (int) ($r->approved_by ?? 0);
                    if ($uid <= 0 || empty($r->approved_at)) {
                        continue;
                    }
                    $medisTimes[$uid][] = Carbon::parse($r->approved_at);
                    $medisCounts[$uid] = (int) (($medisCounts[$uid] ?? 0) + 1);
                }
            });

        // Last activity (recent only, for status online/offline)
        $recentSince = now()->subDay();
        $lastFarmasi = Sale::query()
            ->where('created_at', '>=', $recentSince)
            ->select('medic_user_id', DB::raw('MAX(created_at) AS last_at'))
            ->whereNotNull('medic_user_id')
            ->groupBy('medic_user_id')
            ->pluck('last_at', 'medic_user_id')
            ->all();

        $lastEmsByName = EmsSale::query()
            ->where('created_at', '>=', $recentSince)
            ->selectRaw("LOWER(TRIM(medic_name)) AS medic_key")
            ->selectRaw('MAX(created_at) AS last_at')
            ->groupBy('medic_key')
            ->pluck('last_at', 'medic_key')
            ->all();

        $lastOplasCreate = MedicOperasiPlastik::query()
            ->where('created_at', '>=', $recentSince)
            ->select('id_user', DB::raw('MAX(created_at) AS last_at'))
            ->groupBy('id_user')
            ->pluck('last_at', 'id_user')
            ->all();

        $lastOplasApprove = MedicOperasiPlastik::query()
            ->whereNotNull('approved_at')
            ->where('approved_at', '>=', $recentSince)
            ->select('approved_by', DB::raw('MAX(approved_at) AS last_at'))
            ->groupBy('approved_by')
            ->pluck('last_at', 'approved_by')
            ->all();

        $now = now();

        $presence = UserFarmasiStatus::query()
            ->whereIn('user_id', $users->pluck('id')->all())
            ->select(['user_id', 'status', 'auto_offline_at', 'last_confirm_at', 'updated_at'])
            ->get()
            ->keyBy('user_id');

	        $rows = $users->map(function ($u) use (
	            $farmasiTimes,
	            $medisTimes,
	            $farmasiCounts,
	            $medisCounts,
	            $windowSeconds,
	            $lastFarmasi,
	            $lastEmsByName,
	            $lastOplasCreate,
	            $lastOplasApprove,
	            $nameToId,
	            $normalizeName,
	            $now,
	            $presence,
	        ) {
            $uid = (int) $u->id;

            $farmasi = $farmasiTimes[$uid] ?? [];
            $medis = $medisTimes[$uid] ?? [];
            $all = array_merge($farmasi, $medis);

            $dutyFarmasi = $this->calc->calculateSeconds($farmasi, $windowSeconds);
            $dutyMedis = $this->calc->calculateSeconds($medis, $windowSeconds);
            $dutyTotal = $this->calc->calculateSeconds($all, $windowSeconds);

            $last = null;

            $lf = $lastFarmasi[$uid] ?? null;
            if ($lf) {
                $last = Carbon::parse($lf);
            }

            $nameKey = $normalizeName((string) ($u->full_name ?? ''));
            $le = $lastEmsByName[$nameKey] ?? null;
            if ($le) {
                $ts = Carbon::parse($le);
                if (!$last || $ts->greaterThan($last)) {
                    $last = $ts;
                }
            }

            $lo = $lastOplasCreate[$uid] ?? null;
            if ($lo) {
                $ts = Carbon::parse($lo);
                if (!$last || $ts->greaterThan($last)) {
                    $last = $ts;
                }
            }

            $la = $lastOplasApprove[$uid] ?? null;
            if ($la) {
                $ts = Carbon::parse($la);
                if (!$last || $ts->greaterThan($last)) {
                    $last = $ts;
                }
            }

            $autoOfflineAt = $last ? $last->copy()->addSeconds($windowSeconds) : null;

            $p = $presence->get($uid);
            $presenceOnline = false;
            $presenceAuto = null;
            $presenceConfirm = null;
            $presenceStatus = null;
            if ($p) {
                $presenceStatus = (string) ($p->status ?? '');
                $presenceAuto = !empty($p->auto_offline_at) ? Carbon::parse($p->auto_offline_at) : null;
                $presenceConfirm = !empty($p->last_confirm_at) ? Carbon::parse($p->last_confirm_at) : null;
                $presenceOnline = ($presenceStatus === 'online') && $presenceAuto && $now->lessThanOrEqualTo($presenceAuto);
            }

            $withinDutyWindow = ($autoOfflineAt && $now->lessThanOrEqualTo($autoOfflineAt));

            // Presence is a hint:
            // - If we have a fresh "offline" signal (from close/logout), force offline.
            // - If presence is fresh online, require both presence+window.
            // - If presence is stale/missing, fallback to duty window (so transaksi tetap bikin aktif).
            $forcedOffline = false;
            if ($presenceStatus === 'offline' && $presenceConfirm) {
                $forcedOffline = $presenceConfirm->greaterThanOrEqualTo($now->copy()->subSeconds(120));
            }

            if ($forcedOffline) {
                $status = 'offline';
                $autoOfflineAt = $presenceAuto ?? $presenceConfirm;
            } elseif ($presenceOnline) {
                $status = $withinDutyWindow ? 'active' : 'offline';
            } else {
                $status = $withinDutyWindow ? 'active' : 'offline';
            }

            return [
                'id' => $uid,
                'full_name' => (string) ($u->full_name ?? '-'),
                'position' => (string) ($u->position ?? '-'),
                'role' => (string) ($u->role ?? '-'),
                'is_active' => (bool) ($u->is_active ?? false),
                'status' => $status,
                'last_activity_at' => $last,
                'auto_offline_at' => $autoOfflineAt,
                'duty_seconds_total' => $dutyTotal,
                'duty_seconds_farmasi' => $dutyFarmasi,
                'duty_seconds_medis' => $dutyMedis,
                'trx_count_farmasi' => (int) ($farmasiCounts[$uid] ?? 0),
                'trx_count_medis' => (int) ($medisCounts[$uid] ?? 0),
            ];
        });

        // Sort: active first, then highest duty total.
        $rows = $rows->sortBy(function ($r) {
            return [
                ($r['status'] ?? 'offline') === 'active' ? 0 : 1,
                -((int) ($r['duty_seconds_total'] ?? 0)),
                (string) ($r['full_name'] ?? ''),
            ];
        })->values();

        return [
            'users' => $rows,
            'meta' => [
                'unmapped_ems_names' => $unmappedEms,
            ],
        ];
    }
}
