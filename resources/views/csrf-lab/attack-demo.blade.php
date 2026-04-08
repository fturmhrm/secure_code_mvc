{{-- ============================================ --}}
{{-- CSRF LAB: Attack Demo --}}
{{-- Simulasi serangan CSRF pada transfer uang --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Demo Serangan CSRF - Lab')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('csrf-lab.index') }}">CSRF Lab</a></li>
            <li class="breadcrumb-item active">Demo Serangan</li>
        </ol>
    </nav>

    {{-- Alert Header --}}
    <div class="alert alert-danger mb-4">
        <h5 class="alert-heading">
            <i class="bi bi-exclamation-octagon-fill"></i> HALAMAN DEMO SERANGAN
        </h5>
        <p class="mb-0">
            Halaman ini mensimulasikan serangan CSRF. Ini hanya untuk pembelajaran -
            <strong>JANGAN</strong> gunakan teknik ini untuk menyerang sistem apapun!
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('danger'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Kolom Kiri: Saldo & Simulasi --}}
        <div class="col-lg-6 mb-4">
            {{-- Saldo Card --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bank"></i> Demo Bank - Saldo Anda
                    </h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4 text-primary">
                        Rp {{ number_format($balance) }}
                    </h2>
                    <p class="text-muted mb-0">Saldo saat ini (simulasi)</p>
                </div>
            </div>

            {{-- Form Transfer LEGITIMATE --}}
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check"></i> Transfer Legitimate (dengan @@csrf)
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Form ini memiliki <code>@@csrf</code> token. Transfer dari sini adalah legitimate.
                    </p>
                    <form action="{{ route('csrf-lab.secure-transfer') }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" name="to" class="form-control"
                                       value="Rekening Teman" placeholder="Tujuan" required>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="amount" class="form-control"
                                       value="100000" min="1" placeholder="Jumlah" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-send"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ====================================================== --}}
            {{-- BAGIAN SIMULASI SERANGAN --}}
            {{-- ====================================================== --}}
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bug"></i> Simulasi Serangan CSRF
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Penjelasan Skenario --}}
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>Skenario:</strong> Anda mengunjungi website jahat (<code>evil.com</code>)
                        yang diam-diam mengirim request transfer ke bank Anda!
                    </div>

                    {{-- TEST 1: Attack ke Vulnerable Endpoint --}}
                    <div class="border border-danger rounded p-3 bg-danger bg-opacity-10 mb-3">
                        <h6 class="text-danger mb-2">
                            <i class="bi bi-unlock"></i> Test 1: Attack ke Endpoint VULNERABLE
                        </h6>
                        <p class="small text-muted mb-2">
                            Endpoint ini <strong>tidak dilindungi</strong> CSRF middleware.
                            Serangan dari halaman evil.com akan <strong class="text-danger">BERHASIL</strong>!
                        </p>

                        <a href="{{ url('/attacker-simulation/evil-page.html') }}"
                           target="_blank"
                           class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i> Buka Halaman Evil (Vulnerable)
                        </a>

                        <div class="mt-2 p-2 bg-white rounded small">
                            <strong>Expected:</strong>
                            <span class="text-danger">Transfer Rp 2.500.000 BERHASIL ke Hacker!</span>
                        </div>
                    </div>

                    {{-- TEST 2: Attack ke Protected Endpoint --}}
                    <div class="border border-success rounded p-3 bg-success bg-opacity-10">
                        <h6 class="text-success mb-2">
                            <i class="bi bi-lock"></i> Test 2: Attack ke Endpoint PROTECTED
                        </h6>
                        <p class="small text-muted mb-2">
                            Endpoint ini <strong>dilindungi</strong> CSRF middleware.
                            Serangan akan <strong class="text-success">DIBLOKIR</strong> dengan error 419!
                        </p>

                        <a href="{{ url('/attacker-simulation/evil-page-protected.html') }}"
                           target="_blank"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i> Buka Halaman Evil (Protected)
                        </a>

                        <div class="mt-2 p-2 bg-white rounded small">
                            <strong>Expected:</strong>
                            <span class="text-success">Error 419 Page Expired (serangan DIBLOKIR!)</span>
                        </div>
                    </div>

                    {{-- Kode Serangan --}}
                    <div class="mt-3">
                        <h6 class="small"><i class="bi bi-code-slash"></i> Cara Kerja Kode Serangan:</h6>
                        <pre class="bg-dark text-light p-2 rounded small mb-0"><code>&lt;!-- Form tersembunyi di halaman attacker --&gt;
&lt;form action="https://bank.com/transfer" method="POST"&gt;
    &lt;!-- TIDAK ADA CSRF token! --&gt;
    &lt;input type="hidden" name="to" value="Hacker"&gt;
    &lt;input type="hidden" name="amount" value="1000000"&gt;
&lt;/form&gt;
&lt;script&gt;document.forms[0].submit();&lt;/script&gt;</code></pre>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-info-circle"></i> Form di-submit otomatis saat halaman dibuka -
                            korban tidak perlu klik apapun!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: History --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Riwayat Transfer
                    </h5>
                    <div>
                        <span class="badge bg-secondary me-2">{{ count($transfers) }} transaksi</span>
                        <form action="{{ route('csrf-lab.reset') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @forelse($transfers as $index => $transfer)
                        @php
                            $isAttack = in_array($transfer['source'], ['csrf_attack', 'external_csrf_attack']);
                            $borderClass = $isAttack ? 'border-danger' : 'border-success';
                            $bgClass = $isAttack ? 'bg-danger' : 'bg-success';
                        @endphp
                        <div class="border {{ $borderClass }} rounded p-3 mb-3 {{ $bgClass }} bg-opacity-10">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>
                                    @if($isAttack)
                                        <span class="text-danger">
                                            <i class="bi bi-exclamation-triangle-fill"></i> CSRF ATTACK!
                                        </span>
                                    @else
                                        <span class="text-success">
                                            <i class="bi bi-check-circle-fill"></i> Legitimate
                                        </span>
                                    @endif
                                </strong>
                                <small class="text-muted">{{ $transfer['time'] }}</small>
                            </div>

                            <div class="row small">
                                <div class="col-6">
                                    <strong>Tujuan:</strong><br>
                                    <span class="{{ $isAttack ? 'text-danger' : '' }}">
                                        {{ $transfer['to'] }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <strong>Jumlah:</strong><br>
                                    <span class="text-danger">-Rp {{ number_format($transfer['amount']) }}</span>
                                </div>
                            </div>

                            <div class="mt-2 pt-2 border-top small">
                                <span class="badge {{ $isAttack ? 'bg-danger' : 'bg-success' }}">
                                    {{ $transfer['source'] }}
                                </span>
                                <span class="text-muted ms-2">
                                    Sisa: Rp {{ number_format($transfer['balance_after']) }}
                                </span>
                            </div>

                            @if(isset($transfer['warning']))
                                <div class="alert alert-danger py-1 mb-0 mt-2 small">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    {{ $transfer['warning'] }}
                                </div>
                            @endif
                            @if(isset($transfer['info']))
                                <div class="alert alert-success py-1 mb-0 mt-2 small">
                                    <i class="bi bi-shield-check"></i>
                                    {{ $transfer['info'] }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-1"></i>
                            <p class="mt-3 mb-0">Belum ada transaksi</p>
                            <small>Coba lakukan transfer atau simulasi serangan</small>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kesimpulan --}}
            <div class="card mt-3 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb"></i> Kesimpulan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Skenario</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Form dengan <code>@@csrf</code></td>
                                <td class="text-success"><i class="bi bi-check"></i> Berhasil (legitimate)</td>
                            </tr>
                            <tr>
                                <td>Attack ke endpoint <strong>tanpa</strong> CSRF protection</td>
                                <td class="text-danger"><i class="bi bi-x"></i> Berhasil (BERBAHAYA!)</td>
                            </tr>
                            <tr>
                                <td>Attack ke endpoint <strong>dengan</strong> CSRF protection</td>
                                <td class="text-success"><i class="bi bi-check"></i> Diblokir (419 Error)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('csrf-lab.how-it-works') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Cara Kerja CSRF
        </a>
        <a href="{{ route('csrf-lab.protection-demo') }}" class="btn btn-success">
            Demo Protection <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@endsection
