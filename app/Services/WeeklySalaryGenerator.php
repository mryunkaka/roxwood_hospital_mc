<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WeeklySalaryGenerator
{
    /**
     * Backfill salary per minggu (Senin - Minggu) dari tanggal sales pertama sampai minggu berjalan.
     * Idempotent: skip jika salary periode sudah ada.
     */
    public function backfill(?Carbon $now = null): array
    {
        $now = ($now ?? Carbon::now())->copy();
        $mondayThisWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();

        $firstSaleDate = Sale::query()
            ->selectRaw('MIN(DATE(created_at)) AS d')
            ->value('d');

        if (!$firstSaleDate) {
            return [
                'ok' => true,
                'message' => 'Tidak ada data sales sama sekali',
                'created_periods' => 0,
                'skipped_periods' => 0,
            ];
        }

        $cursor = Carbon::parse((string) $firstSaleDate)->startOfWeek(Carbon::MONDAY)->startOfDay();

        $created = 0;
        $skipped = 0;

        while ($cursor->lte($mondayThisWeek)) {
            $result = $this->generateForWeekStarting($cursor, $now, true);
            if (($result['status'] ?? '') === 'created') {
                $created++;
            } else {
                $skipped++;
            }

            $cursor->addWeek();
        }

        return [
            'ok' => true,
            'message' => 'Selesai backfill salary',
            'created_periods' => $created,
            'skipped_periods' => $skipped,
        ];
    }

    /**
     * Generate salary untuk periode Senin - Minggu dari $monday.
     * - Skip jika minggu belum selesai.
     * - Skip jika periode sudah ada (jika $skipIfExists = true).
     */
    public function generateForWeekStarting(Carbon $monday, ?Carbon $now = null, bool $skipIfExists = true): array
    {
        $now = ($now ?? Carbon::now())->copy();
        $start = $monday->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = $start->copy()->addDays(6)->endOfDay();

        $periodStart = $start->toDateString();
        $periodEnd = $end->toDateString();

        if ($end->greaterThanOrEqualTo($now)) {
            return [
                'status' => 'skipped',
                'reason' => 'minggu_belum_selesai',
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ];
        }

        if ($skipIfExists) {
            $exists = Salary::query()
                ->where('period_start', $periodStart)
                ->where('period_end', $periodEnd)
                ->exists();

            if ($exists) {
                return [
                    'status' => 'skipped',
                    'reason' => 'sudah_ada',
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                ];
            }
        }

        $rows = Sale::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('medic_name')
            ->selectRaw('MAX(medic_jabatan) AS medic_jabatan')
            ->selectRaw('COUNT(*) AS total_transaksi')
            ->selectRaw('COALESCE(SUM(qty_bandage + qty_ifaks + qty_painkiller), 0) AS total_item')
            ->selectRaw('COALESCE(SUM(price), 0) AS total_rupiah')
            ->groupBy('medic_name')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'status' => 'skipped',
                'reason' => 'tidak_ada_sales',
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ];
        }

        DB::transaction(function () use ($rows, $periodStart, $periodEnd) {
            foreach ($rows as $r) {
                $totalRupiah = (int) ($r->total_rupiah ?? 0);

                $payload = [
                    'medic_name' => (string) ($r->medic_name ?? '-'),
                    'medic_jabatan' => (string) ($r->medic_jabatan ?? null),
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'total_transaksi' => (int) ($r->total_transaksi ?? 0),
                    'total_item' => (int) ($r->total_item ?? 0),
                    'total_rupiah' => $totalRupiah,
                    'bonus_40' => (int) floor($totalRupiah * 0.4),
                    'status' => 'pending',
                    'paid_at' => null,
                    'paid_by' => null,
                    'created_at' => now(),
                ];

                Salary::query()->updateOrInsert(
                    [
                        'medic_name' => $payload['medic_name'],
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                    ],
                    $payload
                );
            }
        });

        Cache::forget('salary_generated:' . $periodEnd);

        return [
            'status' => 'created',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'rows' => $rows->count(),
        ];
    }
}

