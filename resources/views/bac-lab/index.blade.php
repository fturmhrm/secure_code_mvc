{{--
    Index View untuk Minggu 4 Hari 4: BAC/IDOR Lab
    Halaman utama yang menampilkan pilihan Secure vs Vulnerable
--}}

@extends('layouts.app')

@section('title', 'Lab Index - BAC/IDOR')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- Header --}}
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">
                <i class="bi bi-shield-exclamation text-warning"></i>
                Lab: Broken Access Control (IDOR)
            </h1>
            <p class="lead text-muted">
                Minggu 4 - Hari 4: "Who Can Access What?"
            </p>
        </div>

        {{-- OWASP Reference --}}
        <div class="alert alert-warning mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                <div>
                    <strong>OWASP A01:2025 - Broken Access Control</strong>
                    <p class="mb-0 small">
                        #1 di OWASP Top 10! IDOR (Insecure Direct Object Reference) adalah kerentanan dimana user bisa mengakses data orang lain dengan mengganti ID di URL.
                    </p>
                </div>
            </div>
        </div>

        {{-- Lab Cards --}}
        <div class="row g-4">

            {{-- Secure Card --}}
            <div class="col-md-6">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check"></i> Secure Version
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Implementasi yang menggunakan <strong>Laravel Policy</strong> untuk authorization.
                        </p>

                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                Policy-based authorization
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                authorizeResource() otomatis
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                Query scoping by role
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                403 Forbidden jika tidak berhak
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                Route model binding
                            </li>
                        </ul>

                        <div class="bg-dark text-light p-2 rounded small">
                            <code>
                                <span class="text-success">// Policy check</span><br>
                                $this->authorize('view', $ticket);
                            </code>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('bac-lab.secure.login') }}" class="btn btn-success">
                                <i class="bi bi-box-arrow-in-right"></i> Login (Secure)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vulnerable Card --}}
            <div class="col-md-6">
                <div class="card h-100 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Vulnerable Version
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Implementasi yang <strong>SENGAJA TIDAK AMAN</strong> untuk pembelajaran IDOR.
                        </p>

                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                Tidak ada authorization check
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                User bisa akses data siapapun
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                ID predictable di URL
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                Tidak ada query scoping
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-danger"></i>
                                Parameter ID langsung dipakai
                            </li>
                        </ul>

                        <div class="bg-dark text-light p-2 rounded small">
                            <code>
                                <span class="text-danger">// IDOR - Tidak ada check!</span><br>
                                $ticket = Ticket::find($id);
                            </code>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('bac-lab.vulnerable.login') }}" class="btn btn-danger">
                                <i class="bi bi-box-arrow-in-right"></i> Login (Vulnerable)
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
                        <h6><i class="bi bi-1-circle text-danger"></i> Lab 1: IDOR Attack</h6>
                        <p class="small text-muted">
                            Login sebagai <code>attacker@test.com</code> dan coba akses ticket milik <code>victim@test.com</code> dengan mengganti ID di URL.
                        </p>
                        <code class="d-block bg-light p-2 rounded small">
                            /bac-lab/vulnerable/tickets/<strong>6</strong> (victim's ticket)
                        </code>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-2-circle text-success"></i> Lab 2: Policy Protection</h6>
                        <p class="small text-muted">
                            Coba hal yang sama di versi secure. Anda akan mendapat <code>403 Forbidden</code>.
                        </p>
                        <code class="d-block bg-light p-2 rounded small">
                            /bac-lab/secure/tickets/<strong>6</strong> → 403 FORBIDDEN
                        </code>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-3-circle text-warning"></i> Lab 3: Index Page</h6>
                        <p class="small text-muted">
                            Bandingkan halaman index - vulnerable menampilkan <strong>semua</strong> ticket, secure hanya milik sendiri.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-4-circle text-info"></i> Lab 4: Update/Delete</h6>
                        <p class="small text-muted">
                            Di vulnerable, user bisa update/delete ticket siapapun!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Test Accounts --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-person-badge"></i> Test Accounts
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Tickets (ID)</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-warning">
                            <td><code>victim@test.com</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge bg-secondary">user</span></td>
                            <td><strong>#6, #7</strong></td>
                            <td>TARGET - Data sensitif</td>
                        </tr>
                        <tr class="table-danger">
                            <td><code>attacker@test.com</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge bg-secondary">user</span></td>
                            <td>#8, #9</td>
                            <td>ATTACKER - Coba IDOR</td>
                        </tr>
                        <tr>
                            <td><code>admin@wikrama.sch.id</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge bg-primary">admin</span></td>
                            <td>#10</td>
                            <td>Akses semua (Admin)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Comparison Link --}}
        <div class="text-center mt-4">
            <a href="{{ route('bac-lab.comparison') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-arrows-angle-expand"></i> Lihat Full Comparison
            </a>
        </div>

    </div>
</div>
@endsection
