<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Register Controller - SECURE IMPLEMENTATION
 * 
 * Controller ini mengimplementasikan:
 * 1. Strong password validation rules
 * 2. Proper password hashing
 * 3. Input validation
 * 4. Auto-login dengan session regeneration
 */
class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'isSecure' => true,
        ]);
    }

    /**
     * Handle an incoming registration request.
     * 
     * SECURITY FEATURES:
     * 1. Email validation + uniqueness check
     * 2. Password complexity requirements
     * 3. Password confirmation
     * 4. Automatic hashing via User model
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // SECURITY: Comprehensive validation
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Prevent XSS in name
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns', // Validasi format + DNS check
                'max:255',
                'unique:' . User::class,
            ],
            'password' => [
                'required',
                'confirmed',
                // SECURITY: Strong password rules
                Rules\Password::defaults()
                    ->min(8)           // Minimal 8 karakter
                    ->letters()        // Harus ada huruf
                    ->numbers()        // Harus ada angka
                    ->mixedCase()      // Harus ada uppercase & lowercase
                    // ->symbols()     // Uncomment untuk production
                    // ->uncompromised() // Check against breached passwords
            ],
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Create user - password otomatis di-hash via model casting
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Auto-hashed!
        ]);

        // Fire registered event (untuk email verification, dll)
        event(new Registered($user));

        // Auto login setelah register
        Auth::login($user);

        // SECURITY: Regenerate session
        $request->session()->regenerate();

        return redirect(route('dashboard', absolute: false));
    }
}
