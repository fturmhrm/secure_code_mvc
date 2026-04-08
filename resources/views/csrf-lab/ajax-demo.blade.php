{{-- ============================================ --}}
{{-- CSRF LAB: AJAX Demo --}}
{{-- Implementasi CSRF untuk AJAX/Fetch Request --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'CSRF untuk AJAX - Lab')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('csrf-lab.index') }}">CSRF Lab</a></li>
            <li class="breadcrumb-item active">CSRF untuk AJAX</li>
        </ol>
    </nav>

    {{-- Alert --}}
    <div class="alert alert-warning mb-4">
        <h5 class="alert-heading">
            <i class="bi bi-lightning"></i> CSRF untuk AJAX Request
        </h5>
        <p class="mb-0">
            Untuk request AJAX/Fetch, CSRF token perlu dikirim via <strong>header</strong> atau <strong>body</strong>, 
            bukan hidden input seperti form biasa.
        </p>
    </div>

    <div class="row">
        {{-- Implementation Guide --}}
        <div class="col-lg-6 mb-4">
            {{-- Step 1: Meta Tag --}}
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-1-circle"></i> Step 1: Meta Tag di Layout
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Tambahkan meta tag di <code>&lt;head&gt;</code> layout:
                    </p>
                    
                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;!-- resources/views/layouts/app.blade.php --&gt;
&lt;head&gt;
    ...
    <span class="text-primary">&lt;meta name="csrf-token" content="&#123;&#123; csrf_token() &#125;&#125;"&gt;</span>
&lt;/head&gt;</code></pre>

                    <div class="alert alert-info py-2 small mb-0 mt-2">
                        <i class="bi bi-info-circle"></i>
                        Meta tag memudahkan JavaScript mengambil token tanpa parsing HTML.
                    </div>
                </div>
            </div>

            {{-- Step 2: Fetch API --}}
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-2-circle"></i> Step 2: Fetch API dengan CSRF
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>// Ambil token dari meta tag
const token = document.querySelector(
    'meta[name="csrf-token"]'
).content;

// Kirim POST request
fetch('/api/action', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        <span class="text-success">'X-CSRF-TOKEN': token</span>  // ← Penting!
    },
    body: JSON.stringify({ data: 'value' })
})
.then(response => response.json())
.then(data => console.log(data));</code></pre>
                </div>
            </div>

            {{-- Step 3: Axios Setup --}}
            <div class="card border-warning mb-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-3-circle"></i> Axios: Auto Header Setup
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Jika menggunakan Axios, set default header sekali saja:
                    </p>
                    
                    <pre class="bg-dark text-light p-3 rounded"><code>// resources/js/bootstrap.js (Laravel default)
import axios from 'axios';
window.axios = axios;

<span class="text-warning">axios.defaults.headers.common['X-CSRF-TOKEN'] = 
    document.querySelector('meta[name="csrf-token"]').content;</span>

// Sekarang semua axios request otomatis include CSRF token!
axios.post('/api/action', { data: 'value' })
    .then(response => console.log(response));</code></pre>
                </div>
            </div>

            {{-- jQuery Ajax --}}
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-4-circle"></i> jQuery AJAX Setup
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code>// Setup global untuk semua jQuery AJAX
<span class="text-info">$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});</span>

// Atau per-request
$.ajax({
    url: '/api/action',
    type: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { message: 'Hello' },
    success: function(response) {
        console.log(response);
    }
});</code></pre>
                </div>
            </div>
        </div>

        {{-- Practice --}}
        <div class="col-lg-6">
            {{-- Live Test: With CSRF --}}
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-check-circle"></i> Test: AJAX dengan CSRF Token
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <input type="text" id="ajaxMessageSuccess" class="form-control" 
                               value="Hello from AJAX with CSRF!" placeholder="Ketik pesan...">
                    </div>
                    
                    <button type="button" class="btn btn-success" onclick="testAjaxWithCSRF()">
                        <i class="bi bi-send"></i> Kirim dengan CSRF Token
                    </button>
                    
                    <div id="ajaxSuccessResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>

            {{-- Live Test: Without CSRF --}}
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-x-circle"></i> Test: AJAX tanpa CSRF Token
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Test kirim request tanpa CSRF token. Expected: <strong>419 Error</strong>
                    </p>
                    
                    <button type="button" class="btn btn-danger" onclick="testAjaxWithoutCSRF()">
                        <i class="bi bi-bug"></i> Kirim tanpa CSRF Token
                    </button>
                    
                    <div id="ajaxFailResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>

            {{-- Results History --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Riwayat Request
                    </h5>
                    <span class="badge bg-secondary">{{ count($ajaxResults) }}</span>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @forelse($ajaxResults as $result)
                        <div class="border border-success rounded p-2 mb-2 bg-success bg-opacity-10 small">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $result['message'] }}</strong>
                                <small class="text-muted">{{ $result['time'] }}</small>
                            </div>
                            <span class="badge bg-info">{{ $result['method'] }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox"></i> Belum ada request
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Code Display --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-code-slash"></i> Kode yang Digunakan
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded small"><code>// Dengan CSRF Token ✓
fetch('{{ route("csrf-lab.ajax-action") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document
            .querySelector('meta[name="csrf-token"]')
            .content
    },
    body: JSON.stringify({ message: 'Hello' })
});

