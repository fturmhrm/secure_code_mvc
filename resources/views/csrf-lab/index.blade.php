{{-- ============================================ --}}
{{-- CSRF LAB: Index/Menu --}}
{{-- Materi Minggu 3 - Hari 3: CSRF Protection --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Lab CSRF Protection')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header --}}
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-shield-lock text-primary"></i>
                    Lab CSRF Protection
                </h1>
                <p class="lead text-muted">
                    Minggu 3 - Hari 3: Cross-Site Request Forgery
                </p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Definisi CSRF --}}
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle"></i> Apa itu CSRF?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lead">
                        <strong>Cross-Site Request Forgery (CSRF)</strong> adalah serangan yang memaksa
                        pengguna yang sudah login untuk melakukan aksi yang <span class="text-danger fw-bold">TIDAK DIINGINKAN</span>
                        pada aplikasi web.
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-danger text-white rounded-circle p-2 me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Dampak CSRF</h6>
                                    <ul class="small text-muted mb-0">
                                        <li>Transfer uang tanpa izin</li>
                                        <li>Ubah email/password akun</li>
                                        <li>Hapus data penting</li>
                                        <li>Post konten berbahaya</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-success text-white rounded-circle p-2 me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Solusi: CSRF Token</h6>
                                    <ul class="small text-muted mb-0">
                                        <li>Token random per session</li>
                                        <li>Disisipkan di setiap form</li>
                                        <li>Divalidasi saat submit</li>
                                        <li>Attacker tidak bisa tahu token</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CSRF vs XSS --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-arrows-expand"></i> CSRF vs XSS
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Aspek</th>
                                    <th width="35%">CSRF</th>
                                    <th width="35%">XSS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Target</strong></td>
                                    <td>Browser trust pada website</td>
                                    <td>Server trust pada browser</td>
                                </tr>
                                <tr>
                                    <td><strong>Kemampuan</strong></td>
                                    <td>Hanya WRITE (melakukan aksi)</td>
                                    <td>READ dan WRITE</td>
                                </tr>
                                <tr>
                                    <td><strong>Login Required?</strong></td>
                                    <td>Ya, victim harus sudah login</td>
                                    <td>Tidak selalu</td>
                                </tr>
                                <tr>
                                    <td><strong>Protection</strong></td>
                                    <td>CSRF Token</td>
                                    <td>Output Encoding</td>
                                </tr>
                                <tr>
                                    <td><strong>Request Origin</strong></td>
                                    <td>Cross-domain (attacker.com)</td>
                                    <td>Same-domain (target.com)</td>
                                </tr>
                            </tbody>
                        </table>
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
                                <i class="bi bi-lightbulb"></i> Cara Kerja CSRF
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Pelajari bagaimana serangan CSRF bekerja dan
                                mengapa CSRF token bisa mencegahnya.
                            </p>
                            <ul class="small">
                                <li>Flow serangan CSRF</li>
                                <li>Mengapa browser kirim cookie otomatis</li>
                                <li>Same-Origin Policy</li>
                                <li>Mekanisme CSRF token</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('csrf-lab.how-it-works') }}" class="btn btn-info w-100">
                                <i class="bi bi-book"></i> Pelajari
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Attack Demo --}}
                <div class="col-md-6">
                    <div class="card h-100 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-bug"></i> Demo Serangan CSRF
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Simulasi serangan CSRF pada fitur transfer uang.
                                Lihat bagaimana attacker bisa mencuri uang!
                            </p>
                            <ul class="small">
                                <li>Form transfer (vulnerable)</li>
                                <li>Simulasi halaman attacker</li>
                                <li>Lihat transfer yang terjadi</li>
                            </ul>
                            <div class="alert alert-danger py-1 small mb-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                Hanya untuk pembelajaran!
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('csrf-lab.attack-demo') }}" class="btn btn-danger w-100">
                                <i class="bi bi-play-circle"></i> Coba Demo
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Protection Demo --}}
                <div class="col-md-6">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-check"></i> Implementasi CSRF Protection
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Pelajari cara mengimplementasikan CSRF protection
                                yang benar di form Laravel.
                            </p>
                            <ul class="small">
                                <li>Directive @@csrf (recommended)</li>
                                <li>Helper csrf_field() dan csrf_token()</li>
                                <li>Method spoofing (PUT/DELETE)</li>
                                <li>Konfigurasi middleware</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('csrf-lab.protection-demo') }}" class="btn btn-success w-100">
                                <i class="bi bi-book"></i> Pelajari Implementasi
                            </a>
                        </div>
                    </div>
                </div>

                {{-- AJAX Demo --}}
                <div class="col-md-6">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-lightning"></i> CSRF untuk AJAX
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Implementasi CSRF token untuk request AJAX/Fetch.
                            </p>
                            <ul class="small">
                                <li>Meta tag csrf-token</li>
                                <li>Header X-CSRF-TOKEN</li>
                                <li>Axios default header</li>
                                <li>Fetch API dengan token</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('csrf-lab.ajax-demo') }}" class="btn btn-warning w-100">
                                <i class="bi bi-play-circle"></i> Coba Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Reference --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-code-slash"></i> Quick Reference
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Form dengan CSRF:</h6>
                            <pre class="bg-dark text-light p-3 rounded small"><code>&lt;form method="POST" action="/transfer"&gt;
    @@csrf  {{-- Wajib! --}}
    &lt;input name="amount" ...&gt;
    &lt;button&gt;Transfer&lt;/button&gt;
&lt;/form&gt;</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>AJAX dengan CSRF:</h6>
                            <pre class="bg-dark text-light p-3 rounded small"><code>fetch('/api/action', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document
            .querySelector('meta[name="csrf-token"]')
            .content
    },
    body: JSON.stringify(data)
});</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reset Button --}}
            <div class="text-center mt-4">
                <form action="{{ route('csrf-lab.reset') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Semua Demo Data
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
