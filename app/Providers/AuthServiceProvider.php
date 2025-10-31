<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Auth::provider('users', function ($app, array $config) {
            return new class($config) extends \Illuminate\Auth\EloquentUserProvider {
                public function validateCredentials($user, array $credentials)
                {
                    $plain = $credentials['password'];
                    return Hash::check($plain, $user->password_hash);
                }
            };
        });
    }
}