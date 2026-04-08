{{--
    Authorization Lab Index - Minggu 4 Hari 2

    Menu utama untuk lab authorization dengan penjelasan konsep
--}}

@extends('layouts.app')

@section('title', 'Authorization Lab')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- Header --}}
        <div class="text-center mb-5">
            <span class="badge bg-primary px-4 py-2 fs-5 mb-3">
                <i class="bi bi-shield-lock"></i> MINGGU 4 - HARI 2
            </span>
            <h1 class="display-5 fw-bold">Authorization Lab</h1>
            <p class="lead text-muted">
                Pelajari perbedaan Authentication vs Authorization dan implementasi RBAC di Laravel
            </p>
        </div>

        {{-- Authentication vs Authorization --}}
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-question-circle"></i> Authentication vs Authorization
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-person-check"></i> Authentication
                            </div>
                            <div class="card-body">
                                <p class="fw-bold text-success">"Siapa kamu?"</p>
                                <ul class="mb-0">
                                    <li>Verifikasi identitas user</li>
                                    <li>Login dengan email & password</li>
                                    <li>Session management</li>
                                    <li>Remember me tokens</li>
                                </ul>
                                <hr>
                                <small class="text-muted">
                                    <i class="bi bi-lightbulb"></i> Dipelajari di Hari 1 (Auth Lab)
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-shield-lock"></i> Authorization
                            </div>
                            <div class="card-body">
                                <p class="fw-bold text-primary">"Apa yang boleh kamu lakukan?"</p>
                                <ul class="mb-0">
                                    <li>Cek hak akses user</li>
                                    <li>Role-Based Access Control (RBAC)</li>
                                    <li>Gates & Policies</li>
                                    <li>Middleware protection</li>
                                </ul>
                                <hr>
                                <small class="text-muted">
                                    <i class="bi bi-lightbulb"></i> Dipelajari di Hari 2 (Lab ini!)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RBAC Explanation --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-diagram-3"></i> Role-Based Access Control (RBAC)
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <div class="display-4 text-danger mb-2">
                                    <i class="bi bi-person-gear"></i>
                                </div>
                                <h5>Admin</h5>
                                <span class="badge bg-danger mb-2">Full Access</span>
                                <ul class="list-unstyled text-start small">
                                    <li><i class="bi bi-check text-success"></i> Kelola semua tiket</li>
                                    <li><i class="bi bi-check text-success"></i> Assign tiket ke staff</li>
                                    <li><i class="bi bi-check text-success"></i> Akses admin panel</li>
                                    <li><i class="bi bi-check text-success"></i> Lihat semua reports</li>
                                    <li><i class="bi bi-check text-success"></i> Manage users</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <div class="display-4 text-primary mb-2">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <h5>Staff</h5>
                                <span class="badge bg-primary mb-2">Limited Access</span>
                                <ul class="list-unstyled text-start small">
                                    <li><i class="bi bi-check text-success"></i> Lihat semua tiket</li>
                                    <li><i class="bi bi-check text-success"></i> Update tiket assigned</li>
                                    <li><i class="bi bi-check text-success"></i> Lihat reports</li>
                                    <li><i class="bi bi-x text-danger"></i> Tidak bisa assign tiket</li>
                                    <li><i class="bi bi-x text-danger"></i> Tidak ada admin panel</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary h-100">
                            <div class="card-body">
                                <div class="display-4 text-secondary mb-2">
                                    <i class="bi bi-person"></i>
                                </div>
                                <h5>User</h5>
                                <span class="badge bg-secondary mb-2">Basic Access</span>
                                <ul class="list-unstyled text-start small">
                                    <li><i class="bi bi-check text-success"></i> Buat tiket baru</li>
                                    <li><i class="bi bi-check text-success"></i> Lihat tiket sendiri</li>
                                    <li><i class="bi bi-check text-success"></i> Edit tiket sendiri*</li>
                                    <li><i class="bi bi-x text-danger"></i> Tidak lihat tiket lain</li>
                                    <li><i class="bi bi-x text-danger"></i> Tidak ada reports</li>
                                </ul>
                                <small class="text-muted">*Hanya jika status open</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Cards --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100 border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-box-arrow-in-right display-4 text-primary"></i>
                        <h4 class="mt-3">Login & Test Roles</h4>
                        <p class="text-muted">
                            Login dengan berbagai role untuk melihat perbedaan akses
                        </p>
                        <a href="{{ route('authorization-lab.login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login dengan Test Account
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-code-slash display-4 text-success"></i>
                        <h4 class="mt-3">Lihat Implementasi</h4>
                        <p class="text-muted">
                            Pelajari cara implementasi Gates, Policies, dan Middleware
                        </p>
                        <a href="{{ route('authorization-lab.implementation') }}" class="btn btn-success">
                            <i class="bi bi-code-slash"></i> Lihat Code
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Reference --}}
        <div class="card">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-bookmark"></i> Quick Reference - Laravel Authorization
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><i class="bi bi-gate text-primary"></i> Gates</h6>
                        <pre class="bg-light p-2 small"><code>// Define Gate
Gate::define('access-admin',
    fn(User $u) => $u->isAdmin()
);

// Check Gate
Gate::allows('access-admin');
@can('access-admin')
    ...
@endcan</code></pre>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="bi bi-file-earmark-code text-success"></i> Policies</h6>
                        <pre class="bg-light p-2 small"><code>// TicketPolicy.php
public function update(
    User $user,
    Ticket $ticket
): bool {
    return $user->isAdmin()
        || $ticket->user_id === $user->id;
}

// Controller
$this->authorize('update', $ticket);</code></pre>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="bi bi-shield text-warning"></i> Middleware</h6>
                        <pre class="bg-light p-2 small"><code>// Route protection
Route::middleware('role:admin,staff')
    ->group(function () {
        Route::get('/reports', ...);
    });

// RoleMiddleware.php
if (!$user->hasAnyRole($roles)) {
    abort(403);
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
