<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Http\Livewire\AdminAttendanceList;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;
    private User $user1;
    private User $user2;
    private Stamp $user1StartWork;
    private Stamp $user1EndWork;
    private Stamp $user2StartWork;
    private Stamp $user2EndWork;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->actingAs($this->admin, 'admin');

        // 今日の日付を取得
        $today = Carbon::today();

        // ユーザー1の勤怠データを作成
        $this->user1StartWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $today->copy()->setHour(9)->setMinute(0),
            'stamp_type' => '出勤',
        ]);
        $this->user1EndWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $today->copy()->setHour(18)->setMinute(0),
            'stamp_type' => '退勤',
        ]);

        // ユーザー2の勤怠データを作成
        $this->user2StartWork = Stamp::create([
            'user_id' => $this->user2->id,
            'stamped_at' => $today->copy()->setHour(9)->setMinute(30),
            'stamp_type' => '出勤',
        ]);
        $this->user2EndWork = Stamp::create([
            'user_id' => $this->user2->id,
            'stamped_at' => $today->copy()->setHour(18)->setMinute(30),
            'stamp_type' => '退勤',
        ]);
    }

    public function test_admin_can_view_all_users_attendance()
    {
        // 勤怠一覧ページにアクセス
        $response = $this->get('/admin/attendance/list');

        // レスポンスの検証
        $response->assertStatus(200);
        $response->assertSee($this->user1->name);
        $response->assertSee($this->user2->name);
        $response->assertSee($this->user1StartWork->stamped_at->format('H:i')); // ユーザー1の出勤時間
        $response->assertSee($this->user1EndWork->stamped_at->format('H:i')); // ユーザー1の退勤時間
        $response->assertSee($this->user2StartWork->stamped_at->format('H:i')); // ユーザー2の出勤時間
        $response->assertSee($this->user2EndWork->stamped_at->format('H:i')); // ユーザー2の退勤時間
    }

    public function test_current_date_is_displayed_correctly()
    {
        // 勤怠一覧ページにアクセス
        $response = $this->get('/admin/attendance/list');

        // レスポンスの検証
        $response->assertStatus(200);

        // 現在の日付が正しく表示されていることを確認
        $today = Carbon::today()->format('Y年m月d日');
        $response->assertSee($today);
    }

    public function test_previous_day_button_shows_correct_attendance()
    {
        // 前日の日付を取得
        $yesterday = Carbon::today()->subDay();

        // 前日の勤怠データを作成
        $yesterdayUser1StartWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $yesterday->copy()->setHour(9)->setMinute(0),
            'stamp_type' => '出勤',
        ]);
        $yesterdayUser1EndWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $yesterday->copy()->setHour(18)->setMinute(0),
            'stamp_type' => '退勤',
        ]);

        // 前日ボタンをクリックして前日の勤怠一覧を表示
        $component = Livewire::actingAs($this->admin)->test(AdminAttendanceList::class)->call('previousDay');

        // 前日の日付が表示されていることを確認
        $component->assertSee($yesterday->format('Y年m月d日'));

        // 前日の勤怠データが表示されていることを確認
        $component->assertSee($yesterdayUser1StartWork->stamped_at->format('H:i'));
        $component->assertSee($yesterdayUser1EndWork->stamped_at->format('H:i'));
    }

    public function test_next_day_button_shows_correct_attendance()
    {
        // 翌日の日付を取得
        $tomorrow = Carbon::today()->addDay();

        // 翌日の勤怠データを作成
        $tomorrowUser1StartWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $tomorrow->copy()->setHour(9)->setMinute(0),
            'stamp_type' => '出勤',
        ]);
        $tomorrowUser1EndWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $tomorrow->copy()->setHour(18)->setMinute(0),
            'stamp_type' => '退勤',
        ]);

        // 翌日ボタンをクリックして翌日の勤怠一覧を表示
        $component = Livewire::actingAs($this->admin)->test(AdminAttendanceList::class)->call('nextDay');

        // 翌日の日付が表示されていることを確認
        $component->assertSee($tomorrow->format('Y年m月d日'));

        // 翌日の勤怠データが表示されていることを確認
        $component->assertSee($tomorrowUser1StartWork->stamped_at->format('H:i'));
        $component->assertSee($tomorrowUser1EndWork->stamped_at->format('H:i'));
    }
}
