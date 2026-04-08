{{-- ============================================ --}}
{{-- CSRF LAB: Protection Demo --}}
{{-- Cara Implementasi CSRF Protection di Laravel --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Implementasi CSRF Protection - Lab')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('csrf-lab.index') }}">CSRF Lab</a></li>
            <li class="breadcrumb-item active">Implementasi Protection</li>
        </ol>
    </nav>

    {{-- Alert --}}
    <div class="alert alert-success mb-4">
        <h5 class="alert-heading">
            <i class="bi bi-shield-check"></i> CARA IMPLEMENTASI CSRF PROTECTION
        </h5>
        <p class="mb-0">
            Halaman ini menjelaskan cara mengimplementasikan CSRF protection yang benar di Laravel.
            Semua metode di bawah ini <strong>wajib</strong> digunakan untuk melindungi form dari serangan CSRF.
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Kolom Kiri: Implementation Guide --}}
        <div class="col-lg-6 mb-4">
            {{-- Method 1: @csrf Directive --}}
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-1-circle"></i> Method 1: @@csrf Directive (Recommended)
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Cara paling mudah dan <strong>direkomendasikan</strong> untuk semua form Blade:
                    </p>

                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;form method="POST" action="/transfer"&gt;
    <span class="text-success">@@csrf</span>  {{-- Wajib untuk POST/PUT/DELETE --}}

    &lt;input name="amount" type="number"&gt;
    &lt;input name="to" type="text"&gt;
    &lt;button&gt;Transfer&lt;/button&gt;
&lt;/form&gt;</code></pre>

                    <p class="small mb-2"><strong>Output HTML yang dihasilkan:</strong></p>
                    <pre class="bg-light p-2 rounded small mb-0"><code>&lt;input type="hidden" name="_token"
       value="{{ substr(csrf_token(), 0, 20) }}..."&gt;</code></pre>
                </div>
            </div>

            {{-- Method 2: csrf_field() --}}
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-2-circle"></i> Method 2: csrf_field() Helper
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Alternatif menggunakan helper function:
                    </p>

                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;form method="POST" action="/transfer"&gt;
    <span class="text-primary">&#123;&#123; csrf_field() &#125;&#125;</span>

    &lt;!-- form fields --&gt;
&lt;/form&gt;</code></pre>
                </div>
            </div>

            {{-- Method 3: Manual Hidden Input --}}
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-3-circle"></i> Method 3: Manual Hidden Input
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Jika perlu kontrol lebih atau di luar Blade:
                    </p>

                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;form method="POST" action="/transfer"&gt;
    &lt;input type="hidden"
           name="_token"
           value="<span class="text-info">&#123;&#123; csrf_token() &#125;&#125;</span>"&gt;

    &lt;!-- form fields --&gt;
&lt;/form&gt;</code></pre>
                </div>
            </div>

            {{-- Method Spoofing --}}
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Method Spoofing (PUT/DELETE)
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        HTML form hanya support GET dan POST. Untuk PUT/PATCH/DELETE, gunakan <code>@@method</code>:
                    </p>

                    <pre class="bg-dark text-light p-3 rounded"><code>&lt;!-- Untuk UPDATE --&gt;
&lt;form method="POST" action="/tickets/1"&gt;
    @@csrf
    <span class="text-warning">@@method('PUT')</span>
    &lt;!-- form fields --&gt;
&lt;/form&gt;

&lt;!-- Untuk DELETE --&gt;
&lt;form method="POST" action="/tickets/1"&gt;
    @@csrf
    <span class="text-warning">@@method('DELETE')</span>
    &lt;button&gt;Hapus&lt;/button&gt;
