# Scheduler (Auto Generate Gaji)

Project ini memakai **Laravel Scheduler** untuk menjalankan job otomatis.

## Command

- Generate / backfill gaji mingguan:
  - `php artisan salary:generate-weekly --backfill`

Periode yang dibuat: **Senin – Minggu** (akan **skip** minggu yang belum selesai, dan **skip** periode yang sudah ada).

## Jadwal Otomatis

Jadwal ada di `routes/console.php`:

- `salary:generate-weekly --backfill` → **setiap Senin 00:15** (timezone mengikuti `APP_TIMEZONE`, default `Asia/Jakarta`).

## Mengaktifkan Scheduler di Server

Laravel scheduler perlu dipanggil oleh cron / task scheduler tiap menit:

- Linux (crontab):
  - `* * * * * cd /path/to/roxwood_hospital_mc && php artisan schedule:run >> /dev/null 2>&1`

- Windows (Task Scheduler):
  - Buat task yang menjalankan `php artisan schedule:run` setiap 1 menit di folder project.

