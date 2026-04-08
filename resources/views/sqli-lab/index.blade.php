{{-- ============================================ --}}
{{-- SQLI LAB: Index/Menu --}}
{{-- Materi Minggu 3 - Hari 4: SQL Injection --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Lab SQL Injection Prevention')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header --}}
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-database-exclamation text-danger"></i>
                    Lab SQL Injection Prevention
                </h1>
                <p class="lead text-muted">
                    Minggu 3 - Hari 4: "Inject or Be Injected"
                </p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Warning Banner --}}
            <div class="alert alert-danger mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-1 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">PERINGATAN PENTING!</h5>
                        <p class="mb-0">
                            Lab ini berisi kode <strong>VULNERABLE</strong> yang sengaja dibuat untuk pembelajaran.
                            <strong>JANGAN</strong> gunakan teknik ini untuk menyerang sistem apapun!
                            Pengetahuan ini hanya untuk <strong>DEFENSIVE</strong> purposes.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Definisi SQLi --}}
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle"></i> Apa itu SQL Injection?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lead">
                        <strong>SQL Injection (SQLi)</strong> adalah serangan yang menyisipkan perintah SQL
                        berbahaya ke dalam query database melalui <span class="text-danger fw-bold">input user</span>.
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="bi bi-exclamation-triangle text-danger"></i> Vulnerable Code:</h6>
                            <pre class="bg-dark text-light p-3 rounded small"><code>$query = "SELECT * FROM users
          WHERE name = '<span class="text-danger">$input</span>'";

// Input: ' OR '1'='1
// Query jadi:
SELECT * FROM users
WHERE name = '' <span class="text-warning">OR '1'='1'</span>
// ↑ Kondisi SELALU TRUE!</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-shield-check text-success"></i> Secure Code:</h6>
                            <pre class="bg-dark text-light p-3 rounded small"><code>// Eloquent ORM
User::where('name', <span class="text-success">$input</span>)->get();

// Parameter Binding
DB::select(
    "SELECT * FROM users WHERE name = <span class="text-success">?</span>",
    [<span class="text-success">$input</span>]
);

// Input di-escape otomatis!</code></pre>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3 mb-0">
                        <strong>OWASP Ranking:</strong> #5 di OWASP Top 10 2025 - "Injection"
                    </div>
                </div>
            </div>

            {{-- Setup Data --}}
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-database-gear"></i> Setup Demo Data
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Sebelum memulai lab, pastikan tabel dan data sudah tersedia:
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('sqli-lab.seed') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Seed Demo Data
                        </a>
                        <a href="{{ route('sqli-lab.reset') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Data
                        </a>
                    </div>
                    <div class="mt-3 small text-muted">
                        <strong>Data yang akan dibuat:</strong>
                        <ul class="mb-0">
                            <li>8 products (untuk demo search)</li>
                            <li>3 users: admin, user1, manager (untuk demo login bypass)</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Lab Menu --}}
            <h4 class="mb-3">
                <i class="bi bi-collection"></i> Pilihan Lab
            </h4>

            <div class="row g-4">
                {{-- How It Works --}}
                <div class="col-md-6">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-lightbulb"></i> Cara Kerja SQLi
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Pelajari bagaimana SQL Injection bekerja dan
                                berbagai jenis serangannya.
                            </p>
                            <ul class="small">
                                <li>Anatomi serangan SQLi</li>
                                <li>Karakter spesial SQL</li>
                                <li>In-Band vs Blind SQLi</li>
                                <li>Dampak serangan</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('sqli-lab.how-it-works') }}" class="btn btn-info w-100">
                                <i class="bi bi-book"></i> Pelajari
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Vulnerable Search --}}
                <div class="col-md-6">
                    <div class="card h-100 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-search"></i> Vulnerable Search
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Demo endpoint search yang vulnerable terhadap SQL Injection.
                            </p>
                            <ul class="small">
                                <li>String concatenation</li>
                                <li>Union-based attack</li>
                                <li>Extract data sensitif</li>
                            </ul>
                            <div class="alert alert-danger py-1 small mb-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                Hanya untuk pembelajaran!
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('sqli-lab.vulnerable-search') }}" class="btn btn-danger w-100">
                                <i class="bi bi-bug"></i> Coba Attack
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Vulnerable Login --}}
                <div class="col-md-6">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-key"></i> Login Bypass (In-band)
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Demo login form yang bisa di-bypass dengan SQL Injection.
                            </p>
                            <ul class="small">
                                <li>Authentication bypass</li>
                                <li>Comment injection (--)</li>
                                <li>OR '1'='1' attack</li>
                            </ul>
                            <div class="alert alert-warning py-1 small mb-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                Classic SQLi attack!
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('sqli-lab.vulnerable-login') }}" class="btn btn-warning w-100">
                                <i class="bi bi-unlock"></i> Coba Bypass
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Blind SQL Injection --}}
                <div class="col-md-6">
                    <div class="card h-100 border-dark">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-eye-slash"></i> Blind SQL Injection
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Teknik SQLi ketika tidak ada output langsung ke attacker.
                            </p>
                            <ul class="small">
                                <li>Boolean-based Blind SQLi</li>
                                <li>Time-based Blind SQLi</li>
                                <li>Extract data char-by-char</li>
                            </ul>
                            <div class="alert alert-dark py-1 small mb-0">
                                <i class="bi bi-clock"></i>
                                Advanced technique!
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('sqli-lab.blind-sqli') }}" class="btn btn-dark w-100">
                                <i class="bi bi-eye-slash"></i> Coba Blind SQLi
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Secure Search --}}
                <div class="col-md-6">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-check"></i> Secure Search
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Cara yang benar untuk query database di Laravel.
                            </p>
                            <ul class="small">
                                <li>Eloquent ORM</li>
                                <li>Query Builder</li>
                                <li>Parameter Binding</li>
                                <li>Named Binding</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('sqli-lab.secure-search') }}" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Lihat Contoh
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cheatsheet Link --}}
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5><i class="bi bi-journal-code"></i> SQLi Cheatsheet</h5>
                    <p class="text-muted mb-3">
                        Kumpulan payload SQLi dan cara pencegahannya
                    </p>
                    <a href="{{ route('sqli-lab.cheatsheet') }}" class="btn btn-dark">
                        <i class="bi bi-file-code"></i> Lihat Cheatsheet
                    </a>
                </div>
            </div>

            {{-- Quick Reference --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-code-slash"></i> Quick Reference: Vulnerable vs Secure
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pattern</th>
                                    <th>Keamanan</th>
                                    <th>Contoh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>String Concatenation</td>
                                    <td><span class="badge bg-danger">VULNERABLE</span></td>
                                    <td><code>"SELECT * FROM users WHERE id = $id"</code></td>
                                </tr>
                                <tr>
                                    <td>Eloquent ORM</td>
                                    <td><span class="badge bg-success">SECURE</span></td>
                                    <td><code>User::find($id)</code></td>
                                </tr>
                                <tr>
                                    <td>Query Builder</td>
                                    <td><span class="badge bg-success">SECURE</span></td>
                                    <td><code>DB::table('users')->where('id', $id)->get()</code></td>
                                </tr>
                                <tr>
                                    <td>Parameter Binding (?)</td>
                                    <td><span class="badge bg-success">SECURE</span></td>
                                    <td><code>DB::select("SELECT * FROM users WHERE id = ?", [$id])</code></td>
                                </tr>
                                <tr>
                                    <td>Named Binding (:name)</td>
                                    <td><span class="badge bg-success">SECURE</span></td>
                                    <td><code>DB::select("SELECT * FROM users WHERE id = :id", ['id' => $id])</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
