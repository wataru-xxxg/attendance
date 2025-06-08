<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\CorrectionRequest;
use App\Models\AdminUser;
use Livewire\Livewire;
use App\Http\Livewire\RequestList;
use App\Http\Livewire\Approve;

class CorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_error_message_is_displayed_when_start_time_is_after_end_time()
    {
        $date = now()->format('Ymd');
        $response = $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '18:00',
            'endWork' => '09:00',
            'notes' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors('startWork');
        $response->assertSessionHasErrors('endWork');

        $response->assertStatus(302);
        $this->get(route('attendance.correct', ['id' => $date]))->assertSeeText('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function test_error_message_is_displayed_when_break_start_time_is_after_end_work_time()
    {
        $date = now()->format('Ymd');
        $response = $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['19:00'],
            'breakEnd' => ['20:00'],
            'notes' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors('breakStart.0');
        $response->assertStatus(302);
        $this->get(route('attendance.correct', ['id' => $date]))->assertSeeText('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function test_error_message_is_displayed_when_break_end_time_is_after_end_work_time()
    {
        $date = now()->format('Ymd');
        $response = $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['17:00'],
            'breakEnd' => ['19:00'],
            'notes' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors('breakEnd.0');
        $response->assertStatus(302);
        $this->get(route('attendance.correct', ['id' => $date]))->assertSeeText('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function test_error_message_is_displayed_when_notes_is_empty()
    {
        $date = now()->format('Ymd');
        $response = $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'notes' => ''
        ]);

        $response->assertSessionHasErrors('notes');
        $response->assertStatus(302);
        $this->get(route('attendance.correct', ['id' => $date]))->assertSeeText('備考を記入してください');
    }

    public function test_correction_request_is_successfully_created()
    {
        $date = now()->format('Ymd');
        $response = $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['12:00'],
            'breakEnd' => ['13:00'],
            'notes' => 'テスト備考'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.index'));

        $this->assertDatabaseHas('correction_requests', [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'notes' => 'テスト備考',
            'approved' => false
        ]);

        $correctionRequest = CorrectionRequest::where('user_id', $this->user->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        $this->assertDatabaseHas('corrections', [
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '出勤',
            'corrected_at' => now()->format('Y-m-d') . ' 09:00:00'
        ]);

        $this->assertDatabaseHas('corrections', [
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '退勤',
            'corrected_at' => now()->format('Y-m-d') . ' 18:00:00'
        ]);

        $this->assertDatabaseHas('corrections', [
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '休憩入',
            'corrected_at' => now()->format('Y-m-d') . ' 12:00:00'
        ]);

        $this->assertDatabaseHas('corrections', [
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '休憩戻',
            'corrected_at' => now()->format('Y-m-d') . ' 13:00:00'
        ]);
    }

    public function test_correction_request_is_displayed_in_admin_approval_page()
    {
        $date = now()->format('Ymd');
        $response = $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['12:00'],
            'breakEnd' => ['13:00'],
            'notes' => 'テスト備考'
        ]);

        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');

        $correctionRequest = CorrectionRequest::where('user_id', $this->user->id)
            ->where('date', now()->format('Y-m-d'))
            ->where('approved', false)
            ->where('notes', 'テスト備考')
            ->first();

        $response = $this->get(route('admin.request.approve', ['attendance_correct_request' => $correctionRequest->id]));
        $response->assertStatus(200);
        $response->assertSeeText($this->user->name);
        $response->assertSeeText(now()->format('Y年'));
        $response->assertSeeText(now()->format('n月d日'));
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
        $response->assertSeeText('テスト備考');
    }

    public function test_correction_request_is_displayed_in_user_request_list()
    {
        $date = now()->format('Ymd');
        $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['12:00'],
            'breakEnd' => ['13:00'],
            'notes' => 'テスト備考'
        ]);

        $response = $this->get(route('request.list'));
        $response->assertStatus(200);
        $response->assertSeeText(now()->format('Y/m/d'));
        $response->assertSeeText('テスト備考');
    }

    public function test_correction_request_is_displayed_in_pending_tab_but_not_in_approved_tab()
    {
        $date = now()->format('Ymd');
        $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['12:00'],
            'breakEnd' => ['13:00'],
            'notes' => 'テスト備考'
        ]);

        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        $correctionRequests = CorrectionRequest::where('user_id', $this->user->id)->where('approved', 0)->get();

        $component = Livewire::actingAs($this->user)->test(RequestList::class, ['correctionRequests' => $correctionRequests]);

        $component->assertSee($this->user->name);
        $component->assertSee(now()->format('Y/m/d'));
        $component->assertSee('テスト備考');

        $component->call('switchTab', 'approved');

        $component->assertDontSee($this->user->name);
        $component->assertDontSee(now()->format('Y/m/d'));
        $component->assertDontSee('テスト備考');
    }

    public function test_correction_request_is_displayed_in_approved_tab_after_admin_approval()
    {
        $date = now()->format('Ymd');
        $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => '09:00',
            'endWork' => '18:00',
            'breakStart' => ['12:00'],
            'breakEnd' => ['13:00'],
            'notes' => 'テスト備考'
        ]);

        $correctionRequest = CorrectionRequest::where('user_id', $this->user->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        // 管理者として承認
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');
        $this->get(route('admin.request.approve', ['attendance_correct_request' => $correctionRequest->id]));
        $approveComponent = Livewire::actingAs($admin)->test(Approve::class, ['correctionRequestId' => $correctionRequest->id]);
        $approveComponent->call('approve');

        // 一般ユーザーとして申請一覧を確認
        $this->actingAs($this->user);
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        $pendingCorrectionRequests = CorrectionRequest::where('user_id', $this->user->id)->where('approved', 0)->get();

        $requestListComponent = Livewire::actingAs($this->user)->test(RequestList::class, ['correctionRequests' => $pendingCorrectionRequests]);

        // 承認待ちタブでは表示されないことを確認
        $requestListComponent->assertDontSee($this->user->name);
        $requestListComponent->assertDontSee(now()->format('Y/m/d'));
        $requestListComponent->assertDontSee('テスト備考');

        // 承認済みタブに切り替えて表示されることを確認
        $requestListComponent->call('switchTab', 'approved');
        $requestListComponent->assertSee($this->user->name);
        $requestListComponent->assertSee(now()->format('Y/m/d'));
        $requestListComponent->assertSee('テスト備考');
    }

    public function test_user_can_view_request_details_from_request_list()
    {
        // 申請を作成
        $date = now()->format('Ymd');
        $startWork = '09:00';
        $endWork = '18:00';
        $breakStart = ['12:00'];
        $breakEnd = ['13:00'];
        $notes = 'テスト備考';

        $this->post(route('attendance.correct', ['id' => $date]), [
            'startWork' => $startWork,
            'endWork' => $endWork,
            'breakStart' => $breakStart,
            'breakEnd' => $breakEnd,
            'notes' => $notes
        ]);

        // 申請一覧ページにアクセス
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        // 申請一覧から詳細ページに遷移
        $correctionRequest = CorrectionRequest::where('user_id', $this->user->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        $detailResponse = $this->get(route('attendance.detail', $date));
        $detailResponse->assertStatus(200);

        // 詳細ページに正しい情報が表示されていることを確認
        $detailResponse->assertSee($this->user->name);
        $detailResponse->assertSee($startWork);
        $detailResponse->assertSee($endWork);
        $detailResponse->assertSee($breakStart[0]);
        $detailResponse->assertSee($breakEnd[0]);
        $detailResponse->assertSee('テスト備考');
    }
}
