{{-- ============================================ --}}
{{-- VIEW: tickets/index.blade.php --}}
{{-- Menampilkan daftar tiket dengan Authorization --}}
{{-- MINGGU 4 HARI 2: @can/@cannot directives --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Daftar Tiket')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">
            <i class="bi bi-ticket-detailed"></i>
            {{-- MINGGU 4 HARI 2: Tampilan berbeda berdasarkan role --}}
            @if(auth()->user()->isUser())
                Tiket Saya
            @else
                Semua Tiket
            @endif
        </h1>
        <p class="text-muted mb-0">
            @if(auth()->user()->isAdmin())
                <span class="badge bg-danger">Admin</span> Kelola semua tiket
            @elseif(auth()->user()->isStaff())
                <span class="badge bg-primary">Staff</span> Lihat & kelola tiket assigned
            @else
                <span class="badge bg-secondary">User</span> Kelola tiket Anda
            @endif
        </p>
    </div>
    @can('create', App\Models\Ticket::class)
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Buat Tiket Baru
        </a>
    @endcan
</div>

{{-- ============================================ --}}
{{-- FILTER (Admin/Staff only) --}}
{{-- ============================================ --}}
@can('view-all-tickets')
    <div class="card mb-4">
        <div class="card-body py-2">
            <form action="{{ route('tickets.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="visually-hidden" for="status">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
@endcan

{{-- ============================================ --}}
{{-- STATISTIK RINGKAS --}}
{{-- ============================================ --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h5 class="card-title">Open</h5>
                <p class="card-text display-6">
                    {{ \App\Models\Ticket::where('status', 'open')->count() }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-info">
            <div class="card-body">
                <h5 class="card-title">In Progress</h5>
                <p class="card-text display-6">
                    {{ \App\Models\Ticket::where('status', 'in_progress')->count() }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success">
            <div class="card-body">
                <h5 class="card-title">Closed</h5>
                <p class="card-text display-6">
                    {{ \App\Models\Ticket::where('status', 'closed')->count() }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- TABEL TIKET --}}
{{-- ============================================ --}}
<div class="card">
    <div class="card-body">
        @forelse($tickets as $ticket)
            <div class="d-flex justify-content-between align-items-start border-bottom py-3
                        {{ $loop->last ? 'border-0 pb-0' : '' }}">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1 flex-wrap gap-1">
                        <h5 class="mb-0 me-2">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                {{ $ticket->title }}
                            </a>
                        </h5>
                        <span class="badge {{ $ticket->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="badge {{ $ticket->priority_badge }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                        {{-- MINGGU 4 HARI 2: Show assigned badge for staff --}}
                        @if($ticket->assigned_to === auth()->id())
                            <span class="badge bg-info">
                                <i class="bi bi-person-check"></i> Assigned to you
                            </span>
                        @endif
                    </div>
                    <p class="text-muted mb-1">
                        {{ Str::limit($ticket->description, 100) }}
                    </p>
                    <small class="text-muted">
                        <i class="bi bi-person"></i>
                        {{ $ticket->user->name ?? 'Unknown' }}
                        @if($ticket->assignee)
                            &bull;
                            <i class="bi bi-person-badge"></i>
                            Assigned: {{ $ticket->assignee->name }}
                        @endif
                        &bull;
                        <i class="bi bi-clock"></i>
                        {{ $ticket->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="ms-3 d-flex gap-1">
                    {{-- MINGGU 4 HARI 2: @can directive untuk edit --}}
                    @can('update', $ticket)
                        <a href="{{ route('tickets.edit', $ticket) }}"
                           class="btn btn-sm btn-outline-primary"
                           title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                    @endcan

                    {{-- MINGGU 4 HARI 2: @can directive untuk delete --}}
                    @can('delete', $ticket)
                        <form action="{{ route('tickets.destroy', $ticket) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus tiket ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">Belum ada tiket</p>
                @can('create', App\Models\Ticket::class)
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Buat Tiket Pertama
                    </a>
                @endcan
            </div>
        @endforelse
    </div>
</div>

{{-- Pagination --}}
@if($tickets->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $tickets->links() }}
    </div>
@endif

{{-- MINGGU 4 HARI 2: Info box untuk User biasa --}}
@if(auth()->user()->isUser())
    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle"></i>
        <strong>Info:</strong> Anda hanya dapat melihat tiket yang Anda buat sendiri.
    </div>
@endif

{{-- Authorization Info Card --}}
<div class="card mt-4 border-secondary">
    <div class="card-header bg-secondary text-white">
        <i class="bi bi-shield-lock"></i> Hak Akses Anda ({{ ucfirst(auth()->user()->role) }})
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled mb-0">
                    <li>
                        @can('create', App\Models\Ticket::class)
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-danger"></i>
                        @endcan
                        Buat Tiket Baru
                    </li>
                    <li>
                        @can('view-all-tickets')
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-danger"></i>
                        @endcan
                        Lihat Semua Tiket
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled mb-0">
                    <li>
                        @can('assign-tickets')
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-danger"></i>
                        @endcan
                        Assign Tiket ke Staff
                    </li>
                    <li>
                        @can('access-admin')
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-danger"></i>
                        @endcan
                        Akses Admin Panel
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
