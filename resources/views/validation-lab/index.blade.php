{{-- ============================================ --}}
{{-- VALIDATION LAB: Index/Menu --}}
{{-- Materi Minggu 3 - Hari 2: Input Validation --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Lab Input Validation')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Header --}}
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-shield-check text-primary"></i>
                    Lab Input Validation
                </h1>
                <p class="lead text-muted">
                    Minggu 3 - Hari 2: Never Trust User Input!
                </p>
            </div>

            {{-- Alert Penting --}}
            <div class="alert alert-warning mb-4">
                <h5 class="alert-heading">
                    <i class="bi bi-exclamation-triangle-fill"></i> Prinsip Dasar
                </h5>
                <p class="mb-0">
                    <strong>NEVER TRUST USER INPUT!</strong> Semua data dari user harus dianggap 
                    <span class="text-danger fw-bold">BERBAHAYA</span> sampai divalidasi dan disanitasi.
                </p>
            </div>

            {{-- Defense in Depth --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-layers"></i> Defense in Depth
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <div class="display-6 text-warning mb-2">
                                    <i class="bi bi-browser-chrome"></i>
                                </div>
                                <h6>Layer 1: Client-Side</h6>
                                <small class="text-muted">JavaScript / HTML5</small>
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark">Untuk UX</span>
                                    <span class="badge bg-danger">Bisa Di-bypass!</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded bg-success bg-opacity-10 border-success">
                                <div class="display-6 text-success mb-2">
                                    <i class="bi bi-server"></i>
                                </div>
                                <h6>Layer 2: Server-Side</h6>
                                <small class="text-muted">Laravel Validation</small>
                                <div class="mt-2">
                                    <span class="badge bg-success">WAJIB!</span>
                                    <span class="badge bg-success">Aman</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <div class="display-6 text-info mb-2">
                                    <i class="bi bi-database"></i>
                                </div>
                                <h6>Layer 3: Database</h6>
                                <small class="text-muted">Constraints</small>
                                <div class="mt-2">
                                    <span class="badge bg-info">Last Defense</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lab Options --}}
            <div class="row">
                {{-- Vulnerable Form --}}
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-exclamation-octagon"></i> Form Vulnerable
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Form <strong>TANPA</strong> server-side validation. 
                                Hanya mengandalkan validasi client-side (HTML5/JavaScript).
                            </p>
                            <ul class="list-unstyled">
                                <li class="text-danger">
                                    <i class="bi bi-x-circle"></i> Tidak ada validasi server
                                </li>
                                <li class="text-danger">
                                    <i class="bi bi-x-circle"></i> Data invalid bisa masuk
                                </li>
                                <li class="text-danger">
                                    <i class="bi bi-x-circle"></i> Rentan serangan
                                </li>
                            </ul>
                            <div class="alert alert-danger py-2 small">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>JANGAN</strong> gunakan pattern ini di production!
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('validation-lab.vulnerable') }}" 
                               class="btn btn-danger w-100">
                                <i class="bi bi-play-circle"></i> Coba Form Vulnerable
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Secure Form --}}
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-check"></i> Form Secure
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Form <strong>DENGAN</strong> server-side validation. 
                                Menggunakan Laravel validation rules.
                            </p>
                            <ul class="list-unstyled">
                                <li class="text-success">
                                    <i class="bi bi-check-circle"></i> Validasi di server
                                </li>
                                <li class="text-success">
                                    <i class="bi bi-check-circle"></i> Data dijamin valid
                                </li>
                                <li class="text-success">
                                    <i class="bi bi-check-circle"></i> Aman dari serangan
                                </li>
                            </ul>
                            <div class="alert alert-success py-2 small">
                                <i class="bi bi-check-circle"></i>
                                <strong>SELALU</strong> gunakan pattern ini!
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('validation-lab.secure') }}" 
                               class="btn btn-success w-100">
                                <i class="bi bi-play-circle"></i> Coba Form Secure
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tipe Validasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check"></i> Tipe-Tipe Validasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipe</th>
                                    <th>Contoh Rule</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Type</strong></td>
                                    <td><code>string</code>, <code>integer</code>, <code>array</code></td>
                                    <td>Cek tipe data</td>
                                </tr>
                                <tr>
                                    <td><strong>Format</strong></td>
                                    <td><code>email</code>, <code>url</code>, <code>date</code></td>
                                    <td>Cek format data</td>
                                </tr>
                                <tr>
                                    <td><strong>Range</strong></td>
                                    <td><code>min:1</code>, <code>max:100</code>, <code>between:1,10</code></td>
                                    <td>Cek rentang nilai</td>
                                </tr>
                                <tr>
                                    <td><strong>Length</strong></td>
                                    <td><code>min:5</code>, <code>max:255</code>, <code>size:10</code></td>
                                    <td>Cek panjang string</td>
                                </tr>
                                <tr>
                                    <td><strong>Business</strong></td>
                                    <td><code>unique</code>, <code>exists</code>, <code>confirmed</code></td>
                                    <td>Cek aturan bisnis</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb"></i> Tips Validasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="bi bi-check-circle"></i> DO (Lakukan)</h6>
                            <ul class="small">
                                <li>Validasi SEMUA input, tanpa kecuali</li>
                                <li>Gunakan whitelist approach (<code>in:a,b,c</code>)</li>
                                <li>Set maximum length untuk semua string</li>
                                <li>Gunakan Form Request untuk code organization</li>
                                <li>Tulis custom messages yang user-friendly</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger"><i class="bi bi-x-circle"></i> DON'T (Jangan)</h6>
                            <ul class="small">
                                <li>Hanya mengandalkan client-side validation</li>
                                <li>Skip validasi untuk "internal" endpoint</li>
                                <li>Trust hidden fields</li>
                                <li>Gunakan blacklist approach</li>
                                <li>Tampilkan technical error ke user</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
