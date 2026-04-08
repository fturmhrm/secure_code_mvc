@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container-fluid py-4">
    {{-- Hero Section --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white shadow-lg" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 50%, #0a58ca 100%);">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-shield-lock display-1 mb-3"></i>
                    <h1 class="display-5 fw-bold mb-3">Secure Ticketing Lab</h1>
                    <p class="lead mb-4 opacity-75">
                        Platform pembelajaran keamanan web untuk SMK Wikrama Bogor
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('tickets.index') }}" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-ticket-detailed me-2"></i> Lihat Tickets
                        </a>
                        <a href="{{ route('security-testing.index') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-shield-shaded me-2"></i> Security Testing
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Access Cards --}}
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-semibold mb-3">
                <i class="bi bi-lightning me-2 text-warning"></i> Quick Access
            </h4>
        </div>
    </div>

    <div class="row g-4 mb-5">
        {{-- Blade Templating --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="bi bi-code-slash text-info fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Blade Templating</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Pelajari template engine Laravel Blade: directives, components, includes, dan stacks.
                    </p>
                    <a href="{{ route('demo-blade.index') }}" class="btn btn-outline-info btn-sm">
                        Buka Demo <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- XSS Lab --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="bi bi-shield-exclamation text-danger fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">XSS Lab</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Eksplor Cross-Site Scripting: Reflected, Stored, dan DOM-based XSS attacks.
                    </p>
                    <a href="{{ route('xss-lab.index') }}" class="btn btn-outline-danger btn-sm">
                        Buka Lab <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Validation Lab --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Input Validation</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Bandingkan form tanpa validasi vs dengan validasi Laravel yang proper.
                    </p>
                    <a href="{{ route('validation-lab.index') }}" class="btn btn-outline-success btn-sm">
                        Buka Lab <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- CSRF Lab --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-key text-warning fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">CSRF Lab</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Pelajari Cross-Site Request Forgery dan proteksi dengan CSRF token.
                    </p>
                    <a href="{{ route('csrf-lab.index') }}" class="btn btn-outline-warning btn-sm">
                        Buka Lab <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- SQLI Lab --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-database text-secondary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">SQL Injection Lab</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Eksplor SQL Injection: teknik serangan dan cara mencegahnya dengan prepared statements.
                    </p>
                    <a href="{{ route('sqli-lab.index') }}" class="btn btn-outline-secondary btn-sm">
                        Buka Lab <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Security Testing --}}
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-shield-shaded text-primary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Security Testing</h5>
                    </div>
                    <p class="card-text text-muted small">
                        Tool untuk menguji keamanan: XSS, CSRF, Security Headers & Audit.
                    </p>
                    <a href="{{ route('security-testing.index') }}" class="btn btn-outline-primary btn-sm">
                        Buka Tools <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>


    {{-- Info Section --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h5 class="fw-semibold mb-2">
                                <i class="bi bi-info-circle me-2 text-primary"></i> Tentang Platform Ini
                            </h5>
                            <p class="text-muted mb-0 small">
                                Secure Ticketing Lab adalah platform edukasi untuk mempelajari keamanan aplikasi web.
                                Dibangun dengan Laravel 12 dan Bootstrap 5, platform ini menyediakan berbagai lab
                                interaktif untuk memahami serangan web seperti XSS, CSRF, SQL Injection, serta
                                cara mengamankannya. <strong>Gunakan hanya untuk pembelajaran!</strong>
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            <span class="badge bg-primary me-1">Laravel 12</span>
                            <span class="badge bg-secondary me-1">Bootstrap 5</span>
                            <span class="badge bg-info">PostgreSQL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
