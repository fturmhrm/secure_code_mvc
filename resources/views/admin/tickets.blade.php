@extends('layouts.app')

@section('title', 'All Tickets - Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="bi bi-ticket-detailed"></i> All Tickets
            </h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.tickets') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="">All Priority</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Assignment</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" name="unassigned" value="1" 
                                   id="unassigned" {{ request('unassigned') ? 'checked' : '' }}>
                            <label class="form-check-label" for="unassigned">Unassigned Only</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.tickets') }}" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tickets Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>#{{ $ticket->id }}</td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}">
                                            {{ Str::limit($ticket->title, 40) }}
                                        </a>
                                    </td>
                                    <td>{{ $ticket->user->name }}</td>
                                    <td>
                                        @if($ticket->assignee)
                                            <span class="badge bg-info">{{ $ticket->assignee->name }}</span>
                                        @else
                                            <span class="badge bg-danger">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="priority-{{ $ticket->priority }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $ticket->status }}">
                                            {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            {{-- Assign Button --}}
                                            @if(!$ticket->assigned_to)
                                                <button type="button" class="btn btn-outline-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#assignModal{{ $ticket->id }}">
                                                    <i class="bi bi-person-plus"></i>
                                                </button>
                                            @endif
                                            
                                            @can('delete', $ticket)
                                                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Yakin hapus ticket ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                {{-- Assign Modal --}}
                                @if(!$ticket->assigned_to)
                                    <div class="modal fade" id="assignModal{{ $ticket->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Assign Ticket #{{ $ticket->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>{{ $ticket->title }}</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Assign ke:</label>
                                                            <select name="assigned_to" class="form-select" required>
                                                                <option value="">-- Pilih Staff --</option>
                                                                @foreach($staffList as $staff)
                                                                    <option value="{{ $staff->id }}">
                                                                        {{ $staff->name }} ({{ $staff->role }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Assign</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-4"></i>
                                        <p class="mt-2">Tidak ada ticket ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                {{ $tickets->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
