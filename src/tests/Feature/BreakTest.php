<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;


class BreakTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_break_button_shows_when_status_is_working()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => Carbon::createFromTime(12, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
    }

    public function test_break_return_button_shows_after_break_start()
    {
        // 出勤状態を作成
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => Carbon::createFromTime(12, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 休憩戻ボタンが表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');
    }

    public function test_break_button_shows_after_break_return()
    {
        // 出勤状態を作成
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => Carbon::createFromTime(12, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 休憩戻ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => Carbon::createFromTime(13, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 休憩入ボタンが再び表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');
    }

    public function test_status_shows_working_after_break_return()
    {
        // 出勤状態を作成
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => Carbon::createFromTime(12, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 休憩戻ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => Carbon::createFromTime(13, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 最終的に出勤中が表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_break_return_button_shows_after_second_break_start()
    {
        // 出勤状態を作成
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);

        // 1回目の休憩入
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => Carbon::createFromTime(12, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 1回目の休憩戻
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => Carbon::createFromTime(13, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 2回目の休憩入
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => Carbon::createFromTime(14, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 最終的に休憩戻ボタンが表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');
    }

    public function test_break_time_is_displayed_in_admin_attendance_list()
    {
        // 出勤状態を作成
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);

        // 休憩入ボタンをクリック
        $breakStartTime = Carbon::createFromTime(12, 0, 0);
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 休憩戻ボタンをクリック
        $breakEndTime = Carbon::createFromTime(13, 0, 0);
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => $breakEndTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 管理者としてログイン
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');

        // 管理者の勤怠一覧画面にアクセス
        $response = $this->get('/admin/attendance/list');
        $response->assertStatus(200);

        $breakTime = $breakEndTime->diffInMinutes($breakStartTime);
        $formattedBreakTime = Carbon::createFromTime(0, 0, 0)->addMinutes($breakTime)->format('H:i');

        // 休憩時間が表示されていることを確認
        $response->assertSee($formattedBreakTime);
    }
}
