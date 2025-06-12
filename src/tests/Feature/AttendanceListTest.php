<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Stamp;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Http\Livewire\AttendanceList;


class AttendanceListTest extends TestCase
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

    public function test_attendance_list_shows_all_times_after_complete_workflow()
    {
        // 勤怠一覧画面にアクセス
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 出勤時間が表示されていることを確認
        $response->assertSee($this->startWorkStamp->stamped_at->format('H:i'));

        // 退勤時間が表示されていることを確認
        $response->assertSee($this->endWorkStamp->stamped_at->format('H:i'));

        // 休憩時間が表示されていることを確認
        $breakTime = $this->breakEndTimeStamp->stamped_at->diffInMinutes($this->breakStartTimeStamp->stamped_at);
        $formattedBreakTime = Carbon::createFromTime(0, 0, 0)->addMinutes($breakTime)->format('H:i');
        $response->assertSee($formattedBreakTime);

        // 勤務時間が表示されていることを確認
        $workTime = $this->endWorkStamp->stamped_at->diffInMinutes($this->startWorkStamp->stamped_at) - $breakTime;
        $formattedWorkTime = Carbon::createFromTime(0, 0, 0)->addMinutes($workTime)->format('H:i');
        $response->assertSee($formattedWorkTime);
    }

    public function test_attendance_list_shows_current_month()
    {
        // 勤怠一覧画面にアクセス
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 現在の月が表示されていることを確認
        $currentMonth = now()->format('Y/m');
        $response->assertSee($currentMonth);
    }

    public function test_attendance_list_shows_previous_month_when_clicking_previous_month_button()
    {
        // 勤怠一覧画面にアクセス
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 前月ボタンをクリック
        $component = Livewire::actingAs($this->user)->test(AttendanceList::class, ['userId' => $this->user->id])->call('previousMonth');

        // 前月が表示されていることを確認
        $component->assertSee(now()->subMonth()->format('Y/m'));
    }

    public function test_attendance_list_shows_next_month_when_clicking_next_month_button()
    {
        // 勤怠一覧画面にアクセス
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 翌月ボタンをクリック
        $component = Livewire::actingAs($this->user)->test(AttendanceList::class, ['userId' => $this->user->id])->call('nextMonth');

        // 翌月が表示されていることを確認
        $component->assertSee(now()->addMonth()->format('Y/m'));
    }

    public function test_attendance_list_redirects_to_detail_page_when_clicking_detail_button()
    {
        // 勤怠一覧画面にアクセス
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        // 詳細ボタンのリンクを取得
        $detailLink = route('attendance.detail', [
            'id' => $this->startWorkStamp->stamped_at->format('Ymd')
        ]);

        // 詳細ボタンが存在することを確認
        $response->assertSee($detailLink);

        // 詳細画面にアクセス
        $detailResponse = $this->get($detailLink);
        $detailResponse->assertStatus(200);

        // 詳細画面に必要な情報が表示されていることを確認
        $detailResponse->assertSee($this->startWorkStamp->stamped_at->format('H:i'));
        $detailResponse->assertSee($this->endWorkStamp->stamped_at->format('H:i'));
    }
}
