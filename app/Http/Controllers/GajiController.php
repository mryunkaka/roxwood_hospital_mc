<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Sale;
use App\Models\UserRh;
use App\Services\WeeklySalaryGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GajiController extends Controller
{
    private function resolveRange(Request $request): array
    {
        $validRanges = ['week1', 'week2', 'week3', 'week4', 'custom', 'all'];
        $range = (string) $request->query('range', 'week3');
        if (!in_array($range, $validRanges, true)) {
            $range = 'week3';
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
        $start = null;
        $end = null;

        switch ($range) {
            case 'all':
                break;
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

        $rangeLabel = $range === 'all'
            ? __('messages.range_all')
            : ($start?->copy()->locale(app()->getLocale())->translatedFormat('d M Y') . ' – ' . $end?->copy()->locale(app()->getLocale())->translatedFormat('d M Y'));

        return [$range, $start, $end, (string) $rangeLabel, $weeks, $fromInput, $toInput];
    }

    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        $userRole = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $isStaff = $userRole === 'staff';
        $userName = (string) ($user['name'] ?? '');

        [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput] = $this->resolveRange($request);

        $rekap = [
            'total_medis' => 0,
            'total_transaksi' => 0,
            'total_item' => 0,
            'total_rupiah' => 0,
            'total_bonus' => 0,
            'paid_bonus' => 0,
            'sisa_bonus' => 0,
        ];

        $salaryQuery = Salary::query();
        if ($isStaff) {
            $salaryQuery->where('medic_name', $userName);
        } elseif ($range !== 'all' && $start && $end) {
            $salaryQuery->whereBetween('period_end', [$start->toDateString(), $end->toDateString()]);
        }

        $salary = $salaryQuery
            ->orderByDesc('period_end')
            ->limit($isStaff ? 500 : 800)
            ->get();

        if (!$isStaff && $range !== 'all' && $start && $end) {
            $rekapRow = Salary::query()
                ->whereBetween('period_end', [$start->toDateString(), $end->toDateString()])
                ->selectRaw('COUNT(DISTINCT medic_name) AS total_medis')
                ->selectRaw('COALESCE(SUM(total_transaksi), 0) AS total_transaksi')
                ->selectRaw('COALESCE(SUM(total_item), 0) AS total_item')
                ->selectRaw('COALESCE(SUM(total_rupiah), 0) AS total_rupiah')
                ->selectRaw('COALESCE(SUM(bonus_40), 0) AS total_bonus')
                ->first();

            $paidBonus = (int) (Salary::query()
                ->whereBetween('period_end', [$start->toDateString(), $end->toDateString()])
                ->where('status', 'paid')
                ->sum('bonus_40'));

            $totalBonus = (int) ($rekapRow?->total_bonus ?? 0);
            $rekap = [
                'total_medis' => (int) ($rekapRow?->total_medis ?? 0),
                'total_transaksi' => (int) ($rekapRow?->total_transaksi ?? 0),
                'total_item' => (int) ($rekapRow?->total_item ?? 0),
                'total_rupiah' => (int) ($rekapRow?->total_rupiah ?? 0),
                'total_bonus' => $totalBonus,
                'paid_bonus' => $paidBonus,
                'sisa_bonus' => max(0, $totalBonus - $paidBonus),
            ];
        }

        $allowedGenerateRoles = ['vice director', 'director'];
        $canGenerateManual = in_array($userRole, $allowedGenerateRoles, true);

        return view('pages.farmasi.gaji.index', [
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'weeks' => $weeks,
            'fromInput' => $fromInput,
            'toInput' => $toInput,
            'isStaff' => $isStaff,
            'userRole' => $userRole,
            'userName' => $userName,
            'rekap' => $rekap,
            'salary' => $salary,
            'canGenerateManual' => $canGenerateManual,
        ]);
    }

    public function pay(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        if (empty($user['id'])) {
            return response()->json(['success' => false, 'message' => __('messages.session_invalid_message')], 401);
        }

        $validated = $request->validate([
            'salary_id' => ['required', 'integer', 'min:1'],
            'pay_method' => ['required', 'in:direct,titip'],
            'titip_to' => ['nullable', 'integer', 'min:1'],
        ]);

        $salary = Salary::query()->where('id', (int) $validated['salary_id'])->first();
        if (!$salary) {
            return response()->json(['success' => false, 'message' => __('messages.no_data')], 404);
        }

        if ((string) ($salary->status ?? 'pending') === 'paid') {
            return response()->json(['success' => true, 'message' => __('messages.salary_already_paid')]);
        }

        $paidBy = (string) ($user['name'] ?? '-');

        if ($validated['pay_method'] === 'titip') {
            $titipId = (int) ($validated['titip_to'] ?? 0);
            if ($titipId <= 0) {
                return response()->json(['success' => false, 'message' => __('messages.salary_titip_required')], 422);
            }

            $titipName = (string) (UserRh::query()->where('id', $titipId)->value('full_name') ?? '');
            if ($titipName === '') {
                return response()->json(['success' => false, 'message' => __('messages.salary_titip_required')], 422);
            }

            $paidBy = __('messages.salary_titip_to_prefix') . ' ' . $titipName;
        }

        Salary::query()
            ->where('id', (int) $salary->id)
            ->update([
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by' => $paidBy,
            ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.salary_paid_success'),
            'row' => [
                'id' => (int) $salary->id,
                'status' => 'paid',
                'paid_by' => $paidBy,
                'paid_at' => now()->toIso8601String(),
                'paid_at_text' => now()->locale(app()->getLocale())->translatedFormat('d M Y H:i'),
            ],
        ]);
    }

    public function generateManual(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        if (!in_array($role, ['vice director', 'director'], true)) {
            abort(403);
        }

        $tz = config('app.timezone', 'Asia/Jakarta');
        $now = Carbon::now($tz);
        $mondayLastWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->subWeek();

        /** @var WeeklySalaryGenerator $gen */
        $gen = app(WeeklySalaryGenerator::class);
        $r = $gen->generateForWeekStarting($mondayLastWeek, $now, false);

        if (($r['status'] ?? '') !== 'created') {
            return redirect()->route('farmasi.gaji', ['range' => 'week3', 'msg' => 'nosales']);
        }

        $periodEnd = (string) ($r['period_end'] ?? $mondayLastWeek->copy()->addDays(6)->toDateString());

        return redirect()->route('farmasi.gaji', [
            'range' => 'week3',
            'generated' => $periodEnd,
        ]);
    }
}
