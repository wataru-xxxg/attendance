<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AttendanceViewTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_status_badge_shows_working_when_clocked_in()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => now(),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_status_badge_shows_clocked_out_when_clocked_out_today()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '退勤',
            'stamped_at' => now(),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }

    public function test_status_badge_shows_working_outside_when_clocked_out_yesterday()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '退勤',
            'stamped_at' => now()->subDay(),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('勤務外');
    }

    public function test_status_badge_shows_break_when_on_break()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '休憩入',
            'stamped_at' => now(),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    public function test_status_badge_shows_working_after_break_return()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '休憩戻',
            'stamped_at' => now(),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_status_badge_shows_working_outside_when_no_stamps()
    {
        $response = $this->get('/attendance');
        $response->assertSee('勤務外');
    }
}
