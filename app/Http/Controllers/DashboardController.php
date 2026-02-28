<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\EmsSale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard page
     */
    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $validRanges = ['today', 'yesterday', 'last7', 'week1', 'week2', 'week3', 'week4', 'custom'];
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
            ],
            'week2' => [
                'start' => $mondayThisWeek->copy()->subWeeks(2)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(2)->addDays(6)->endOfDay(),
            ],
            'week3' => [
                'start' => $mondayThisWeek->copy()->subWeeks(1)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(1)->addDays(6)->endOfDay(),
            ],
            'week4' => [
                'start' => $mondayThisWeek->copy()->startOfDay(),
                'end' => $mondayThisWeek->copy()->addDays(6)->endOfDay(),
            ],
        ];

        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                break;
            case 'last7':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'custom':
                $from = (string) $request->query('from', '');
                $to = (string) $request->query('to', '');
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

        $rangeLabel = $start
            ->copy()
            ->locale(app()->getLocale())
            ->translatedFormat('d M Y')
            . ' – ' .
            $end->copy()->locale(app()->getLocale())->translatedFormat('d M Y');

        $salesBase = Sale::query()->whereBetween('created_at', [$start, $end]);
        $farmasi = $salesBase->clone()
            ->selectRaw('COUNT(DISTINCT medic_name) AS total_medic')
            ->selectRaw('COUNT(DISTINCT TRIM(UPPER(consumer_name))) AS total_consumer')
            ->selectRaw('COUNT(id) AS total_transaksi')
            ->selectRaw('COALESCE(SUM(qty_bandage + qty_ifaks + qty_painkiller), 0) AS total_item')
            ->selectRaw('COALESCE(SUM(qty_bandage), 0) AS total_bandage')
            ->selectRaw('COALESCE(SUM(qty_painkiller), 0) AS total_painkiller')
            ->selectRaw('COALESCE(SUM(qty_ifaks), 0) AS total_ifaks')
            ->selectRaw("COALESCE(SUM(CASE WHEN package_name = 'Paket A' THEN 1 ELSE 0 END), 0) AS total_paket_a")
            ->selectRaw("COALESCE(SUM(CASE WHEN package_name = 'Paket B' THEN 1 ELSE 0 END), 0) AS total_paket_b")
            ->selectRaw('COALESCE(SUM(price), 0) AS total_income')
            ->first();

        $emsBase = EmsSale::query()->whereBetween('created_at', [$start, $end]);
        $rekapMedis = $emsBase->clone()
            ->selectRaw("COALESCE(SUM(UPPER(medicine_usage) LIKE '%P3K%'), 0) AS total_p3k")
            ->selectRaw("COALESCE(SUM(UPPER(medicine_usage) LIKE '%BANDAGE%'), 0) AS total_bandage")
            ->selectRaw("COALESCE(SUM(UPPER(medicine_usage) LIKE '%GAUZE%'), 0) AS total_gauze")
            ->selectRaw("COALESCE(SUM(UPPER(medicine_usage) LIKE '%IODINE%'), 0) AS total_iodine")
            ->selectRaw("COALESCE(SUM(UPPER(medicine_usage) LIKE '%SYRINGE%'), 0) AS total_syringe")
            ->selectRaw("COALESCE(SUM(operasi_tingkat = 'plastik'), 0) AS operasi_plastik")
            ->selectRaw("COALESCE(SUM(operasi_tingkat = 'ringan'), 0) AS operasi_ringan")
            ->selectRaw("COALESCE(SUM(operasi_tingkat = 'berat'), 0) AS operasi_berat")
            ->first();

        $dashboard = [
            'range' => $range,
            'range_label' => $rangeLabel,
            'range_start' => $start->toIso8601String(),
            'range_end' => $end->toIso8601String(),

            // Farmasi
            'total_medic' => (int) ($farmasi->total_medic ?? 0),
            'total_consumer' => (int) ($farmasi->total_consumer ?? 0),
            'total_paket_a' => (int) ($farmasi->total_paket_a ?? 0),
            'total_paket_b' => (int) ($farmasi->total_paket_b ?? 0),
            'total_bandage' => (int) ($farmasi->total_bandage ?? 0),
            'total_painkiller' => (int) ($farmasi->total_painkiller ?? 0),
            'total_ifaks' => (int) ($farmasi->total_ifaks ?? 0),
            'total_transaksi' => (int) ($farmasi->total_transaksi ?? 0),
            'total_item' => (int) ($farmasi->total_item ?? 0),
            'total_income' => (int) ($farmasi->total_income ?? 0),
            'total_bonus' => (int) round(((int) ($farmasi->total_income ?? 0)) * 0.4),
            'company_profit' => (int) round(((int) ($farmasi->total_income ?? 0)) * 0.6),

            // Medis
            'rekap_medis' => [
                'p3k' => (int) ($rekapMedis->total_p3k ?? 0),
                'bandage' => (int) ($rekapMedis->total_bandage ?? 0),
                'gauze' => (int) ($rekapMedis->total_gauze ?? 0),
                'iodine' => (int) ($rekapMedis->total_iodine ?? 0),
                'syringe' => (int) ($rekapMedis->total_syringe ?? 0),
                'operasi_plastik' => (int) ($rekapMedis->operasi_plastik ?? 0),
                'operasi_ringan' => (int) ($rekapMedis->operasi_ringan ?? 0),
                'operasi_berat' => (int) ($rekapMedis->operasi_berat ?? 0),
            ],

            // Charts + winners
            'chart_weekly' => [
                'labels' => [],
                'values' => [],
            ],
            'weekly_winner' => [],
        ];

        foreach ($weeks as $w) {
            $label = $w['start']->copy()->locale(app()->getLocale())->translatedFormat('d M y')
                . ' - ' .
                $w['end']->copy()->locale(app()->getLocale())->translatedFormat('d M y');

            $weeklyIncome = Sale::query()
                ->whereBetween('created_at', [$w['start'], $w['end']])
                ->sum('price');

            $winnerRow = Sale::query()
                ->select('medic_name', DB::raw('SUM(price) AS total'))
                ->whereBetween('created_at', [$w['start'], $w['end']])
                ->groupBy('medic_name')
                ->orderByDesc('total')
                ->first();

            $winnerTotal = (int) ($winnerRow->total ?? 0);
            $dashboard['weekly_winner'][$label] = [
                'medic' => (string) ($winnerRow->medic_name ?? '-'),
                'bonus_40' => (int) round($winnerTotal * 0.4),
            ];

            $dashboard['chart_weekly']['labels'][] = $label;
            $dashboard['chart_weekly']['values'][] = (int) $weeklyIncome;
        }

        return view('pages.dashboard', [
            'dashboard' => $dashboard,
            'rangeLabel' => $rangeLabel,
        ]);
    }
}
