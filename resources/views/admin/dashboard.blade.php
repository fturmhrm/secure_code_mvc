@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="bi bi-speedometer2"></i> Admin Dashboard
            </h1>
            <span class="badge bg-danger fs-6">Admin Only</span>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                                <small>Total Users</small>
                            </div>
                            <i class="bi bi-people display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['total_tickets'] }}</h4>
                                <small>Total Tickets</small>
                            </div>
                            <i class="bi bi-ticket display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['open_tickets'] }}</h4>
                                <small>Open Tickets</small>
                            </div>
                            <i class="bi bi-exclamation-circle display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $stats['unassigned_tickets'] }}</h4>
                                <small>Unassigned</small>
                            </div>
                            <i class="bi bi-person-x display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-lightning"></i> Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                                <i class="bi bi-people"></i> Manage Users
                            </a>
                            <a href="{{ route('admin.tickets') }}" class="btn btn-outline-info">
                                <i class="bi bi-ticket-detailed"></i> All Tickets
                            </a>
                            <a href="{{ route('admin.tickets', ['unassigned' => true]) }}" class="btn btn-outline-warning">
                                <i class="bi bi-person-x"></i> Unassigned Tickets ({{ $stats['unassigned_tickets'] }})
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock-history"></i> Recent Tickets
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentTickets as $ticket)
                            <a href="{{ route('tickets.show', $ticket) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>#{{ $ticket->id }}</strong> - {{ Str::limit($ticket->title, 30) }}
                                        <br>
                                        <small class="text-muted">
                                            by {{ $ticket->user->name }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge status-{{ $ticket->status }}">
                                            {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="list-group-item text-muted">
                                Belum ada ticket
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Security Note --}}
        <div class="alert alert-info">
            <i class="bi bi-shield-check"></i>
            <strong>Security Note:</strong> Halaman ini dilindungi oleh Gate <code>access-admin</code>. 
            Hanya user dengan role <code>admin</code> yang dapat mengakses.
        </div>
    </div>
</div>
@endsection
