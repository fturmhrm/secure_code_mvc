<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware - Middleware untuk check role user
 *
 * OWASP A01:2025 - Broken Access Control
 * Middleware ini memastikan hanya user dengan role tertentu yang bisa akses route.
 *
 * USAGE:
 * - Single role:   Route::middleware('role:admin')
 * - Multiple roles: Route::middleware('role:admin,staff')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Multiple roles (e.g., 'admin', 'staff')
     *
     * IMPORTANT: Laravel passes comma-separated middleware params as SEPARATE arguments!
     * - Route::middleware('role:admin,staff') → handle($request, $next, 'admin', 'staff')
     * - NOT as single string 'admin,staff'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (! $request->user()) {
            // Redirect ke login jika belum authenticated
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // $roles sudah berupa array karena variadic parameter
        // Check if user has any of the allowed roles
        if (! $request->user()->hasAnyRole($roles)) {
            // SECURITY: Return 403 Forbidden
            // Jangan redirect ke halaman lain - user harus tahu akses ditolak
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
