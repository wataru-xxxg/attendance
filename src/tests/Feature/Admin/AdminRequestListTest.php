<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\CorrectionRequest;
use App\Models\Correction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Http\Livewire\RequestList;

class AdminRequestListTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;
    private User $user;
    private CorrectionRequest $correctionRequest;
    private Correction $startWorkCorrection;
    private Correction $endWorkCorrection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();
        $this->user = User::factory()->create();
        $this->actingAs($this->admin, 'admin');

        // 修正申請を作成
        $this->correctionRequest = CorrectionRequest::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'notes' => 'テスト備考',
            'approved' => false
        ]);

        // 出勤修正内容を作成
        $this->startWorkCorrection = Correction::create([
            'correction_request_id' => $this->correctionRequest->id,
            'stamp_type' => '出勤',
            'corrected_at' => Carbon::today()->setHour(9)->setMinute(0)
        ]);

        // 退勤修正内容を作成
        $this->endWorkCorrection = Correction::create([
            'correction_request_id' => $this->correctionRequest->id,
            'stamp_type' => '退勤',
            'corrected_at' => Carbon::today()->setHour(18)->setMinute(0)
        ]);
    }

    public function test_admin_can_view_pending_requests()
    {
        // 申請一覧画面にアクセス
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        // 承認待ちの修正申請が表示されていることを確認
        $response->assertSee($this->user->name);
        $response->assertSee(Carbon::parse($this->correctionRequest->date)->format('Y/m/d'));
        $response->assertSee($this->correctionRequest->notes);
    }

    public function test_pending_requests_are_displayed_when_clicking_pending_tab()
    {
        // 申請一覧画面にアクセス
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        // 承認待ちタブをクリック
        $component = Livewire::actingAs($this->admin)->test(RequestList::class);
        $component->call('switchTab', 'pending');

        // 承認待ちの修正申請が表示されていることを確認
        $component->assertSee($this->user->name);
        $component->assertSee(Carbon::parse($this->correctionRequest->date)->format('Y/m/d'));
        $component->assertSee($this->correctionRequest->notes);
    }

    public function test_approved_requests_are_not_displayed_in_pending_tab()
    {
        // 修正申請を承認済みに更新
        $this->correctionRequest->approved = true;
        $this->correctionRequest->save();

        // 申請一覧画面にアクセス
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        // 承認待ちタブをクリック
        $component = Livewire::actingAs($this->admin)->test(RequestList::class);
        $component->call('switchTab', 'pending');

        // 承認済みの修正申請が表示されていないことを確認
        $component->assertDontSee($this->user->name);
        $component->assertDontSee(Carbon::parse($this->correctionRequest->date)->format('Y/m/d'));
        $component->assertDontSee($this->correctionRequest->notes);
    }

    public function test_approved_requests_are_displayed_when_clicking_approved_tab()
    {
        // 修正申請を承認済みに更新
        $this->correctionRequest->approved = true;
        $this->correctionRequest->save();

        // 申請一覧画面にアクセス
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        // 承認済みタブをクリック
        $component = Livewire::actingAs($this->admin)->test(RequestList::class);
        $component->call('switchTab', 'approved');

        // 承認済みの修正申請が表示されていることを確認
        $component->assertSee($this->user->name);
        $component->assertSee(Carbon::parse($this->correctionRequest->date)->format('Y/m/d'));
        $component->assertSee($this->correctionRequest->notes);
    }

    public function test_admin_can_view_request_details_from_request_list()
    {
        // 申請一覧画面にアクセス
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        // 詳細ページに遷移
        $detailResponse = $this->get(route('admin.request.approve', ['attendance_correct_request' => $this->correctionRequest->id]));
        $detailResponse->assertStatus(200);

        // 詳細ページに正しい情報が表示されていることを確認
        $detailResponse->assertSee($this->user->name);
        $detailResponse->assertSee(Carbon::parse($this->correctionRequest->date)->format('Y年'));
        $detailResponse->assertSee(Carbon::parse($this->correctionRequest->date)->format('m月d日'));
        $detailResponse->assertSee($this->startWorkCorrection->corrected_at->format('H:i'));
        $detailResponse->assertSee($this->endWorkCorrection->corrected_at->format('H:i'));
        $detailResponse->assertSee($this->correctionRequest->notes);
    }

    public function test_admin_can_approve_correction_request()
    {
        // 申請一覧画面にアクセス
        $response = $this->get(route('request.list'));
        $response->assertStatus(200);

        // 詳細ページに遷移
        $detailResponse = $this->get(route('admin.request.approve', ['attendance_correct_request' => $this->correctionRequest->id]));
        $detailResponse->assertStatus(200);

        // 承認ボタンをクリック
        $component = Livewire::actingAs($this->admin)->test('approve', ['correctionRequestId' => $this->correctionRequest->id]);
        $component->call('approve');

        // 修正申請が承認されたことを確認
        $this->assertDatabaseHas('correction_requests', [
            'id' => $this->correctionRequest->id,
            'approved' => true
        ]);

        // 承認済みタブに表示されることを確認
        $listComponent = Livewire::actingAs($this->admin)->test(RequestList::class);
        $listComponent->call('switchTab', 'approved');
        $listComponent->assertSee($this->user->name);
        $listComponent->assertSee(Carbon::parse($this->correctionRequest->date)->format('Y/m/d'));
        $listComponent->assertSee($this->correctionRequest->notes);
    }
}
