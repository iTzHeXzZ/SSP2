<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Mache die E-Mail-Adresse bei der Authentifizierung nicht case-sensitive
        \Illuminate\Support\Facades\Validator::extend('case_insensitive_email', function ($attribute, $value, $parameters, $validator) {
            $user = \App\Models\User::whereRaw('LOWER(email) = ?', [strtolower($value)])->first();
            if ($user && Hash::check($validator->getData()[$parameters[0]], $user->password)) {
                return true;
            }
            return false;
        });
    }
}