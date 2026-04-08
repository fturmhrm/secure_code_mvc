{{-- ============================================ --}}
{{-- CSRF LAB: How It Works --}}
{{-- Penjelasan cara kerja CSRF Attack & Protection --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Cara Kerja CSRF - Lab')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('csrf-lab.index') }}">CSRF Lab</a></li>
            <li class="breadcrumb-item active">Cara Kerja CSRF</li>
        </ol>
    </nav>

    <h2 class="mb-4">
        <i class="bi bi-lightbulb text-info"></i>
        Cara Kerja CSRF Attack
    </h2>

    {{-- Step by Step Attack --}}
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="bi bi-bug"></i> Skenario Serangan CSRF
            </h5>
        </div>
        <div class="card-body">
            {{-- Timeline --}}
            <div class="position-relative">
                {{-- Step 1 --}}
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <strong>1</strong>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold">Victim Login ke bank.com</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-2">User login dan browser menyimpan session cookie.</p>
                            <pre class="bg-dark text-light p-2 rounded small mb-0"><code>POST /login → 200 OK
Set-Cookie: session=abc123xyz; HttpOnly; Secure</code></pre>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <strong>2</strong>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold">Victim Mengunjungi evil.com (Tab Baru)</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-2">Attacker mengirim link berbahaya via email/chat.</p>
                            <div class="alert alert-warning py-2 mb-0 small">
                                <i class="bi bi-envelope"></i>
                                "Selamat! Anda menang undian! Klik di sini: <u>evil.com/prize</u>"
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <strong>3</strong>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold">evil.com Berisi Hidden Form</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-2">Halaman attacker berisi form tersembunyi yang auto-submit:</p>
                            <pre class="bg-dark text-light p-2 rounded small mb-0"><code>&lt;!-- Di evil.com --&gt;
&lt;h1&gt;Selamat! Anda Menang!&lt;/h1&gt;

&lt;!-- Form tersembunyi --&gt;
&lt;form id="hack" action="https://bank.com/transfer" method="POST" style="display:none"&gt;
    &lt;input name="to" value="attacker_account"&gt;
    &lt;input name="amount" value="10000000"&gt;
&lt;/form&gt;

&lt;script&gt;
    document.getElementById('hack').submit(); // Auto submit!
&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <strong>4</strong>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold">Browser OTOMATIS Mengirim Cookie</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-2">Browser mengirim cookie bank.com untuk request ke bank.com:</p>
                            <pre class="bg-dark text-light p-2 rounded small mb-0"><code>POST https://bank.com/transfer
Cookie: session=abc123xyz  ← OTOMATIS DIKIRIM!
Content-Type: application/x-www-form-urlencoded

to=attacker_account&amount=10000000</code></pre>
                            <div class="alert alert-danger py-2 mt-2 mb-0 small">
                                <i class="bi bi-exclamation-triangle"></i>
                                Inilah inti masalahnya! Browser selalu mengirim cookie untuk domain tujuan.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <strong>5</strong>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold">bank.com Memproses Request</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-2">Server hanya cek session, tidak tahu request dari mana:</p>
                            <pre class="bg-dark text-light p-2 rounded small mb-0"><code>// Di server bank.com
if (session_valid($cookie)) {
    // Session valid? OK, proses transfer!
    transfer($to, $amount); // Rp 10.000.000 ke attacker!
}</code></pre>
                            <div class="alert alert-danger py-2 mt-2 mb-0">
                                <strong>💸 Uang victim berpindah ke attacker!</strong><br>
                                <small>Victim tidak tahu karena halaman terlihat seperti "hadiah undian".</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mengapa Bisa Terjadi --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-question-circle"></i> Mengapa CSRF Bisa Terjadi?
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6><i class="bi bi-1-circle text-primary"></i> Browser Auto-Send Cookies</h6>
                        <p class="small text-muted mb-0">
                            Setiap request ke domain X, browser <strong>OTOMATIS</strong> mengirim 
                            semua cookies untuk domain X. Ini fitur, bukan bug.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6><i class="bi bi-2-circle text-primary"></i> Server Hanya Validasi Session</h6>
                        <p class="small text-muted mb-0">
                            Server cuma cek: "Session valid?" tanpa verifikasi 
                            "Apakah user <em>sengaja</em> melakukan aksi ini?"
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6><i class="bi bi-3-circle text-primary"></i> Cross-Origin Form Submit</h6>
                        <p class="small text-muted mb-0">
                            Form HTML dari domain A bisa submit ke domain B. 
                            Ini fitur web yang sah (untuk integrasi).
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6><i class="bi bi-4-circle text-primary"></i> Predictable Request</h6>
                        <p class="small text-muted mb-0">
                            Attacker tahu struktur request (parameter apa yang dibutuhkan) 
                            dari menganalisis form target.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSRF Token Solution --}}
    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-shield-check"></i> Solusi: CSRF Token
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h6>Cara Kerja CSRF Token:</h6>
                    <ol>
                        <li class="mb-2">
                            <strong>Generate Token</strong><br>
                            <small class="text-muted">Server generate token random, simpan di session</small>
                        </li>
                        <li class="mb-2">
                            <strong>Sisipkan di Form</strong><br>
                            <small class="text-muted">Token disisipkan sebagai hidden input di setiap form</small>
                        </li>
                        <li class="mb-2">
                            <strong>Validasi saat Submit</strong><br>
                            <small class="text-muted">Server bandingkan token di request vs token di session</small>
                        </li>
                        <li class="mb-2">
                            <strong>Attacker Tidak Bisa Tahu</strong><br>
                            <small class="text-muted">Same-Origin Policy mencegah JavaScript dari domain lain membaca token</small>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-6">
                    <div class="bg-light p-3 rounded">
                        <h6>Visualisasi:</h6>
                        <pre class="bg-dark text-light p-2 rounded small mb-2"><code>{{-- Legitimate Request --}}
