<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\OneTimePasswordServiceProvider;
use App\Notifications\EmailVerificationOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    protected $otpService;

    public function __construct(OneTimePasswordServiceProvider $otpService)
    {
        $this->otpService = $otpService;
        $this->middleware('auth');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show()
    {
        return view('auth.verify-guidance');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if ($this->otpService->verifyOtp($user, $request->otp)) {
            $user->markEmailAsVerified();
            return redirect()->intended('/attendance')->with('verified', true);
        }

        return back()->withErrors(['otp' => 'OTPが無効または期限切れです。']);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect('/home');
        }

        $otp = $this->otpService->generateOtp($user);
        $user->notify(new EmailVerificationOtp($otp));

        return back()->with('status', '新しいOTPを送信しました！');
    }
}
