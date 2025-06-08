<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;


class StartWorkTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_clock_in_button_shows_when_status_is_working_outside()
    {
        $response = $this->get('/attendance');
        $response->assertSee('勤務外');
        $response->assertSee('出勤');

        $response = $this->post('/attendance', [
            'stamp_type' => '出勤',
            'stamped_at' => now(),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
    }

    public function test_clock_in_button_not_shows_when_status_is_clocked_out()
    {
        Stamp::create([
            'user_id' => $this->user->id,
            'stamp_type' => '退勤',
            'stamped_at' => now(),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
        $response->assertDontSee('出勤');
    }

    public function test_clock_in_time_is_displayed_in_admin_attendance_list()
    {
        // 出勤ボタンをクリック
        $response = $this->post('/attendance', [
            'stamp_type' => '出勤',
            'stamped_at' => now(),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302); // リダイレクトを確認

        // 管理者としてログイン
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');

        // 管理者の勤怠一覧画面にアクセス
        $response = $this->get('/admin/attendance/list');
        $response->assertStatus(200);

        // 現在の時間が表示されていることを確認
        $currentTime = now()->format('H:i');
        $response->assertSee($currentTime);
    }
}
