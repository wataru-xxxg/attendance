<?php

namespace App\Providers;

use App\Models\User;
use App\Models\OneTimePassword;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class OneTimePasswordServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OneTimePasswordServiceProvider::class, function ($app) {
            return new OneTimePasswordServiceProvider($app);
        });
    }
    public function generateOtp(User $user): string
    {
        OneTimePassword::where('user_id', $user->id)
            ->where('used', false)
            ->update(['used' => true]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OneTimePassword::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        return $otp;
    }

    public function verifyOtp(User $user, string $otp): bool
    {
        $userOtp = OneTimePassword::where('user_id', $user->id)
            ->where('otp', $otp)
            ->where('used', false)
            ->first();

        if (!$userOtp || !$userOtp->isValid()) {
            return false;
        }

        $userOtp->update(['used' => true]);
        return true;
    }
}