&lt;/form&gt;</code></pre>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Practice & History --}}
        <div class="col-lg-6">
            {{-- Practice Form --}}
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-play-circle"></i> Coba Form dengan CSRF
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Form ini menggunakan <code>@@csrf</code>. Submit akan berhasil karena token valid.
                    </p>

                    <form action="{{ route('csrf-lab.protected-action') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Pilih Aksi</label>
                            <select name="action_type" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="update_profile">Update Profile</option>
                                <option value="change_settings">Change Settings</option>
                                <option value="delete_data">Delete Data</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi (Opsional)</label>
                            <input type="text" name="description" class="form-control"
                                   placeholder="Contoh: Update email ke baru@email.com">
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send"></i> Submit dengan CSRF
                        </button>
                    </form>

                    {{-- Token Info --}}
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            <strong>Current CSRF Token:</strong><br>
                            <code class="text-break" style="font-size: 10px;">{{ csrf_token() }}</code>
                        </small>
                    </div>
                </div>
            </div>

            {{-- Action History --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check"></i> Riwayat Aksi
                    </h5>
                    <span class="badge bg-secondary">{{ count($actions) }}</span>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                    @forelse($actions as $action)
                        <div class="border border-success rounded p-2 mb-2 bg-success bg-opacity-10 small">
                            <div class="d-flex justify-content-between">
                                <strong>{{ ucwords(str_replace('_', ' ', $action['type'])) }}</strong>
                                <small class="text-muted">{{ $action['time'] }}</small>
                            </div>
                            <div class="text-muted">{{ $action['description'] }}</div>
                            <span class="badge bg-success">CSRF Valid ✓</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox"></i> Belum ada aksi
                            <br><small>Coba submit form di atas</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Middleware Info --}}
    <div class="card mt-4 border-secondary">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="bi bi-gear"></i> Konfigurasi VerifyCsrfToken Middleware
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6><i class="bi bi-check-circle text-success"></i> Default Behavior (Laravel 12):</h6>
                    <pre class="bg-dark text-light p-3 rounded small"><code>// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // CSRF protection sudah AKTIF secara default
        // untuk semua route di 'web' middleware group
    })
    ->create();</code></pre>
                    <div class="alert alert-success py-2 small mb-0">
                        <i class="bi bi-info-circle"></i>
                        Di Laravel 12, CSRF protection otomatis aktif. Tidak perlu konfigurasi tambahan!
                    </div>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-exclamation-triangle text-warning"></i> Exclude Routes (Hati-hati!):</h6>
                    <pre class="bg-dark text-light p-3 rounded small"><code>// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'api/*',           // API stateless
        'webhook/*',       // Webhook endpoints
        'stripe/webhook',  // Payment webhooks
    ]);
})</code></pre>
                    <div class="alert alert-warning py-2 small mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Peringatan:</strong> Hanya exclude route yang memang tidak memerlukan session
                        (API dengan token auth, webhooks). Jangan exclude route sensitif!
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Key Takeaways --}}
    <div class="card mt-4 border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="bi bi-lightbulb"></i> Ringkasan Penting
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 bg-success bg-opacity-10 rounded h-100">
                        <h6 class="text-success"><i class="bi bi-check-circle"></i> WAJIB Dilakukan</h6>
                        <ul class="small mb-0">
                            <li>Tambahkan <code>@@csrf</code> di SEMUA form POST/PUT/DELETE</li>
                            <li>Gunakan <code>@@method</code> untuk PUT/DELETE</li>
                            <li>Sertakan X-CSRF-TOKEN header di AJAX request</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 bg-danger bg-opacity-10 rounded h-100">
                        <h6 class="text-danger"><i class="bi bi-x-circle"></i> JANGAN Dilakukan</h6>
                        <ul class="small mb-0">
                            <li>Exclude route sensitif dari CSRF middleware</li>
                            <li>Menonaktifkan CSRF protection secara global</li>
                            <li>Menggunakan GET untuk aksi yang mengubah data</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 bg-warning bg-opacity-10 rounded h-100">
                        <h6 class="text-warning"><i class="bi bi-info-circle"></i> Tips</h6>
                        <ul class="small mb-0">
                            <li>Token otomatis di-refresh setiap session</li>
                            <li>Error 419 = Token expired/invalid</li>
                            <li>Untuk SPA, regenerate token setelah login</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('csrf-lab.attack-demo') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Demo Serangan
        </a>
        <a href="{{ route('csrf-lab.ajax-demo') }}" class="btn btn-warning">
            CSRF untuk AJAX <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@endsection
