<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;


class testBreakTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private $startWorkTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->startWorkTime = Carbon::createFromTime(9, 0, 0);
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => $this->startWorkTime,
        ]);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '出勤')->update([
            'created_at' => $this->startWorkTime,
            'updated_at' => $this->startWorkTime,
        ]);
    }

    public function test_break_button_shows_when_status_is_working()
    {
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $breakStartTime = (clone $this->startWorkTime)->addHours(3);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩入')->update([
            'created_at' => $breakStartTime,
            'updated_at' => $breakStartTime,
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');
    }

    public function test_break_return_button_shows_after_break_start()
    {
        $breakStartTime = (clone $this->startWorkTime)->addHours(3);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩入')->update([
            'created_at' => $breakStartTime,
            'updated_at' => $breakStartTime,
        ]);

        // 休憩戻ボタンが表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');
    }

    public function test_break_button_shows_after_break_return()
    {
        $breakStartTime = (clone $this->startWorkTime)->addHours(3);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩入')->update([
            'created_at' => $breakStartTime,
            'updated_at' => $breakStartTime,
        ]);

        $breakEndTime = (clone $breakStartTime)->addHours(1);

        // 休憩戻ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => $breakEndTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩戻')->update([
            'created_at' => $breakEndTime,
            'updated_at' => $breakEndTime,
        ]);

        // 休憩入ボタンが再び表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');
    }

    public function test_status_shows_working_after_break_return()
    {
        $breakStartTime = (clone $this->startWorkTime)->addHours(3);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩入')->update([
            'created_at' => $breakStartTime,
            'updated_at' => $breakStartTime,
        ]);

        $breakEndTime = (clone $breakStartTime)->addHours(1);

        // 休憩戻ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => $breakEndTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩戻')->update([
            'created_at' => $breakEndTime,
            'updated_at' => $breakEndTime,
        ]);

        // 最終的に出勤中が表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_break_return_button_shows_after_second_break_start()
    {
        $breakStartTime1 = (clone $this->startWorkTime)->addHours(3);

        // 1回目の休憩入
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime1,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩入')->update([
            'created_at' => $breakStartTime1,
            'updated_at' => $breakStartTime1,
        ]);

        $breakEndTime1 = (clone $breakStartTime1)->addHours(1);

        // 1回目の休憩戻
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => $breakEndTime1,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩戻')->update([
            'created_at' => $breakEndTime1,
            'updated_at' => $breakEndTime1,
        ]);

        $breakStartTime2 = (clone $breakEndTime1)->addHours(1);

        // 2回目の休憩入
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime2,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩入')->update([
            'created_at' => $breakStartTime2,
            'updated_at' => $breakStartTime2,
        ]);

        // 最終的に休憩戻ボタンが表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');
    }

    public function test_break_time_is_displayed_in_admin_attendance_list()
    {
        $breakStartTime = (clone $this->startWorkTime)->addHours(3);

        // 休憩入ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩入',
            'stamped_at' => $breakStartTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩入')->update([
            'created_at' => $breakStartTime,
            'updated_at' => $breakStartTime,
        ]);

        // 休憩戻ボタンが表示されることを確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');

        $breakEndTime = (clone $breakStartTime)->addHours(1);

        // 休憩戻ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '休憩戻',
            'stamped_at' => $breakEndTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        Stamp::where('user_id', $this->user->id)->where('stamp_type', '休憩戻')->update([
            'created_at' => $breakEndTime,
            'updated_at' => $breakEndTime,
        ]);

        // 管理者としてログイン
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');

        // 管理者の勤怠一覧画面にアクセス
        $response = $this->get('/admin/attendance/list');
        $response->assertStatus(200);

        $breakTime = $breakEndTime->diffInMinutes($breakStartTime);
        $formattedBreakTime = Carbon::createFromTime(0, 0, 0)->addMinutes($breakTime)->format('H:i');

        $this->assertEquals($breakTime, 60);

        // 休憩時間が表示されていることを確認
        $response->assertSee($formattedBreakTime);
    }
}
