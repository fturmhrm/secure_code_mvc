@extends('layouts.app')

@section('title', 'Lab Index - Authentication')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        {{-- Header --}}
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">
                <i class="bi bi-shield-lock"></i>
                Lab Authentication & Password Security
            </h1>
            <p class="lead text-muted">
                Minggu 4 - Hari 1: "Who Are You? Prove It!"
            </p>
        </div>

        {{-- OWASP Reference --}}
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                <div>
                    <strong>OWASP A07:2021 - Identification and Authentication Failures</strong>
                    <p class="mb-0 small">
                        Kelemahan dalam proses autentikasi dapat menyebabkan akun user diambil alih oleh attacker.
                    </p>
                </div>
            </div>
        </div>

        {{-- Lab Cards --}}
        <div class="row g-4">
            
            {{-- Secure Auth Card --}}
            <div class="col-md-6">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check"></i> Secure Authentication
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Implementasi authentication yang mengikuti best practices security.
                        </p>
                        
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                Password hashing (bcrypt)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                Rate limiting (5 attempts/min)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                Session regeneration
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                Strong password rules
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                CSRF protection
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-success">
                                <i class="bi bi-box-arrow-in-right"></i> Login (Secure)
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-success">
                                <i class="bi bi-person-plus"></i> Register (Secure)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vulnerable Auth Card --}}
            <div class="col-md-6">
                <div class="card h-100 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Vulnerable Authentication
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Implementasi yang <strong>SENGAJA TIDAK AMAN</strong> untuk pembelajaran.
                        </p>
                        
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                Password plaintext
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                No rate limiting
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                No session regeneration
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                Weak password allowed
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                Information disclosure
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('vulnerable.login') }}" class="btn btn-danger">
                                <i class="bi bi-box-arrow-in-right"></i> Login (Vulnerable)
                            </a>
                            <a href="{{ route('vulnerable.register') }}" class="btn btn-outline-danger">
                                <i class="bi bi-person-plus"></i> Register (Vulnerable)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lab Scenarios --}}
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Skenario Lab
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-1-circle"></i> Lab 1: Brute Force</h6>
                        <p class="small text-muted">
                            Bandingkan rate limiting di secure vs vulnerable login.
                        </p>
                        <a href="{{ route('vulnerable.brute-force-stats') }}" class="btn btn-sm btn-outline-danger">
                            Lihat Stats
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-2-circle"></i> Lab 2: Password Storage</h6>
                        <p class="small text-muted">
                            Lihat bagaimana password disimpan di database.
                        </p>
                        <a href="{{ route('vulnerable.show-users') }}" class="btn btn-sm btn-outline-danger">
                            Lihat Database
                        </a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-3-circle"></i> Lab 3: Session Security</h6>
                        <p class="small text-muted">
                            Cek session ID sebelum dan sesudah login.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-4-circle"></i> Lab 4: Password Validation</h6>
                        <p class="small text-muted">
                            Coba register dengan password lemah.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comparison Link --}}
        <div class="text-center mt-4">
            <a href="{{ route('auth-lab.comparison') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-arrows-angle-expand"></i> Lihat Full Comparison
            </a>
        </div>

    </div>
</div>
@endsection
