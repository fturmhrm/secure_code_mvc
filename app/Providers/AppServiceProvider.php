<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\User;
use App\Policies\TicketPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

/**
 * AppServiceProvider
 *
 * Main Service Provider untuk aplikasi
 * Minggu 4: Authentication & Authorization
 */
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
    public function boot(): void
    {
        // Use Bootstrap 5 for pagination (instead of Tailwind)
        Paginator::useBootstrapFive();

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

        // ========================================
        // REGISTER POLICIES
        // ========================================
        Gate::policy(Ticket::class, TicketPolicy::class);

        // ========================================
        // DEFINE GATES (Minggu 4 Hari 2)
        // ========================================

        /**
         * Gate: access-admin
         * Hanya admin yang bisa akses admin panel
         */
        Gate::define('access-admin', function (User $user): bool {
            return $user->isAdmin();
        });

        /**
         * Gate: manage-users
         * Hanya admin yang bisa manage users
         */
        Gate::define('manage-users', function (User $user): bool {
            return $user->isAdmin();
        });

        /**
         * Gate: view-reports
         * Admin dan staff bisa lihat reports
         */
        Gate::define('view-reports', function (User $user): bool {
            return $user->hasAnyRole(['admin', 'staff']);
        });

        /**
         * Gate: view-all-tickets
         * Admin dan staff bisa lihat semua tickets
         */
        Gate::define('view-all-tickets', function (User $user): bool {
            return $user->hasAnyRole(['admin', 'staff']);
        });

        /**
         * Gate: assign-tickets
         * Hanya admin yang bisa assign ticket ke staff
         */
        Gate::define('assign-tickets', function (User $user): bool {
            return $user->isAdmin();
        });
    }
}
