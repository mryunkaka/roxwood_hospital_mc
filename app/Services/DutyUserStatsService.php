<?php

namespace App\Services;

use App\Models\EmsSale;
use App\Models\MedicOperasiPlastik;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DutyUserStatsService
{
    public function __construct(
        private readonly DutyTimeCalculator $calc,
    ) {}

    /**
     * Hitung duty seconds (elapsed) untuk 1 user pada periode tertentu.
     *
     * - Menggunakan union event timestamps dari tabel transaksi.
     * - `capUpper` dipakai untuk mencegah menghitung waktu "masa depan" (mis. endOfWeek > now()).
     *
     * @return array{duty_seconds:int, trx_count:int}
     */
    public function statsForUser(int $userId, string $fullName, Carbon $start, Carbon $end, int $windowSeconds = 300, ?Carbon $capUpper = null): array
    {
        $start = $start->copy();
        $end = $end->copy();

        $capUnix = $capUpper ? (int) $capUpper->timestamp : null;
        if ($capUnix !== null && $end->timestamp > $capUnix) {
            $end = $capUpper->copy();
        }

        if ($end->lessThanOrEqualTo($start)) {
            return ['duty_seconds' => 0, 'trx_count' => 0];
        }

        $nameKey = mb_strtolower(trim(preg_replace('/\\s+/', ' ', $fullName)));

        $trxFarmasi = (int) Sale::query()
            ->where('medic_user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $trxEms = (int) EmsSale::query()
            ->whereRaw('LOWER(TRIM(medic_name)) = ?', [$nameKey])
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $trxOplasCreate = (int) MedicOperasiPlastik::query()
            ->where('id_user', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $trxOplasApprove = (int) MedicOperasiPlastik::query()
            ->whereNotNull('approved_at')
            ->where('approved_by', $userId)
            ->whereBetween('approved_at', [$start, $end])
            ->count();

        $trxCount = $trxFarmasi + $trxEms + $trxOplasCreate + $trxOplasApprove;

        $base = Sale::query()
            ->where('medic_user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('created_at AS ts')
            ->toBase();

        $ems = EmsSale::query()
            ->whereRaw('LOWER(TRIM(medic_name)) = ?', [$nameKey])
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('created_at AS ts')
            ->toBase();

        $opCreate = MedicOperasiPlastik::query()
            ->where('id_user', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('created_at AS ts')
            ->toBase();

        $opApprove = MedicOperasiPlastik::query()
            ->whereNotNull('approved_at')
            ->where('approved_by', $userId)
            ->whereBetween('approved_at', [$start, $end])
            ->selectRaw('approved_at AS ts')
            ->toBase();

        $union = $base
            ->unionAll($ems)
            ->unionAll($opCreate)
            ->unionAll($opApprove);

        $ordered = DB::query()
            ->fromSub($union, 'u')
            ->select(['ts'])
            ->orderBy('ts');

        $total = 0;
        $blockStart = null;
        $blockEnd = null;
        $stop = false;

        $ordered->chunk(2000, function ($rows) use (&$total, &$blockStart, &$blockEnd, &$stop, $windowSeconds, $capUnix) {
            foreach ($rows as $r) {
                if ($stop) {
                    return false;
                }

                $raw = $r->ts ?? null;
                if (!$raw) {
                    continue;
                }

                try {
                    $ts = Carbon::parse($raw)->timestamp;
                } catch (\Throwable $e) {
                    continue;
                }

                if ($ts <= 0) {
                    continue;
                }

                if ($capUnix !== null && $ts >= $capUnix) {
                    $stop = true;
                    return false;
                }

                $intervalEnd = $ts + $windowSeconds;
                if ($capUnix !== null && $intervalEnd > $capUnix) {
                    $intervalEnd = $capUnix;
                }
                if ($intervalEnd <= $ts) {
                    continue;
                }

                if ($blockStart === null) {
                    $blockStart = $ts;
                    $blockEnd = $intervalEnd;
                    continue;
                }

                if ($ts <= $blockEnd) {
                    if ($intervalEnd > $blockEnd) {
                        $blockEnd = $intervalEnd;
                    }
                    continue;
                }

                $total += max(0, $blockEnd - $blockStart);
                $blockStart = $ts;
                $blockEnd = $intervalEnd;
            }

            return true;
        });

        if ($blockStart !== null && $blockEnd !== null) {
            $total += max(0, $blockEnd - $blockStart);
        }

        return [
            'duty_seconds' => (int) $total,
            'trx_count' => (int) $trxCount,
        ];
    }
}
