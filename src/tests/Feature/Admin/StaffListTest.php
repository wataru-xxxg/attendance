<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Stamp;
use Carbon\Carbon;
use Livewire\Livewire;
use App\Http\Livewire\AttendanceList;

class StaffListTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;
    private User $user1;
    private User $user2;
    private Stamp $user1StartWork;
    private Stamp $user1EndWork;
    private Stamp $user2StartWork;
    private Stamp $user2EndWork;
    private Stamp $user1BreakStartTimeStamp;
    private Stamp $user1BreakEndTimeStamp;
    private Stamp $user2BreakStartTimeStamp;
    private Stamp $user2BreakEndTimeStamp;
    private $user1BreakTime;
    private $user2BreakTime;
    private $user1TotalTime;
    private $user2TotalTime;

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
        $this->user1BreakStartTimeStamp = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $today->copy()->setHour(12)->setMinute(0),
            'stamp_type' => '休憩入',
        ]);
        $this->user1BreakEndTimeStamp = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $today->copy()->setHour(13)->setMinute(0),
            'stamp_type' => '休憩戻',
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
        $this->user2BreakStartTimeStamp = Stamp::create([
            'user_id' => $this->user2->id,
            'stamped_at' => $today->copy()->setHour(12)->setMinute(0),
            'stamp_type' => '休憩入',
        ]);
        $this->user2BreakEndTimeStamp = Stamp::create([
            'user_id' => $this->user2->id,
            'stamped_at' => $today->copy()->setHour(13)->setMinute(0),
            'stamp_type' => '休憩戻',
        ]);

        $this->user1BreakTime = $this->user1BreakStartTimeStamp->stamped_at->diffInMinutes($this->user1BreakEndTimeStamp->stamped_at);
        $this->user2BreakTime = $this->user2BreakStartTimeStamp->stamped_at->diffInMinutes($this->user2BreakEndTimeStamp->stamped_at);
        $this->user1TotalTime = $this->user1StartWork->stamped_at->diffInMinutes($this->user1EndWork->stamped_at) - $this->user1BreakTime;
        $this->user2TotalTime = $this->user2StartWork->stamped_at->diffInMinutes($this->user2EndWork->stamped_at) - $this->user2BreakTime;
    }

    public function test_staff_list_displays_all_users_correctly()
    {
        // スタッフ一覧ページにアクセス
        $response = $this->get(route('admin.staff.list'));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 各ユーザーの情報が正しく表示されていることを確認
        $response->assertSee($this->user1->name);
        $response->assertSee($this->user1->email);
        $response->assertSee($this->user2->name);
        $response->assertSee($this->user2->email);
    }

    public function test_staff_attendance_displays_correctly()
    {
        // スタッフ一覧ページにアクセス
        $response = $this->get(route('admin.staff.list'));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // スタッフの勤怠ページにアクセス
        $response = $this->get(route('admin.attendance.staff', ['id' => $this->user1->id]));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // ユーザー名が表示されていることを確認
        $response->assertSee($this->user1->name);

        // 勤怠情報が正しく表示されていることを確認
        $response->assertSee($this->user1StartWork->stamped_at->format('H:i')); // 出勤時間
        $response->assertSee($this->user1EndWork->stamped_at->format('H:i')); // 退勤時間
        $response->assertSee(Carbon::createFromTime(0, 0, 0)->addMinutes($this->user1BreakTime)->format('H:i')); // 休憩時間
        $response->assertSee(Carbon::createFromTime(0, 0, 0)->addMinutes($this->user1TotalTime)->format('H:i')); // 勤務時間
    }

    public function test_previous_month_button_displays_correct_attendance_data()
    {
        $previousMonth = Carbon::now()->subMonth();

        // 前月の勤怠データを作成
        $user1PreviousMonthStartWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $previousMonth->copy()->setHour(9)->setMinute(0),
            'stamp_type' => '出勤',
        ]);
        $user1PreviousMonthEndWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $previousMonth->copy()->setHour(18)->setMinute(0),
            'stamp_type' => '退勤',
        ]);
        $user1PreviousMonthBreakStartTimeStamp = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $previousMonth->copy()->setHour(12)->setMinute(0),
            'stamp_type' => '休憩入',
        ]);
        $user1PreviousMonthBreakEndTimeStamp = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $previousMonth->copy()->setHour(13)->setMinute(0),
            'stamp_type' => '休憩戻',
        ]);

        $user1PreviousMonthBreakTime = $user1PreviousMonthBreakStartTimeStamp->stamped_at->diffInMinutes($user1PreviousMonthBreakEndTimeStamp->stamped_at);
        $user1PreviousMonthTotalTime = $user1PreviousMonthStartWork->stamped_at->diffInMinutes($user1PreviousMonthEndWork->stamped_at) - $user1PreviousMonthBreakTime;

        // 勤怠一覧ページにアクセス
        $response = $this->get(route('admin.attendance.staff', ['id' => $this->user1->id]));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 現在の月のデータが表示されていることを確認
        $response->assertSee(Carbon::now()->format('Y/m'));

        // 前月ボタンをクリック
        $component = Livewire::actingAs($this->admin)->test(AttendanceList::class, ['userId' => $this->user1->id])->call('previousMonth');

        // 前月のデータが表示されていることを確認
        $component->assertSee($previousMonth->format('Y/m'));

        // 前月の勤怠データが正しく表示されていることを確認
        $component->assertSee($user1PreviousMonthStartWork->stamped_at->format('H:i')); // 出勤時間
        $component->assertSee($user1PreviousMonthEndWork->stamped_at->format('H:i')); // 退勤時間
        $component->assertSee(Carbon::createFromTime(0, 0, 0)->addMinutes($user1PreviousMonthBreakTime)->format('H:i')); // 休憩時間
        $component->assertSee(Carbon::createFromTime(0, 0, 0)->addMinutes($user1PreviousMonthTotalTime)->format('H:i')); // 勤務時間
    }

    public function test_next_month_button_displays_correct_attendance_data()
    {
        $nextMonth = Carbon::now()->addMonth();

        // 翌月の勤怠データを作成
        $user1NextMonthStartWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $nextMonth->copy()->setHour(9)->setMinute(0),
            'stamp_type' => '出勤',
        ]);
        $user1NextMonthEndWork = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $nextMonth->copy()->setHour(18)->setMinute(0),
            'stamp_type' => '退勤',
        ]);
        $user1NextMonthBreakStartTimeStamp = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $nextMonth->copy()->setHour(12)->setMinute(0),
            'stamp_type' => '休憩入',
        ]);
        $user1NextMonthBreakEndTimeStamp = Stamp::create([
            'user_id' => $this->user1->id,
            'stamped_at' => $nextMonth->copy()->setHour(13)->setMinute(0),
            'stamp_type' => '休憩戻',
        ]);

        $user1NextMonthBreakTime = $user1NextMonthBreakStartTimeStamp->stamped_at->diffInMinutes($user1NextMonthBreakEndTimeStamp->stamped_at);
        $user1NextMonthTotalTime = $user1NextMonthStartWork->stamped_at->diffInMinutes($user1NextMonthEndWork->stamped_at) - $user1NextMonthBreakTime;

        // 勤怠一覧ページにアクセス
        $response = $this->get(route('admin.attendance.staff', ['id' => $this->user1->id]));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 現在の月のデータが表示されていることを確認
        $response->assertSee(Carbon::now()->format('Y/m'));

        // 翌月ボタンをクリック
        $component = Livewire::actingAs($this->admin)->test(AttendanceList::class, ['userId' => $this->user1->id])->call('nextMonth');

        // 翌月のデータが表示されていることを確認
        $component->assertSee($nextMonth->format('Y/m'));

        // 翌月の勤怠データが正しく表示されていることを確認
        $component->assertSee($user1NextMonthStartWork->stamped_at->format('H:i')); // 出勤時間
        $component->assertSee($user1NextMonthEndWork->stamped_at->format('H:i')); // 退勤時間
        $component->assertSee(Carbon::createFromTime(0, 0, 0)->addMinutes($user1NextMonthBreakTime)->format('H:i')); // 休憩時間
        $component->assertSee(Carbon::createFromTime(0, 0, 0)->addMinutes($user1NextMonthTotalTime)->format('H:i')); // 勤務時間
    }

    public function test_staff_attendance_detail_button_redirects_to_detail_page()
    {
        // スタッフ一覧ページにアクセス
        $response = $this->get(route('admin.staff.list'));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // スタッフの勤怠ページにアクセス
        $response = $this->get(route('admin.attendance.staff', ['id' => $this->user1->id]));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // ユーザー名が表示されていることを確認
        $response->assertSee($this->user1->name);

        // 詳細ボタンをクリックして勤怠詳細ページに遷移
        $response = $this->get(route('attendance.detail', [
            'id' => Carbon::today()->format('Ymd'),
            'userId' => $this->user1->id
        ]));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 勤怠詳細ページに必要な情報が表示されていることを確認
        $response->assertSee($this->user1->name);
        $response->assertSee($this->user1StartWork->stamped_at->format('H:i')); // 出勤時間
        $response->assertSee($this->user1EndWork->stamped_at->format('H:i')); // 退勤時間
        $response->assertSee($this->user1BreakStartTimeStamp->stamped_at->format('H:i')); // 休憩開始時間
        $response->assertSee($this->user1BreakEndTimeStamp->stamped_at->format('H:i')); // 休憩終了時間
    }
}
