{{-- ============================================ --}}
{{-- Dashboard with RBAC Information --}}
{{-- Minggu 4 Hari 2: Authorization --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Dashboard - Secure')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Main Dashboard --}}
        <div class="col-lg-8">
            <div class="card secure-border mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2"></i> Dashboard (Secure)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5 class="alert-heading">
                            <i class="bi bi-check-circle"></i>
                            Selamat datang, {{ Auth::user()->name }}!
                        </h5>
                        <p>Anda berhasil login dengan <strong>Secure Authentication</strong>.</p>
                        <hr>
                        <p class="mb-0">
                            Role Anda:
                            @if(Auth::user()->isAdmin())
                                <span class="badge bg-danger fs-6">👑 Admin</span>
                            @elseif(Auth::user()->isStaff())
                                <span class="badge bg-warning text-dark fs-6">⚡ Staff</span>
                            @else
                                <span class="badge bg-primary fs-6">👤 User</span>
                            @endif
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-person"></i> User Info
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ Auth::user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ Auth::user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Role:</strong></td>
                                            <td>
                                                <span class="badge
                                                    @if(Auth::user()->role === 'admin') bg-danger
                                                    @elseif(Auth::user()->role === 'staff') bg-warning text-dark
                                                    @else bg-primary
                                                    @endif">
                                                    {{ ucfirst(Auth::user()->role) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Password:</strong></td>
                                            <td>
                                                <code class="text-muted">
                                                    {{ Str::limit(Auth::user()->password, 30) }}...
                                                </code>
                                                <br>
                                                <span class="badge bg-success">HASHED (bcrypt)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Session ID:</strong></td>
                                            <td>
                                                <code class="small">{{ Str::limit(session()->getId(), 20) }}...</code>
                                                <span class="badge bg-success">Regenerated</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-shield-check"></i> Security Status
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Rate Limiting
                                            <span class="badge bg-success">✓ Enabled</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Password Hashing
                                            <span class="badge bg-success">✓ bcrypt</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Session Regeneration
                                            <span class="badge bg-success">✓ Done</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            CSRF Protection
                                            <span class="badge bg-success">✓ Active</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            RBAC Authorization
                                            <span class="badge bg-success">✓ Active</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Quick Actions based on Role --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Tickets - All users --}}
                        <div class="col-md-4">
                            <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-ticket fs-3 d-block mb-2"></i>
                                <span>My Tickets</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('tickets.create') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-plus-circle fs-3 d-block mb-2"></i>
                                <span>Create Ticket</span>
                            </a>
                        </div>

                        {{-- Admin Only --}}
                        @can('access-admin')
                        <div class="col-md-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger w-100 py-3">
                                <i class="bi bi-gear fs-3 d-block mb-2"></i>
                                <span>Admin Panel</span>
                            </a>
                        </div>
                        @endcan

                        @can('view-reports')
                        <div class="col-md-4">
                            <a href="{{ route('admin.reports') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-graph-up fs-3 d-block mb-2"></i>
                                <span>View Reports</span>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Permissions --}}
        <div class="col-lg-4">
            {{-- Your Permissions Card --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-key"></i> Your Permissions
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Berdasarkan role <strong>{{ ucfirst(Auth::user()->role) }}</strong>, Anda memiliki akses ke:
                    </p>
                    <ul class="list-group">
                        {{-- Universal permissions --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-check-circle text-success"></i> Buat Tiket Baru</span>
                            <span class="badge bg-success">✓</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-check-circle text-success"></i> Lihat Tiket Sendiri</span>
                            <span class="badge bg-success">✓</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-check-circle text-success"></i> Edit Tiket Sendiri</span>
                            <span class="badge bg-success">✓</span>
                        </li>

                        {{-- Staff/Admin permissions --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                @can('view-all-tickets')
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-danger"></i>
                                @endcan
                                Lihat Semua Tiket
                            </span>
                            @can('view-all-tickets')
                                <span class="badge bg-success">✓</span>
                            @else
                                <span class="badge bg-secondary">✗</span>
                            @endcan
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                @if(Auth::user()->hasAnyRole(['admin', 'staff']))
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-danger"></i>
                                @endif
                                Update Status Tiket
                            </span>
                            @if(Auth::user()->hasAnyRole(['admin', 'staff']))
                                <span class="badge bg-success">✓</span>
                            @else
                                <span class="badge bg-secondary">✗</span>
                            @endif
                        </li>

                        {{-- Admin only permissions --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                @can('access-admin')
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-danger"></i>
                                @endcan
                                Akses Admin Panel
                            </span>
                            @can('access-admin')
                                <span class="badge bg-success">✓</span>
                            @else
                                <span class="badge bg-secondary">✗</span>
                            @endcan
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                @can('manage-users')
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-danger"></i>
                                @endcan
                                Kelola User
                            </span>
                            @can('manage-users')
                                <span class="badge bg-success">✓</span>
                            @else
                                <span class="badge bg-secondary">✗</span>
                            @endcan
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                @can('assign-tickets')
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-x-circle text-danger"></i>
                                @endcan
                                Assign Tiket ke Staff
                            </span>
                            @can('assign-tickets')
                                <span class="badge bg-success">✓</span>
                            @else
                                <span class="badge bg-secondary">✗</span>
                            @endcan
                        </li>
                    </ul>
                </div>
            </div>

            {{-- RBAC Info Card --}}
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> RBAC Info
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <strong>Role-Based Access Control (RBAC)</strong> adalah metode kontrol akses
                        berdasarkan peran pengguna dalam organisasi.
                    </p>
                    <hr>
                    <h6>Roles di Aplikasi:</h6>
                    <ul class="list-unstyled">
                        <li>
                            <span class="badge bg-danger">Admin</span> - Full access ke semua fitur
                        </li>
                        <li>
                            <span class="badge bg-warning text-dark">Staff</span> - Menangani tiket user
                        </li>
                        <li>
                            <span class="badge bg-primary">User</span> - Membuat dan manage tiket sendiri
                        </li>
                    </ul>
                    <hr>
                    <a href="{{ route('auth-lab.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-book"></i> Pelajari lebih lanjut
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
