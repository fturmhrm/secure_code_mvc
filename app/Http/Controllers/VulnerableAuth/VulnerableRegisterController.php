<?php

namespace App\Http\Controllers\VulnerableAuth;

use App\Http\Controllers\Controller;
use App\Models\VulnerableUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vulnerable Register Controller
 * 
 * ⚠️ PERINGATAN: Controller ini SENGAJA TIDAK AMAN!
 * Hanya untuk pembelajaran - JANGAN gunakan di production!
 * 
 * VULNERABILITIES:
 * 1. ❌ Password disimpan PLAINTEXT (tidak di-hash)
 * 2. ❌ Tidak ada password complexity rules
 * 3. ❌ Minimal input validation
 * 4. ❌ Tidak ada email format validation
 * 5. ❌ Tidak ada XSS protection pada name
 */
class VulnerableRegisterController extends Controller
{
    /**
     * Display the vulnerable registration view.
     */
    public function create(): View
    {
        return view('vulnerable-auth.register', [
            'isSecure' => false,
        ]);
    }

    /**
     * Handle vulnerable registration request.
     * 
     * ❌ VULNERABLE POINTS:
     * 1. Password tidak di-hash
     * 2. Tidak ada password requirements
     * 3. Name tidak di-sanitize (XSS potential)
     */
    public function store(Request $request): RedirectResponse
    {
        // ❌ VULNERABLE: Minimal validation - no password rules
        $request->validate([
            'name' => 'required',           // No max length, no sanitization
            'email' => 'required|email',    // Basic email check only
            'password' => 'required',       // No min length, no confirmation!
        ]);

        // ❌ VULNERABLE: Check if email exists (but with bad error)
        if (VulnerableUser::where('email', $request->email)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email sudah terdaftar.']);
        }

        // ❌ VULNERABLE: Password stored as PLAINTEXT!
        $user = VulnerableUser::create([
            'name' => $request->name,       // No XSS sanitization
            'email' => $request->email,
            'password' => $request->password, // PLAINTEXT!!! BAHAYA!!!
        ]);

        // ❌ VULNERABLE: Auto login tanpa session regeneration
        $request->session()->put('vulnerable_user', $user);
        $request->session()->put('vulnerable_logged_in', true);

        return redirect()->route('vulnerable.dashboard')
            ->with('success', 'Registrasi berhasil! Password tersimpan: ' . $request->password);
    }

    /**
     * Show registered users with plaintext passwords
     * (untuk demo betapa bahayanya plaintext storage)
     */
    public function showUsers(): View
    {
        // ❌ VULNERABLE: Menampilkan semua user dengan password!
        $users = VulnerableUser::all();

        return view('vulnerable-auth.show-users', [
            'users' => $users,
            'isSecure' => false,
        ]);
    }
}
