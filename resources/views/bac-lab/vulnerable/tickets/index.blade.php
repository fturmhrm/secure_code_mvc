@extends('layouts.app')

@section('title', 'Vulnerable - My Tickets')

@section('content')
{{-- Warning Banner --}}
<div class="alert alert-danger danger-box">
    <h5><i class="bi bi-exclamation-triangle-fill"></i>VULNERABLE VERSION - IDOR VULNERABILITY!</h5>
    <p class="mb-1">
        Halaman ini menampilkan ticket milik Anda. <strong>TAPI</strong> ada vulnerability IDOR!
    </p>
    <p class="mb-0">
        <strong>Coba IDOR:</strong> Ganti ID di URL untuk akses ticket orang lain, misalnya:
        <code>/bac-lab/vulnerable/tickets/6</code> (milik victim)
    </p>
</div>

{{-- IDOR Attack Guide --}}
<div class="card border-warning mb-4">
    <div class="card-header bg-warning text-dark">
        <i class="bi bi-bullseye"></i> <strong>Cara Test IDOR Attack</strong>
    </div>
    <div class="card-body">
        <ol class="mb-0">
            <li>Lihat ticket milik Anda di bawah (ID: <strong>{{ $tickets->pluck('id')->implode(', ') ?: 'kosong' }}</strong>)</li>
            <li>Klik salah satu ticket untuk melihat URL-nya</li>
            <li>Ganti ID di URL dengan ID ticket milik orang lain:</li>
        </ol>
        <div class="mt-2">
            @if(auth()->user()->email === 'attacker@test.com')
                <code class="d-block bg-dark text-light p-2 rounded">
                    /bac-lab/vulnerable/tickets/<span class="text-danger">6</span> → Ticket milik victim! 🎯
                </code>
            @elseif(auth()->user()->email === 'victim@test.com')
                <code class="d-block bg-dark text-light p-2 rounded">
                    /bac-lab/vulnerable/tickets/<span class="text-danger">8</span> → Ticket milik attacker!
                </code>
            @else
                <code class="d-block bg-dark text-light p-2 rounded">
                    /bac-lab/vulnerable/tickets/<span class="text-danger">{id_lain}</span> → Ticket milik user lain!
                </code>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-ticket-perforated"></i> My Tickets (Vulnerable Version)
        </h5>
        <span class="badge bg-dark">
            Total: {{ $tickets->count() }} tickets
        </span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
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
                            <a href="{{ route('bac-lab.vulnerable.tickets.show', $ticket->id) }}" class="text-decoration-none">
                                {{ $ticket->title }}
                            </a>
                        </td>
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
                            <a href="{{ route('bac-lab.vulnerable.tickets.show', $ticket->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('bac-lab.vulnerable.tickets.edit', $ticket->id) }}"
                               class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No tickets found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Code Preview --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-code-slash"></i> Vulnerable Code
    </div>
    <div class="card-body code-preview">
        <pre class="mb-0 bg-dark text-light p-3 rounded small"><code><span class="comment">// ❌ VulnerableController.php - JANGAN DITIRU!</span>

<span class="keyword">public function</span> index()
{
    <span class="comment">// ❌ VULNERABLE: Tidak ada filter ownership</span>
    <span class="comment">// User bisa melihat SEMUA tickets!</span>
    $tickets = Ticket::with(<span class="string">'user'</span>)->latest()->get();

    <span class="keyword">return</span> view(<span class="string">'bac-lab.vulnerable.tickets.index'</span>, compact(<span class="string">'tickets'</span>));
}</code></pre>
    </div>
</div>

{{-- Compare Link --}}
<div class="text-center mt-4">
    <a href="{{ route('bac-lab.secure.tickets.index') }}" class="btn btn-success">
        <i class="bi bi-shield-check"></i> Bandingkan dengan Versi Secure
    </a>
</div>
@endsection
