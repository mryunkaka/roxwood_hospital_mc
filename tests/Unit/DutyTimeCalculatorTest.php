<?php

namespace Tests\Unit;

use App\Services\DutyTimeCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DutyTimeCalculatorTest extends TestCase
{
    public function test_empty_returns_zero(): void
    {
        $calc = new DutyTimeCalculator();
        $this->assertSame(0, $calc->calculateSeconds([]));
    }

    public function test_single_event_adds_window(): void
    {
        $calc = new DutyTimeCalculator();
        $t0 = Carbon::parse('2026-03-03 09:39:00');
        $this->assertSame(300, $calc->calculateSeconds([$t0]));
    }

    public function test_overlapping_events_extend_window(): void
    {
        $calc = new DutyTimeCalculator();
        $t0 = Carbon::parse('2026-03-03 09:39:00');
        $t1 = Carbon::parse('2026-03-03 09:42:00'); // within 5 minutes
        // Expected: 09:39 -> 09:47 (8 minutes) = 480 seconds
        $this->assertSame(480, $calc->calculateSeconds([$t0, $t1]));
    }

    public function test_gap_creates_new_block(): void
    {
        $calc = new DutyTimeCalculator();
        $t0 = Carbon::parse('2026-03-03 09:39:00');
        $t1 = Carbon::parse('2026-03-03 09:45:01'); // gap > 5 minutes from 09:39
        $this->assertSame(600, $calc->calculateSeconds([$t0, $t1]));
    }
}

