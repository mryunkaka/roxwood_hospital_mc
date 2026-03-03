<?php

namespace App\Http\Controllers;

use App\Services\DutyMonitoringService;
use App\Services\DutyUserStatsService;
use App\Models\UserRh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DutyMonitoringController extends Controller
{
    private function resolveRange(Request $request): array
    {
        $validRanges = ['week1', 'week2', 'week3', 'week4', 'custom'];
        $range = (string) $request->query('range', 'week4');
        if (!in_array($range, $validRanges, true)) {
            $range = 'week4';
        }

        $now = Carbon::now();
        $mondayThisWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weeks = [
            'week1' => [
                'start' => $mondayThisWeek->copy()->subWeeks(3)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(3)->addDays(6)->endOfDay(),
                'label' => __('messages.range_3_weeks_ago'),
            ],
            'week2' => [
                'start' => $mondayThisWeek->copy()->subWeeks(2)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(2)->addDays(6)->endOfDay(),
                'label' => __('messages.range_2_weeks_ago'),
            ],
            'week3' => [
                'start' => $mondayThisWeek->copy()->subWeeks(1)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(1)->addDays(6)->endOfDay(),
                'label' => __('messages.range_last_week'),
            ],
            'week4' => [
                'start' => $mondayThisWeek->copy()->startOfDay(),
                'end' => $mondayThisWeek->copy()->addDays(6)->endOfDay(),
                'label' => __('messages.range_this_week'),
            ],
        ];

        $fromInput = '';
        $toInput = '';

        switch ($range) {
            case 'custom':
                $from = (string) $request->query('from', '');
                $to = (string) $request->query('to', '');
                $fromInput = $from;
                $toInput = $to;
                if ($from !== '' && $to !== '') {
                    $start = Carbon::parse($from)->startOfDay();
                    $end = Carbon::parse($to)->endOfDay();
                } else {
                    $start = $weeks['week4']['start'];
                    $end = $weeks['week4']['end'];
                }
                break;
            case 'week1':
            case 'week2':
            case 'week3':
            case 'week4':
            default:
                $start = $weeks[$range]['start'];
                $end = $weeks[$range]['end'];
                break;
        }

        $rangeLabel = $start->copy()->locale(app()->getLocale())->translatedFormat('d M Y') . ' – ' .
            $end->copy()->locale(app()->getLocale())->translatedFormat('d M Y');

        return [$range, $start, $end, (string) $rangeLabel, $weeks, $fromInput, $toInput];
    }

    public function index(Request $request, DutyMonitoringService $svc)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        if ($role === 'staff') {
            abort(403);
        }

        [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput] = $this->resolveRange($request);

        // Jangan hitung waktu "masa depan" (mis. week4 sampai Minggu), cap ke sekarang.
        $now = now();
        $endEffective = $end->copy();
        if ($endEffective->greaterThan($now)) {
            $endEffective = $now->copy();
        }

        $data = $svc->build($start, $endEffective, 300);

        $summary = [
            'total_users' => (int) ($data['users']?->count() ?? 0),
            'active_users' => (int) ($data['users']?->where('status', 'active')->count() ?? 0),
            'total_duty_seconds' => (int) ($data['users']?->sum('duty_seconds_total') ?? 0),
            'total_trx_farmasi' => (int) ($data['users']?->sum('trx_count_farmasi') ?? 0),
            'total_trx_medis' => (int) ($data['users']?->sum('trx_count_medis') ?? 0),
        ];

        $me = null;
        $userId = (int) session('user.id', 0);
        $userName = (string) session('user.name', '');

        if ($userId > 0 && $userName !== '') {
            $meRow = $data['users']?->firstWhere('id', $userId);
            $meRowArr = is_array($meRow) ? $meRow : [];

            /** @var DutyUserStatsService $userStats */
            $userStats = app(DutyUserStatsService::class);

            // Duty sejak daftar (tanggal_masuk/created_at user_rh) sampai sekarang.
            $join = null;
            try {
                $u = UserRh::query()
                    ->select(['tanggal_masuk', 'created_at'])
                    ->where('id', $userId)
                    ->first();
                if ($u) {
                    if (!empty($u->tanggal_masuk)) {
                        $join = Carbon::parse((string) $u->tanggal_masuk)->startOfDay();
                    } elseif (!empty($u->created_at)) {
                        $join = Carbon::parse((string) $u->created_at)->startOfDay();
                    }
                }
            } catch (\Throwable $e) {
                $join = null;
            }
            $join ??= $now->copy()->subYear(); // fallback: 1 tahun terakhir (hindari query sangat berat)

            $all = $userStats->statsForUser($userId, $userName, $join, $now, 300, $now);
            $period = $userStats->statsForUser($userId, $userName, $start, $endEffective, 300, $endEffective);

            $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $week = $userStats->statsForUser($userId, $userName, $weekStart, $now, 300, $now);

            $me = [
                'id' => $userId,
                'name' => $userName,
                'position' => (string) session('user.position', '-'),
                'status' => (string) ($meRowArr['status'] ?? 'offline'),
                'last_activity_at' => $meRowArr['last_activity_at'] ?? null,
                'auto_offline_at' => $meRowArr['auto_offline_at'] ?? null,
                'duty_seconds_all' => (int) ($all['duty_seconds'] ?? 0),
                'trx_count_all' => (int) ($all['trx_count'] ?? 0),
                'duty_seconds_period' => (int) ($period['duty_seconds'] ?? 0),
                'trx_count_period' => (int) ($period['trx_count'] ?? 0),
                'duty_seconds_week_elapsed' => (int) ($week['duty_seconds'] ?? 0),
                'server_now_ms' => (int) ($now->timestamp * 1000),
                'auto_offline_ms' => !empty($meRowArr['auto_offline_at'])
                    ? (int) (($meRowArr['auto_offline_at'] instanceof \DateTimeInterface
                        ? $meRowArr['auto_offline_at']->getTimestamp()
                        : Carbon::parse((string) $meRowArr['auto_offline_at'])->timestamp) * 1000)
                    : null,
            ];
        }

        return view('pages.duty.monitor', [
            'range' => $range,
            'start' => $start,
            'end' => $endEffective,
            'rangeLabel' => $rangeLabel,
            'weeks' => $weeks,
            'fromInput' => $fromInput,
            'toInput' => $toInput,
            'rows' => $data['users'],
            'meta' => $data['meta'],
            'summary' => $summary,
            'windowSeconds' => 300,
            'me' => $me,
        ]);
    }
}
