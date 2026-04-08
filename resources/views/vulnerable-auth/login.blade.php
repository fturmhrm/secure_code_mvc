@extends('layouts.app')

@section('title', 'Login - Vulnerable')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">

        {{-- Vulnerable Badge --}}
        <div class="text-center mb-4">
            <span class="badge vulnerable-badge px-4 py-2 fs-6">
                <i class="bi bi-exclamation-triangle"></i> VULNERABLE LOGIN
            </span>
        </div>

        {{-- Warning Banner --}}
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-octagon"></i> PERINGATAN!</strong>
            <p class="mb-0 small">
                Form ini <strong>SENGAJA TIDAK AMAN</strong> untuk pembelajaran.
                Jangan gunakan pattern ini di production!
            </p>
        </div>

        <div class="card auth-card vulnerable-border">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i> Login (Vulnerable)
                </h5>
            </div>
            <div class="card-body p-4">

                {{-- Vulnerability Info --}}
                <div class="alert alert-warning small mb-4">
                    <strong><i class="bi bi-bug"></i> Vulnerabilities:</strong>
                    <ul class="mb-0 mt-1">
                        <li>❌ Tidak ada rate limiting</li>
                        <li>❌ Password plaintext comparison</li>
                        <li>❌ Tidak ada session regeneration</li>
                        <li>❌ Information disclosure di error</li>
                    </ul>
                </div>

                {{-- Brute Force Counter --}}
                <div class="alert alert-info small">
                    <i class="bi bi-graph-up"></i>
                    <strong>Login Attempts (5 menit terakhir):</strong>
                    <span class="badge bg-dark">{{ $recentAttempts ?? 0 }}</span>
                    <br>
                    <small class="text-muted">Tidak ada batasan! Coba brute force!</small>
                </div>

                <form method="POST" action="{{ route('vulnerable.login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="form-floating mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="email@example.com"
                               required>
                        <label for="email">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-floating mb-3">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Password"
                               required>
                        <label for="password">
                            <i class="bi bi-key"></i> Password
                        </label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login (Vulnerable)
                    </button>
                </form>

                <hr>

                <div class="text-center">
                    <p class="mb-0 small">
                        Belum punya akun?
                        <a href="{{ route('vulnerable.register') }}" class="text-danger">Register (Vulnerable)</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Compare Link --}}
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-shield-check"></i> Bandingkan dengan Secure Login
            </a>
        </div>

        {{-- Vulnerable Code Preview --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <small><i class="bi bi-code-slash"></i> Vulnerable Code Preview</small>
            </div>
            <div class="card-body p-0">
                <pre class="bg-dark text-light p-3 mb-0 small"><code>// VulnerableLoginController.php
public function store(Request $request)
{
    $email = $request->input('email');
    $password = $request->input('password');

    <span class="text-danger">// ❌ TIDAK ADA RATE LIMITING!</span>

    $user = VulnerableUser::where('email', $email)->first();

    if (!$user) {
        <span class="text-danger">// ❌ Information disclosure!</span>
        return back()->withErrors([
            'email' => 'Email tidak ditemukan.'
        ]);
    }

    <span class="text-danger">// ❌ PLAINTEXT PASSWORD COMPARISON!</span>
    if ($user->password !== $password) {
        return back()->withErrors([...]);
    }

    <span class="text-danger">// ❌ TIDAK ADA SESSION REGENERATION!</span>
    session()->put('vulnerable_user', $user);
}</code></pre>
            </div>
        </div>

        {{-- Brute Force Demo --}}
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <small><i class="bi bi-lightning"></i> Demo: Brute Force Attack</small>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    Coba login berkali-kali dengan password salah.
                    Tidak ada batasan karena tidak ada rate limiting!
                </p>
                <a href="{{ route('vulnerable.brute-force-stats') }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-graph-up"></i> Lihat Statistik Brute Force
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
