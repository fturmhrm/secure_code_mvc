<?php

namespace App\Http\Controllers\VulnerableAuth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\VulnerableUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vulnerable Login Controller
 * 
 * ⚠️ PERINGATAN: Controller ini SENGAJA TIDAK AMAN!
 * Hanya untuk pembelajaran - JANGAN gunakan di production!
 * 
 * VULNERABILITIES:
 * 1. ❌ Tidak ada rate limiting
 * 2. ❌ Tidak ada session regeneration
 * 3. ❌ Password di-compare plaintext
 * 4. ❌ Tidak ada proper validation
 * 5. ❌ Session tidak di-invalidate dengan benar
 */
class VulnerableLoginController extends Controller
{
    /**
     * Display the vulnerable login view.
     */
    public function create(): View
    {
        // Hitung login attempts untuk demo
        $recentAttempts = LoginAttempt::vulnerable()
            ->failed()
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        return view('vulnerable-auth.login', [
            'isSecure' => false,
            'recentAttempts' => $recentAttempts,
        ]);
    }

    /**
     * Handle vulnerable login request.
     * 
     * ❌ VULNERABLE POINTS:
     * 1. Tidak ada rate limiting - bisa brute force unlimited
     * 2. Password comparison plaintext
     * 3. Tidak ada session regeneration
     * 4. Error message terlalu spesifik (information disclosure)
     */
    public function store(Request $request): RedirectResponse
    {
        // ❌ VULNERABLE: Minimal validation
        $email = $request->input('email');
        $password = $request->input('password');

        // Log attempt (untuk tracking di comparison page)
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $request->ip(),
            'successful' => false,
            'type' => 'vulnerable',
        ]);

        // ❌ VULNERABLE: Mencari user dengan plaintext password
        $user = VulnerableUser::where('email', $email)->first();

        if (!$user) {
            // ❌ VULNERABLE: Information disclosure - "email tidak ditemukan"
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak ditemukan di database.']);
        }

        // ❌ VULNERABLE: Plaintext password comparison
        if ($user->password !== $password) {
            return back()
                ->withInput()
                ->withErrors(['password' => 'Password salah.']);
        }

        // ❌ VULNERABLE: Tidak ada rate limit check
        // ❌ VULNERABLE: Tidak ada session regeneration

        // Update login attempt sebagai successful
        LoginAttempt::where('email', $email)
            ->where('type', 'vulnerable')
            ->latest()
            ->first()
            ?->update(['successful' => true]);

        // Store user di session (insecure way)
        $request->session()->put('vulnerable_user', $user);
        $request->session()->put('vulnerable_logged_in', true);

        return redirect()->route('vulnerable.dashboard')
            ->with('success', 'Login berhasil! (Tapi tidak aman!)');
    }

    /**
     * Vulnerable logout
     * 
     * ❌ VULNERABLE: Session tidak di-invalidate dengan benar
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ❌ VULNERABLE: Hanya remove specific keys, tidak invalidate
        $request->session()->forget('vulnerable_user');
        $request->session()->forget('vulnerable_logged_in');

        // ❌ VULNERABLE: Tidak regenerate CSRF token
        // ❌ VULNERABLE: Session ID masih sama

        return redirect()->route('vulnerable.login')
            ->with('info', 'Logged out (tapi session masih rentan!)');
    }

    /**
     * Vulnerable dashboard
     */
    public function dashboard(Request $request): View
    {
        $user = $request->session()->get('vulnerable_user');

        if (!$user) {
            return redirect()->route('vulnerable.login');
        }

        return view('vulnerable-auth.dashboard', [
            'user' => $user,
            'isSecure' => false,
        ]);
    }

    /**
     * Show brute force demo stats
     */
    public function bruteForceStats(Request $request): View
    {
        $email = $request->input('email', '');
        
        $attempts = LoginAttempt::vulnerable()
            ->where('email', 'like', "%{$email}%")
            ->latest()
            ->take(50)
            ->get();

        $stats = [
            'total' => $attempts->count(),
            'failed' => $attempts->where('successful', false)->count(),
            'success' => $attempts->where('successful', true)->count(),
            'last_5_min' => $attempts->where('created_at', '>=', now()->subMinutes(5))->count(),
        ];

        return view('vulnerable-auth.brute-force-stats', [
            'attempts' => $attempts,
            'stats' => $stats,
            'email' => $email,
        ]);
    }
}
