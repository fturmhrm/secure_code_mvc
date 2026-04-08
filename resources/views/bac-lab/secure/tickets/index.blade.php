@extends('layouts.app')

@section('title', 'Secure - My Tickets')

@section('content')
{{-- Success Banner --}}
<div class="alert alert-success success-box">
    <h5><i class="bi bi-shield-check"></i>SECURE VERSION - Dilindungi Policy</h5>
    <p class="mb-0">
        Halaman ini hanya menampilkan tickets milik Anda sendiri.
        @if(auth()->user()->isAdmin())
            Sebagai <strong>Admin</strong>, Anda bisa melihat semua tickets.
        @endif
    </p>
</div>

<div class="card">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-shield-check"></i>
            @if(auth()->user()->isAdmin())
                All Tickets (Admin View)
            @else
                My Tickets
            @endif
        </h5>
        <span class="badge bg-dark">
            Total: {{ $tickets->count() }} tickets
        </span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    @if(auth()->user()->isAdmin())
                        <th>Owner</th>
                    @endif
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td><strong>#{{ $ticket->id }}</strong></td>
                        <td>
                            <a href="{{ route('bac-lab.secure.tickets.show', $ticket) }}" class="text-decoration-none">
                                {{ $ticket->title }}
                            </a>
                        </td>
                        @if(auth()->user()->isAdmin())
                            <td>
                                {{ $ticket->user->name }}
                                <br><small class="text-muted">{{ $ticket->user->email }}</small>
                            </td>
                        @endif
                        <td>
                            <span class="badge {{ $ticket->status_badge }}">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->priority_badge }}">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('bac-lab.secure.tickets.show', $ticket) }}"
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-eye"></i> View
                            </a>
                            @can('update', $ticket)
                                <a href="{{ route('bac-lab.secure.tickets.edit', $ticket) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="text-center py-4">
                            No tickets found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Code Preview --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-code-slash"></i> Secure Code
    </div>
    <div class="card-body code-preview">
        <pre class="mb-0 bg-dark text-light p-3 rounded small"><code><span class="comment">// ✅ SecureController.php - GUNAKAN INI!</span>

<span class="keyword">public function</span> __construct()
{
    <span class="comment">// ✅ Otomatis authorization untuk semua action</span>
    $this->authorizeResource(Ticket::<span class="keyword">class</span>, <span class="string">'ticket'</span>);
}

<span class="keyword">public function</span> index()
{
    $user = auth()->user();

    <span class="comment">// ✅ SECURE: Filter berdasarkan role</span>
    <span class="keyword">if</span> ($user->isAdmin()) {
        $tickets = Ticket::with(<span class="string">'user'</span>)->latest()->get();
    } <span class="keyword">else</span> {
        <span class="comment">// User biasa hanya lihat milik sendiri</span>
        $tickets = $user->tickets()->latest()->get();
    }

    <span class="keyword">return</span> view(...);
}</code></pre>
    </div>
</div>

{{-- Compare Link --}}
<div class="text-center mt-4">
    <a href="{{ route('bac-lab.vulnerable.tickets.index') }}" class="btn btn-outline-danger">
        <i class="bi bi-exclamation-triangle"></i> Bandingkan dengan Versi Vulnerable
    </a>
</div>
@endsection
