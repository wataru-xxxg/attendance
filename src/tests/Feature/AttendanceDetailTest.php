<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Stamp $startWorkStamp;
    private Stamp $breakStartTimeStamp;
    private Stamp $breakEndTimeStamp;
    private Stamp $endWorkStamp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->startWorkStamp = Stamp::factory()->create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);
        $this->breakStartTimeStamp = Stamp::factory()->create([
            'user_id' => $this->user->id,
            'stamp_type' => '休憩入',
            'stamped_at' => Carbon::createFromTime(12, 0, 0),
        ]);
        $this->breakEndTimeStamp = Stamp::factory()->create([
            'user_id' => $this->user->id,
            'stamp_type' => '休憩戻',
            'stamped_at' => Carbon::createFromTime(13, 0, 0),
        ]);
        $this->endWorkStamp = Stamp::factory()->create([
            'user_id' => $this->user->id,
            'stamp_type' => '退勤',
            'stamped_at' => Carbon::createFromTime(18, 0, 0),
        ]);
    }

    public function test_user_name_is_displayed_correctly_for_normal_user()
    {
        $response = $this->get(route('attendance.detail', ['id' => now()->format('Ymd')]));
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
    }

    public function test_selected_date_is_displayed_correctly()
    {
        $date = Carbon::now();
        $response = $this->get(route('attendance.detail', ['id' => $date->format('Ymd')]));

        $response->assertStatus(200);
        $response->assertSee($date->format('Y年'));
        $response->assertSee($date->format('n月d日'));
    }

    public function test_work_times_are_displayed_correctly()
    {
        $date = Carbon::now();
        $response = $this->get(route('attendance.detail', ['id' => $date->format('Ymd')]));

        $response->assertStatus(200);
        $response->assertSee($this->startWorkStamp->stamped_at->format('H:i'));
        $response->assertSee($this->endWorkStamp->stamped_at->format('H:i'));
    }

    public function test_break_times_are_displayed_correctly()
    {
        $date = Carbon::now();
        $response = $this->get(route('attendance.detail', ['id' => $date->format('Ymd')]));

        $response->assertStatus(200);
        $response->assertSee($this->breakStartTimeStamp->stamped_at->format('H:i'));
        $response->assertSee($this->breakEndTimeStamp->stamped_at->format('H:i'));
    }
}
