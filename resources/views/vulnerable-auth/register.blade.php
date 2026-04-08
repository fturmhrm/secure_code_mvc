@extends('layouts.app')

@section('title', 'Register - Vulnerable')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">

        {{-- Vulnerable Badge --}}
        <div class="text-center mb-4">
            <span class="badge vulnerable-badge px-4 py-2 fs-6">
                <i class="bi bi-exclamation-triangle"></i> VULNERABLE REGISTER
            </span>
        </div>

        {{-- Warning Banner --}}
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-octagon"></i> PERINGATAN!</strong>
            <p class="mb-0 small">
                Form ini <strong>SENGAJA TIDAK AMAN</strong> untuk pembelajaran.
                Password akan disimpan <strong>PLAINTEXT</strong>!
            </p>
        </div>

        <div class="card auth-card vulnerable-border">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus"></i> Register (Vulnerable)
                </h5>
            </div>
            <div class="card-body p-4">

                {{-- Vulnerability Info --}}
                <div class="alert alert-warning small mb-4">
                    <strong><i class="bi bi-bug"></i> Vulnerabilities:</strong>
                    <ul class="mb-0 mt-1">
                        <li>❌ Password disimpan PLAINTEXT</li>
                        <li>❌ Tidak ada password rules</li>
                        <li>❌ Tidak ada password confirmation</li>
                        <li>❌ Minimal validation</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('vulnerable.register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="form-floating mb-3">
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Nama Lengkap"
                               required>
                        <label for="name">
                            <i class="bi bi-person"></i> Nama
                        </label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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

                    {{-- Password (NO CONFIRMATION!) --}}
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
                        <div class="form-text text-danger">
                            <small>
                                <i class="bi bi-exclamation-triangle"></i>
                                Password bisa apa saja! Tidak ada aturan!
                            </small>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-person-plus"></i> Register (Vulnerable)
                    </button>
                </form>

                <hr>

                <div class="text-center">
                    <p class="mb-0 small">
                        Sudah punya akun?
                        <a href="{{ route('vulnerable.login') }}" class="text-danger">Login (Vulnerable)</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- View Passwords Link --}}
        <div class="card mt-4 border-danger">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-database-exclamation"></i> Demo: Lihat Database
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    Setelah register, lihat bagaimana password tersimpan di database.
                    <strong>PLAINTEXT!</strong>
                </p>
                <a href="{{ route('vulnerable.show-users') }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-eye"></i> Lihat Semua User + Password
                </a>
            </div>
        </div>

        {{-- Compare Link --}}
        <div class="text-center mt-4">
            <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-shield-check"></i> Bandingkan dengan Secure Register
            </a>
        </div>

        {{-- Vulnerable Code Preview --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <small><i class="bi bi-code-slash"></i> Vulnerable Code Preview</small>
            </div>
            <div class="card-body p-0">
                <pre class="bg-dark text-light p-3 mb-0 small"><code>// VulnerableRegisterController.php
public function store(Request $request)
{
    <span class="text-danger">// ❌ Minimal validation - no password rules!</span>
    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required', <span class="text-danger">// No min, no confirmation!</span>
    ]);

    <span class="text-danger">// ❌ PASSWORD STORED AS PLAINTEXT!!!</span>
    $user = VulnerableUser::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => $request->password, <span class="text-danger">// NOT HASHED!</span>
    ]);
}</code></pre>
            </div>
        </div>

    </div>
</div>
@endsection
