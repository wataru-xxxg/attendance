<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\RealTimeClock;
use Carbon\Carbon;

class RealTimeClockTest extends TestCase
{
    public function test_realtime_clock_displays_current_time()
    {
        // 現在時刻を固定
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $component = Livewire::test(RealTimeClock::class)
            ->assertSee($now->format('H:i'))
            ->assertSee($now->isoFormat('YYYY年MM月DD日(ddd)'));
    }
}
