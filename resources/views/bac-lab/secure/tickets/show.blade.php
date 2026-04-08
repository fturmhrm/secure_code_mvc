@extends('layouts.app')

@section('title', 'Secure - View Ticket')

@section('content')
{{-- Success Banner --}}
<div class="alert alert-success success-box">
    <h5><i class="bi bi-shield-check"></i>ACCESS GRANTED - Policy Authorized</h5>
    <p class="mb-0">
        Anda memiliki akses ke ticket ini karena
        @if(auth()->user()->isAdmin())
            Anda adalah <strong>Admin</strong>.
        @else
            ini adalah ticket <strong>milik Anda</strong>.
        @endif
    </p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-ticket"></i> Ticket #{{ $ticket->id }}
                    </h5>
                    <span class="badge bg-light text-success">
                        <i class="bi bi-shield-check"></i> Authorized
                    </span>
                </div>
            </div>
            <div class="card-body">
                <h4>{{ $ticket->title }}</h4>

                <div class="mb-3">
                    <span class="badge {{ $ticket->status_badge }}">{{ $ticket->status }}</span>
                    <span class="badge {{ $ticket->priority_badge }}">{{ $ticket->priority }}</span>
                </div>

                <div class="mb-4 p-3 bg-light rounded">
                    <h6>Description:</h6>
                    <p class="mb-0">{{ $ticket->description }}</p>
                </div>

                <div class="row text-muted small">
                    <div class="col-md-6">
                        <strong>Created:</strong> {{ $ticket->created_at->format('d M Y H:i') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Updated:</strong> {{ $ticket->updated_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                @can('update', $ticket)
                    <a href="{{ route('bac-lab.secure.tickets.edit', $ticket) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endcan
                @can('delete', $ticket)
                    <form action="{{ route('bac-lab.secure.tickets.destroy', $ticket) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Yakin hapus ticket ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                @endcan
                <a href="{{ route('bac-lab.secure.tickets.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Owner Info --}}
        <div class="card border-info mb-4">
            <div class="card-header bg-info text-white">
                <i class="bi bi-person"></i> Owner Info
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Name:</strong> {{ $ticket->user->name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $ticket->user->email }}</p>
                <p class="mb-0"><strong>Role:</strong>
                    <span class="badge {{ $ticket->user->isAdmin() ? 'bg-danger' : 'bg-secondary' }}">
                        {{ $ticket->user->role }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Policy Check Info --}}
        <div class="card">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-shield-lock"></i> Policy Check
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li>
                        @can('view', $ticket)
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-danger"></i>
                        @endcan
                        Can View
                    </li>
                    <li>
                        @can('update', $ticket)
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-danger"></i>
                        @endcan
                        Can Update
                    </li>
                    <li>
                        @can('delete', $ticket)
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-x-circle text-danger"></i>
                        @endcan
                        Can Delete
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Code Preview --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-code-slash"></i> Secure Code - Policy Protection
    </div>
    <div class="card-body code-preview">
        <pre class="mb-0 bg-dark text-light p-3 rounded small"><code><span class="comment">// ✅ SecureController.php - GUNAKAN INI!</span>

<span class="keyword">public function</span> __construct()
{
    <span class="comment">// ✅ authorizeResource otomatis check policy</span>
    $this->authorizeResource(Ticket::<span class="keyword">class</span>, <span class="string">'ticket'</span>);
}

<span class="keyword">public function</span> show(Ticket $ticket)
{
    <span class="comment">// ✅ Authorization sudah di-handle authorizeResource</span>
    <span class="comment">// Jika tidak berhak → otomatis 403 Forbidden</span>

    <span class="keyword">return</span> view(<span class="string">'tickets.show'</span>, compact(<span class="string">'ticket'</span>));
}

<span class="comment">// ======= TicketPolicy.php =======</span>
<span class="keyword">public function</span> view(User $user, Ticket $ticket): <span class="keyword">bool</span>
{
    <span class="comment">// ✅ Check ownership - KUNCI PENCEGAHAN IDOR</span>
    <span class="keyword">return</span> $ticket->user_id === $user->id;
}</code></pre>
    </div>
</div>

<div class="text-center mt-4">
    <a href="{{ route('bac-lab.vulnerable.tickets.show', $ticket) }}" class="btn btn-outline-danger">
        <i class="bi bi-exclamation-triangle"></i> Lihat di Versi Vulnerable
    </a>
</div>
@endsection
