@extends('layouts.app')

@section('title', 'Comparison - Secure vs Vulnerable')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        
        {{-- Header --}}
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">
                <i class="bi bi-arrows-angle-expand"></i>
                Comparison: Secure vs Vulnerable
            </h1>
            <p class="lead text-muted">
                Perbandingan implementasi authentication yang aman dan rentan
            </p>
        </div>

        {{-- Section 1: Password Storage --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-database"></i> 1. Password Storage
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <h6>Code:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code>// Model User - Laravel 10+
protected $casts = [
    <span class="text-success">'password' => 'hashed'</span>, // Auto-hash!
];

// Atau manual
$user->password = <span class="text-success">Hash::make($password)</span>;

// Verify
<span class="text-success">Hash::check($input, $user->password)</span>;</code></pre>

                                <h6 class="mt-3">Hasil di Database:</h6>
                                <code class="d-block bg-light p-2 rounded small">
                                    $2y$10$xK9mN2pQr5sT8uVwXyZaBcD...
                                </code>
                                <span class="badge bg-success mt-2">TIDAK BISA DI-REVERSE</span>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <h6>Code:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code>// Model VulnerableUser
<span class="text-danger">// Tidak ada password hashing!</span>

$user->password = <span class="text-danger">$password</span>; // Plaintext!

// Verify
<span class="text-danger">$user->password === $input</span>; // Direct compare!</code></pre>

                                <h6 class="mt-3">Hasil di Database:</h6>
                                <code class="d-block bg-light p-2 rounded small text-danger">
                                    password123
                                </code>
                                <span class="badge bg-danger mt-2">LANGSUNG TERBACA!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Rate Limiting --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-speedometer"></i> 2. Rate Limiting (Brute Force Protection)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>// LoginRequest.php

public function authenticate(): void
{
    <span class="text-success">// Check rate limit FIRST</span>
    $this->ensureIsNotRateLimited();

    if (! Auth::attempt(...)) {
        <span class="text-success">// Increment counter on fail</span>
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([...]);
    }

    <span class="text-success">// Clear on success</span>
    RateLimiter::clear($this->throttleKey());
}

public function ensureIsNotRateLimited(): void
{
    <span class="text-success">// Max 5 attempts per minute</span>
    if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
        throw ValidationException::withMessages([
            'email' => 'Too many attempts. Try again later.'
        ]);
    }
}</code></pre>
                                <div class="alert alert-success small mb-0 mt-2">
                                    <strong>Result:</strong> Attacker hanya bisa 5 attempts/menit
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>// VulnerableLoginController.php

public function store(Request $request)
{
    $email = $request->input('email');
    $password = $request->input('password');

    <span class="text-danger">// NO RATE LIMITING AT ALL!</span>

    $user = VulnerableUser::where('email', $email)->first();

    if (!$user) {
        return back()->withErrors([...]);
    }

    if ($user->password !== $password) {
        return back()->withErrors([...]);
    }

    <span class="text-danger">// Attacker can try unlimited times!</span>
}</code></pre>
                                <div class="alert alert-danger small mb-0 mt-2">
                                    <strong>Result:</strong> Attacker bisa unlimited attempts!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Session Management --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-key"></i> 3. Session Management
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>// LoginController.php

public function store(LoginRequest $request)
{
    $request->authenticate();

    <span class="text-success">// Regenerate session ID</span>
    <span class="text-success">// Prevents session fixation attack!</span>
    $request->session()->regenerate();

    return redirect()->intended('dashboard');
}

public function destroy(Request $request)
{
    Auth::logout();

    <span class="text-success">// Invalidate entire session</span>
    $request->session()->invalidate();

    <span class="text-success">// Regenerate CSRF token</span>
    $request->session()->regenerateToken();

    return redirect('/');
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>// VulnerableLoginController.php

public function store(Request $request)
{
    // ... authentication logic ...

    <span class="text-danger">// NO SESSION REGENERATION!</span>
    <span class="text-danger">// Session fixation vulnerable!</span>
    session()->put('vulnerable_user', $user);

    return redirect('dashboard');
}

public function destroy(Request $request)
{
    <span class="text-danger">// Only forget specific keys</span>
    session()->forget('vulnerable_user');

    <span class="text-danger">// Session ID stays the same!</span>
    <span class="text-danger">// CSRF token not regenerated!</span>

    return redirect('/login');
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Password Validation --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-check2-square"></i> 4. Password Validation Rules
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>// RegisterController.php

$request->validate([
    'password' => [
        'required',
        'confirmed', <span class="text-success">// Must confirm</span>
        Rules\Password::defaults()
            <span class="text-success">->min(8)</span>         // Min 8 chars
            <span class="text-success">->letters()</span>      // Has letters
            <span class="text-success">->numbers()</span>      // Has numbers
            <span class="text-success">->mixedCase()</span>    // Upper + lower
            // ->symbols()    // Special chars
            // ->uncompromised() // Check breach
    ],
]);</code></pre>
                                <div class="alert alert-success small mb-0 mt-2">
                                    Password "123" → <strong>REJECTED</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>// VulnerableRegisterController.php

$request->validate([
    'name' => 'required',
    'email' => 'required|email',
    <span class="text-danger">'password' => 'required'</span>, // That's it!
    <span class="text-danger">// No min length!</span>
    <span class="text-danger">// No confirmation!</span>
    <span class="text-danger">// No complexity!</span>
]);</code></pre>
                                <div class="alert alert-danger small mb-0 mt-2">
                                    Password "123" → <strong>ACCEPTED!</strong> 😱
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5: Error Messages --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-chat-left-text"></i> 5. Error Messages (Information Disclosure)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <h6>Error Message:</h6>
                                <div class="alert alert-danger">
                                    "These credentials do not match our records."
                                </div>
                                <p class="small text-muted">
                                    <i class="bi bi-check"></i> 
                                    Generic message - tidak membocorkan apakah email ada atau tidak.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <h6>Error Messages:</h6>
                                <div class="alert alert-danger mb-2">
                                    "Email tidak ditemukan di database."
                                </div>
                                <div class="alert alert-danger">
                                    "Password salah."
                                </div>
                                <p class="small text-danger">
                                    <i class="bi bi-x"></i> 
                                    Membocorkan info! Attacker tahu email mana yang valid.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Summary
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Aspek</th>
                                <th class="text-center bg-success">Secure</th>
                                <th class="text-center bg-danger">Vulnerable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Password Storage</td>
                                <td class="text-center">bcrypt/Argon2 <span class="badge bg-success">✓</span></td>
                                <td class="text-center">Plaintext <span class="badge bg-danger">✗</span></td>
                            </tr>
                            <tr>
                                <td>Rate Limiting</td>
                                <td class="text-center">5 attempts/min <span class="badge bg-success">✓</span></td>
                                <td class="text-center">Unlimited <span class="badge bg-danger">✗</span></td>
                            </tr>
                            <tr>
                                <td>Session Regeneration</td>
                                <td class="text-center">Yes <span class="badge bg-success">✓</span></td>
                                <td class="text-center">No <span class="badge bg-danger">✗</span></td>
                            </tr>
                            <tr>
                                <td>Password Rules</td>
                                <td class="text-center">Min 8, mixed <span class="badge bg-success">✓</span></td>
                                <td class="text-center">None <span class="badge bg-danger">✗</span></td>
                            </tr>
                            <tr>
                                <td>Error Messages</td>
                                <td class="text-center">Generic <span class="badge bg-success">✓</span></td>
                                <td class="text-center">Specific <span class="badge bg-danger">✗</span></td>
                            </tr>
                            <tr>
                                <td>CSRF Protection</td>
                                <td class="text-center">Yes <span class="badge bg-success">✓</span></td>
                                <td class="text-center">Yes <span class="badge bg-success">✓</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Try It Yourself --}}
        <div class="text-center">
            <h5 class="mb-3">Coba Sendiri!</h5>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('login') }}" class="btn btn-success">
                    <i class="bi bi-shield-check"></i> Secure Login
                </a>
                <a href="{{ route('vulnerable.login') }}" class="btn btn-danger">
                    <i class="bi bi-exclamation-triangle"></i> Vulnerable Login
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