POST /transfer
Cookie: session=abc123
_token=xyz789  ✓ (dari form di bank.com)

Server: session_token == request_token? 
        xyz789 == xyz789 ✓ → OK!</code></pre>
                        <pre class="bg-dark text-light p-2 rounded small mb-0"><code>{{-- CSRF Attack --}}
POST /transfer
Cookie: session=abc123
_token=???  ✗ (attacker tidak tahu!)

Server: session_token == request_token? 
        xyz789 == ??? ✗ → 419 REJECTED!</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Same-Origin Policy --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-lock"></i> Same-Origin Policy (SOP)
            </h5>
        </div>
        <div class="card-body">
            <p>
                <strong>Same-Origin Policy</strong> adalah mekanisme keamanan browser yang 
                <span class="text-danger">MENCEGAH</span> script dari satu origin mengakses data dari origin lain.
            </p>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Origin = Protocol + Domain + Port</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered small">
                            <thead class="table-light">
                                <tr>
                                    <th>URL</th>
                                    <th>Same Origin?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>https://bank.com/page1</code></td>
                                    <td class="text-success">✓ Base</td>
                                </tr>
                                <tr>
                                    <td><code>https://bank.com/page2</code></td>
                                    <td class="text-success">✓ Same</td>
                                </tr>
                                <tr>
                                    <td><code>http://bank.com/page</code></td>
                                    <td class="text-danger">✗ Different protocol</td>
                                </tr>
                                <tr>
                                    <td><code>https://evil.com/page</code></td>
                                    <td class="text-danger">✗ Different domain</td>
                                </tr>
                                <tr>
                                    <td><code>https://bank.com:8080/page</code></td>
                                    <td class="text-danger">✗ Different port</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Yang Diblokir SOP:</h6>
                    <ul class="small">
                        <li>JavaScript dari evil.com <strong>TIDAK BISA</strong> membaca DOM bank.com</li>
                        <li>JavaScript dari evil.com <strong>TIDAK BISA</strong> membaca response dari bank.com</li>
                        <li>JavaScript dari evil.com <strong>TIDAK BISA</strong> membaca cookies bank.com</li>
                    </ul>
                    
                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-lightbulb"></i>
                        <strong>Inilah mengapa CSRF token aman!</strong><br>
                        Attacker bisa <em>mengirim</em> request ke bank.com, 
                        tapi tidak bisa <em>membaca</em> halaman bank.com untuk mendapatkan token.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('csrf-lab.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Menu
        </a>
        <a href="{{ route('csrf-lab.attack-demo') }}" class="btn btn-danger">
            Coba Demo Serangan <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@endsection
