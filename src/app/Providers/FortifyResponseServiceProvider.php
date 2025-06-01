<?php

namespace App\Providers;

use App\Http\Responses\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Support\ServiceProvider;

class FortifyResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            RegisterResponseContract::class,
            RegisterResponse::class
        );
    }
}
