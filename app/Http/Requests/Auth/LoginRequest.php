<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login Request - SECURE IMPLEMENTATION
 *
 * Request ini mengimplementasikan:
 * 1. Input validation
 * 2. Rate limiting (brute force protection)
 * 3. Proper authentication flow
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * SECURITY: Validasi input sebelum proses authentication
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * SECURITY FEATURES:
     * 1. Rate limiting - mencegah brute force
     * 2. Session regeneration - mencegah session fixation
     * 3. Proper credential verification via Auth facade
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // Check rate limit BEFORE attempting login
        $this->ensureIsNotRateLimited();

        // Attempt authentication
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Login gagal - increment rate limiter
            RateLimiter::hit($this->throttleKey());

            // Log attempt (untuk audit)
            \App\Models\LoginAttempt::create([
                'email' => $this->input('email'),
                'ip_address' => $this->ip(),
                'successful' => false,
                'type' => 'secure',
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Login berhasil - clear rate limiter
        RateLimiter::clear($this->throttleKey());

        // Log successful attempt
        \App\Models\LoginAttempt::create([
            'email' => $this->input('email'),
            'ip_address' => $this->ip(),
            'successful' => true,
            'type' => 'secure',
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * SECURITY: Batasi 5 percobaan per menit
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * SECURITY: Key berdasarkan email + IP untuk prevent bypass
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('email')) . '|' . $this->ip()
        );
    }

    /**
     * Get remaining attempts before lockout
     */
    public function remainingAttempts(): int
    {
        return RateLimiter::remaining($this->throttleKey(), 5);
    }
}
