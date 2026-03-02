<?php

use App\Services\WeeklySalaryGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('salary:generate-weekly {--backfill : Backfill dari sales pertama}', function () {
    $tz = config('app.timezone', 'Asia/Jakarta');
    $now = Carbon::now($tz);

    /** @var WeeklySalaryGenerator $gen */
    $gen = app(WeeklySalaryGenerator::class);

    if ($this->option('backfill')) {
        $this->info('Mulai backfill salary mingguan (Senin - Minggu)...');
        $res = $gen->backfill($now);
        $this->line(sprintf(
            'Selesai. created=%d skipped=%d',
            (int) ($res['created_periods'] ?? 0),
            (int) ($res['skipped_periods'] ?? 0),
        ));
        return 0;
    }

    $mondayLastWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->subWeek();
    $r = $gen->generateForWeekStarting($mondayLastWeek, $now, true);

    if (($r['status'] ?? '') === 'created') {
        $this->info('CREATED ' . $r['period_start'] . ' - ' . $r['period_end']);
        return 0;
    }

    $this->line('SKIP ' . ($r['period_start'] ?? '-') . ' - ' . ($r['period_end'] ?? '-') . ' (' . ($r['reason'] ?? 'unknown') . ')');
    return 0;
})->purpose('Generate gaji mingguan (salary) dari data sales');

// Auto-generate tiap Senin dini hari setelah minggu selesai (Mon-Sun).
Schedule::command('salary:generate-weekly --backfill')
    ->weeklyOn(1, '00:15')
    ->timezone(config('app.timezone', 'Asia/Jakarta'))
    ->withoutOverlapping()
    ->runInBackground();

