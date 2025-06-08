<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;


class EndWorkTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_clock_out_button_shows_when_status_is_working()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('退勤');

        // 退勤ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '退勤',
            'stamped_at' => Carbon::createFromTime(18, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
    }

    public function test_clock_out_time_is_displayed_in_admin_attendance_list()
    {
        // 出勤ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '出勤',
            'stamped_at' => Carbon::createFromTime(9, 0, 0),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 退勤ボタンをクリック
        $clockOutTime = Carbon::createFromTime(18, 0, 0);
        $response = $this->post('/attendance', [
            'stamp_type' => '退勤',
            'stamped_at' => $clockOutTime,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        // 管理者としてログイン
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');

        // 管理者の勤怠一覧画面にアクセス
        $response = $this->get('/admin/attendance/list');
        $response->assertStatus(200);

        // 退勤時間が表示されていることを確認
        $response->assertSee($clockOutTime->format('H:i'));
    }
}
