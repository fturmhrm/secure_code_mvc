@extends('layouts.app')

@section('title', 'Vulnerable - View Ticket')

@section('content')
{{-- Warning Banner --}}
<div class="alert alert-danger danger-box">
    <h5><i class="bi bi-exclamation-triangle-fill"></i>IDOR VULNERABILITY DEMONSTRATED!</h5>
    <p class="mb-0">
        @if($ticket->user_id !== auth()->id())
            <strong>BERHASIL!</strong> Anda sedang melihat ticket milik <strong>{{ $ticket->user->name }}</strong>.
            Ini seharusnya <strong>TIDAK BOLEH</strong> terjadi!
        @else
            Ini ticket milik Anda. Coba ganti ID di URL untuk melihat ticket orang lain.
        @endif
    </p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-ticket"></i> Ticket #{{ $ticket->id }}
                    </h5>
                    @if($ticket->user_id !== auth()->id())
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-exclamation-triangle"></i> IDOR SUCCESS!
                        </span>
                    @endif
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
                <a href="{{ route('bac-lab.vulnerable.tickets.edit', $ticket) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit (VULNERABLE)
                </a>
                <a href="{{ route('bac-lab.vulnerable.tickets.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Owner Info --}}
        <div class="card border-warning mb-4">
            <div class="card-header bg-warning text-dark">
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

                @if($ticket->user_id !== auth()->id())
                    <hr>
                    <div class="alert alert-danger small mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Anda bukan pemilik ticket ini!</strong>
                        <br>Ini adalah kebocoran data (data breach).
                    </div>
                @endif
            </div>
        </div>

        {{-- IDOR Test --}}
        <div class="card">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-bug"></i> Test IDOR
            </div>
            <div class="card-body">
                <p class="small">Coba akses ticket lain:</p>
                <div class="d-grid gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <a href="{{ route('bac-lab.vulnerable.tickets.show', $i) }}"
                           class="btn btn-sm {{ $i == $ticket->id ? 'btn-danger' : 'btn-outline-danger' }}">
                            /tickets/{{ $i }}
                            @if($i == $ticket->id) (current) @endif
                        </a>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Code Preview --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-code-slash"></i> Vulnerable Code - IDOR
    </div>
    <div class="card-body code-preview">
        <pre class="mb-0 bg-dark text-light p-3 rounded small"><code><span class="comment">// ❌ VulnerableController.php - JANGAN DITIRU!</span>

<span class="keyword">public function</span> show($id)
{
    <span class="comment">// ❌ VULNERABLE: Langsung find tanpa authorization</span>
    $ticket = Ticket::findOrFail($id);

    <span class="comment">// ❌ TIDAK ADA check: $ticket->user_id === auth()->id()</span>
    <span class="comment">// User bisa akses ticket SIAPA SAJA dengan mengganti ID!</span>

    <span class="keyword">return</span> view(<span class="string">'tickets.show'</span>, compact(<span class="string">'ticket'</span>));
}</code></pre>
    </div>
</div>

<div class="text-center mt-4">
    <a href="{{ route('bac-lab.secure.tickets.show', $ticket) }}" class="btn btn-success">
        <i class="bi bi-shield-check"></i> Coba Akses di Versi Secure
    </a>
</div>
@endsection
