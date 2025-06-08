<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailVerificationOtp;
use App\Providers\OneTimePasswordServiceProvider;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password-confirmation' => 'password123'
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(302);
        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', $userData['email'])->first();
        $this->assertFalse($user->hasVerifiedEmail());

        Notification::assertSentTo(
            $user,
            EmailVerificationOtp::class
        );
    }

    public function test_user_can_navigate_to_otp_verification_page_from_guidance()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));
        $response->assertStatus(200);

        $response = $this->get(route('verification.otp'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-otp');
    }

    public function test_user_is_redirected_to_attendance_after_successful_otp_verification()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $this->actingAs($user);

        $otpService = app(OneTimePasswordServiceProvider::class);
        $otp = $otpService->generateOtp($user);

        $response = $this->post(route('verification.verify'), [
            'otp' => $otp
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.index'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
