<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Default Password Rules
        |--------------------------------------------------------------------------
        |
        | Set default password rules yang akan digunakan aplikasi.
        | Rules ini diterapkan saat menggunakan Password::defaults()
        |
        | SECURITY RECOMMENDATION:
        | - min(8): Minimum 8 karakter
        | - letters(): Harus ada huruf
        | - numbers(): Harus ada angka
        | - mixedCase(): Huruf besar dan kecil
        | - symbols(): Special characters (optional tapi recommended)
        | - uncompromised(): Check against breached password databases
        |
        */
        Password::defaults(function () {
            $rule = Password::min(8)
                ->letters()
                ->numbers()
                ->mixedCase();

            // Di production, tambahkan:
            // return $rule->uncompromised();

            return $rule;
        });
    }
}
