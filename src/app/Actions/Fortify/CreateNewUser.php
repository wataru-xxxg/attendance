<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;
use App\Providers\OneTimePasswordServiceProvider;
use App\Notifications\EmailVerificationOtp;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make(
            $input,
            (new RegisterRequest())->rules(),
            (new RegisterRequest())->messages(),
        )->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $otpService = app(OneTimePasswordServiceProvider::class);
        $otp = $otpService->generateOtp($user);
        $user->notify(new EmailVerificationOtp($otp));

        return $user;
    }
}
