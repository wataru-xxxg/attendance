<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AdminUser;


class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private $date;
    private AdminUser $admin;
    private User $user;
    private Stamp $startWorkStamp;
    private Stamp $breakStartTimeStamp;
    private Stamp $breakEndTimeStamp;
    private Stamp $endWorkStamp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->date = Carbon::now();
        $this->admin = AdminUser::factory()->create();
        $this->user = User::factory()->create();
        $this->actingAs($this->admin, 'admin');

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

    public function test_admin_can_view_attendance_detail()
    {
        // 勤怠詳細ページにアクセス
        $response = $this->get(route('attendance.detail', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]));

        // レスポンスの検証
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->date->format('Y年'));
        $response->assertSee($this->date->format('n月d日'));
        $response->assertSee($this->startWorkStamp->stamped_at->format('H:i'));
        $response->assertSee($this->endWorkStamp->stamped_at->format('H:i'));
        $response->assertSee($this->breakStartTimeStamp->stamped_at->format('H:i'));
        $response->assertSee($this->breakEndTimeStamp->stamped_at->format('H:i'));
    }

    public function test_error_message_is_displayed_when_start_time_is_after_end_time()
    {
        $response = $this->post(route('attendance.correct', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]), [
            'startWork' => '18:00',
            'endWork' => '09:00',
            'notes' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors('startWork');
        $response->assertSessionHasErrors('endWork');
        $response->assertStatus(302);

        // エラーメッセージが表示されていることを確認
        $response = $this->get(route('attendance.detail', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]));
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function test_error_message_is_displayed_when_break_start_time_is_after_end_work_time()
    {
        $response = $this->post(route('attendance.correct', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['19:00'],
            'breakEnd' => ['20:00'],
            'notes' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors('breakStart.0');
        $response->assertSessionHasErrors('breakEnd.0');
        $response->assertStatus(302);

        // エラーメッセージが表示されていることを確認
        $response = $this->get(route('attendance.detail', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]));
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function test_error_message_is_displayed_when_break_end_time_is_after_end_work_time()
    {
        $response = $this->post(route('attendance.correct', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['12:00'],
            'breakEnd' => ['19:00'],
            'notes' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors('breakEnd.0');
        $response->assertStatus(302);

        // エラーメッセージが表示されていることを確認
        $response = $this->get(route('attendance.detail', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]));
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function test_error_message_is_displayed_when_notes_is_empty()
    {
        $response = $this->post(route('attendance.correct', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['12:00'],
            'breakEnd' => ['13:00'],
            'notes' => ''
        ]);

        $response->assertSessionHasErrors('notes');
        $response->assertStatus(302);

        // エラーメッセージが表示されていることを確認
        $response = $this->get(route('attendance.detail', [
            'id' => $this->date->format('Ymd'),
            'userId' => $this->user->id
        ]));
        $response->assertSee('備考を記入してください');
    }
}