// Tanpa CSRF Token ✗
fetch('{{ route("csrf-lab.ajax-action") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        // Tidak ada X-CSRF-TOKEN!
    },
    body: JSON.stringify({ message: 'Hello' })
}); // → 419 Page Expired</code></pre>
                </div>
            </div>
        </div>
    </div>

    {{-- Cookie-Based Alternative --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-cookie"></i> Alternatif: XSRF-TOKEN Cookie
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Laravel juga set cookie XSRF-TOKEN:</h6>
                    <p class="small text-muted">
                        Laravel otomatis set cookie <code>XSRF-TOKEN</code> yang bisa dibaca JavaScript.
                        Framework seperti Angular dan Axios bisa auto-read cookie ini.
                    </p>
                    <pre class="bg-light p-3 rounded small"><code>// Cookie yang di-set Laravel
Set-Cookie: XSRF-TOKEN=eyJpdiI6I...; Path=/</code></pre>
                </div>
                <div class="col-md-6">
                    <h6>Axios auto-read cookie:</h6>
                    <pre class="bg-light p-3 rounded small"><code>// Axios (dengan withCredentials)
axios.defaults.withCredentials = true;

// Axios akan otomatis:
// 1. Baca cookie XSRF-TOKEN
// 2. Kirim sebagai header X-XSRF-TOKEN</code></pre>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="bi bi-info-circle"></i>
                        Laravel menerima token dari header <code>X-CSRF-TOKEN</code> atau <code>X-XSRF-TOKEN</code>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Checklist --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-clipboard-check"></i> CSRF Checklist
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success"><i class="bi bi-check-circle"></i> Yang HARUS dilakukan:</h6>
                    <ul class="small">
                        <li>Gunakan <code>@@csrf</code> di SEMUA form POST/PUT/DELETE</li>
                        <li>Tambahkan meta tag csrf-token di layout</li>
                        <li>Kirim X-CSRF-TOKEN header untuk AJAX request</li>
                        <li>Biarkan VerifyCsrfToken middleware aktif</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger"><i class="bi bi-x-circle"></i> Yang JANGAN dilakukan:</h6>
                    <ul class="small">
                        <li>Disable CSRF middleware untuk route web</li>
                        <li>Exclude route sensitif dari CSRF check</li>
                        <li>Menyimpan CSRF token di localStorage (gunakan session)</li>
                        <li>Menganggap GET request aman (gunakan POST untuk aksi)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('csrf-lab.protection-demo') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Demo Protection
        </a>
        <a href="{{ route('csrf-lab.index') }}" class="btn btn-primary">
            <i class="bi bi-house"></i> Kembali ke Menu
        </a>
    </div>
</div>

@push('scripts')
<script>
    function testAjaxWithCSRF() {
        const message = document.getElementById('ajaxMessageSuccess').value;
        const resultDiv = document.getElementById('ajaxSuccessResult');
        
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="alert alert-info py-2"><i class="bi bi-hourglass-split"></i> Sending...</div>';
        
        fetch('{{ route("csrf-lab.ajax-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = `
                <div class="alert alert-success py-2">
                    <i class="bi bi-check-circle"></i> <strong>Success!</strong><br>
                    <small>Message: ${data.data}</small><br>
                    <small>Time: ${data.time}</small>
                </div>`;
            // Reload setelah 1 detik untuk update history
            setTimeout(() => location.reload(), 1000);
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger py-2">
                    <i class="bi bi-x-circle"></i> Error: ${error.message}
                </div>`;
        });
    }
    
    function testAjaxWithoutCSRF() {
        const resultDiv = document.getElementById('ajaxFailResult');
        
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="alert alert-info py-2"><i class="bi bi-hourglass-split"></i> Sending without CSRF...</div>';
        
        fetch('{{ route("csrf-lab.ajax-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                // Sengaja TIDAK ada X-CSRF-TOKEN
            },
            body: JSON.stringify({ message: 'Test tanpa CSRF' })
        })
        .then(response => {
            if (response.status === 419) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success py-2">
                        <i class="bi bi-shield-check"></i> <strong>CSRF Protection Working!</strong><br>
                        <small>Status: 419 Page Expired</small><br>
                        <small class="text-muted">Request ditolak karena tidak ada CSRF token.</small>
                    </div>`;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning py-2">
                        Unexpected status: ${response.status}
                    </div>`;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-success py-2">
                    <i class="bi bi-shield-check"></i> <strong>CSRF Protection Working!</strong><br>
                    <small>Request blocked.</small>
                </div>`;
        });
    }
</script>
@endpush
@endsection
