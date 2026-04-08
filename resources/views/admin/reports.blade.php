{{-- ============================================ --}}
{{-- Admin Reports --}}
{{-- Minggu 4 Hari 2: Authorization Demo --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="bi bi-graph-up text-info"></i>
                Reports & Analytics
            </h2>
            <p class="text-muted mb-0">Statistik dan performa sistem</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        {{-- Ticket Statistics --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-ticket"></i> Ticket Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-primary">{{ $ticketStats['total'] }}</h2>
                                    <p class="mb-0 text-muted">Total Tickets</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-info">{{ $ticketStats['open'] }}</h2>
                                    <p class="mb-0 text-muted">Open</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-warning">{{ $ticketStats['in_progress'] }}</h2>
                                    <p class="mb-0 text-muted">In Progress</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-success">{{ $ticketStats['resolved'] }}</h2>
                                    <p class="mb-0 text-muted">Resolved</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h2 class="text-secondary">{{ $ticketStats['closed'] }}</h2>
                                    <p class="mb-0 text-muted">Closed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Priority Breakdown --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Priority Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <h2 class="text-danger">{{ $priorityStats['high'] }}</h2>
                                    <p class="mb-0">🔴 High Priority</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h2 class="text-warning">{{ $priorityStats['medium'] }}</h2>
                                    <p class="mb-0">🟡 Medium Priority</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h2 class="text-success">{{ $priorityStats['low'] }}</h2>
                                    <p class="mb-0">🟢 Low Priority</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Staff Performance --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-people"></i> Staff Performance
                    </h5>
                </div>
                <div class="card-body">
                    @if($staffPerformance->isEmpty())
                        <p class="text-muted text-center">No staff data available</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Role</th>
                                        <th>Assigned</th>
                                        <th>Resolved</th>
                                        <th>Resolution Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffPerformance as $staff)
                                        <tr>
                                            <td>
                                                <strong>{{ $staff->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $staff->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $staff->role === 'admin' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                    {{ ucfirst($staff->role) }}
                                                </span>
                                            </td>
                                            <td>{{ $staff->total_assigned }}</td>
                                            <td>{{ $staff->resolved_count }}</td>
                                            <td>
                                                @php
                                                    $rate = $staff->total_assigned > 0
                                                        ? round(($staff->resolved_count / $staff->total_assigned) * 100)
                                                        : 0;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $rate >= 70 ? 'bg-success' : ($rate >= 40 ? 'bg-warning' : 'bg-danger') }}"
                                                         role="progressbar"
                                                         style="width: {{ $rate }}%"
                                                         aria-valuenow="{{ $rate }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $rate }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- User Statistics (Admin Only) --}}
            @if($userStats)
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge"></i> User Statistics
                            <span class="badge bg-light text-danger ms-2">Admin Only</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Users
                                <span class="badge bg-primary rounded-pill">{{ $userStats['total'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><span class="badge bg-danger">Admin</span></span>
                                <span class="badge bg-secondary rounded-pill">{{ $userStats['admins'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><span class="badge bg-warning text-dark">Staff</span></span>
                                <span class="badge bg-secondary rounded-pill">{{ $userStats['staff'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><span class="badge bg-primary">User</span></span>
                                <span class="badge bg-secondary rounded-pill">{{ $userStats['users'] }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Authorization Info --}}
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check"></i> Access Info
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Halaman ini dilindungi oleh:
                    </p>
                    <ul class="list-unstyled">
                        <li>
                            <code>Gate::define('view-reports')</code>
                            <br>
                            <small class="text-muted">Hanya admin & staff yang bisa akses</small>
                        </li>
                    </ul>
                    <hr>
                    <p class="small mb-0">
                        Your Role:
                        <span class="badge {{ auth()->user()->isAdmin() ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
