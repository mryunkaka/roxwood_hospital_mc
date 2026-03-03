<?php

namespace App\Services;

use Carbon\CarbonInterface;

class DutyTimeCalculator
{
    /**
     * Hitung total durasi duty berdasarkan event timestamps.
     *
     * Setiap event menambah window $windowSeconds (default 5 menit).
     * Jika ada event lagi sebelum window habis, window diperpanjang (union of intervals).
     *
     * @param  array<int, CarbonInterface|string|\DateTimeInterface|int>  $eventTimes
     */
    public function calculateSeconds(array $eventTimes, int $windowSeconds = 300): int
    {
        if ($windowSeconds <= 0 || empty($eventTimes)) {
            return 0;
        }

        $times = [];
        foreach ($eventTimes as $t) {
            if ($t instanceof CarbonInterface) {
                $times[] = $t->getTimestamp();
                continue;
            }
            if ($t instanceof \DateTimeInterface) {
                $times[] = $t->getTimestamp();
                continue;
            }
            if (is_int($t)) {
                $times[] = $t;
                continue;
            }
            if (is_string($t) && $t !== '') {
                try {
                    $times[] = \Carbon\Carbon::parse($t)->getTimestamp();
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }

        if (empty($times)) {
            return 0;
        }

        sort($times);

        $total = 0;
        $start = $times[0];
        $end = $start + $windowSeconds;

        $n = count($times);
        for ($i = 1; $i < $n; $i++) {
            $ts = $times[$i];

            if ($ts <= $end) {
                $candidateEnd = $ts + $windowSeconds;
                if ($candidateEnd > $end) {
                    $end = $candidateEnd;
                }
                continue;
            }

            $total += max(0, $end - $start);
            $start = $ts;
            $end = $ts + $windowSeconds;
        }

        $total += max(0, $end - $start);
        return (int) $total;
    }

    /**
     * Streaming union-of-intervals calculator for already-sorted unix timestamps.
     *
     * @param iterable<int|numeric-string> $unixTimestamps Sorted ascending.
     */
    public function calculateSecondsFromSortedUnixTimestamps(iterable $unixTimestamps, int $windowSeconds = 300, ?int $capUpperUnix = null): int
    {
        if ($windowSeconds <= 0) {
            return 0;
        }

        $total = 0;
        $start = null;
        $end = null;

        foreach ($unixTimestamps as $raw) {
            $ts = (int) $raw;
            if ($ts <= 0) {
                continue;
            }
            if ($capUpperUnix !== null && $ts >= $capUpperUnix) {
                // Sorted input: no more effective time.
                break;
            }

            $intervalEnd = $ts + $windowSeconds;
            if ($capUpperUnix !== null && $intervalEnd > $capUpperUnix) {
                $intervalEnd = $capUpperUnix;
            }
            if ($intervalEnd <= $ts) {
                continue;
            }

            if ($start === null) {
                $start = $ts;
                $end = $intervalEnd;
                continue;
            }

            if ($ts <= $end) {
                if ($intervalEnd > $end) {
                    $end = $intervalEnd;
                }
                continue;
            }

            $total += max(0, $end - $start);
            $start = $ts;
            $end = $intervalEnd;
        }

        if ($start !== null && $end !== null) {
            $total += max(0, $end - $start);
        }

        return (int) $total;
    }
}
