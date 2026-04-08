@extends('layouts.app')

@section('title', 'Secure - Edit Ticket')

@section('content')
{{-- Success Banner --}}
<div class="alert alert-success success-box">
    <h5><i class="bi bi-shield-check"></i>EDIT AUTHORIZED - Policy Protected</h5>
    <p class="mb-0">
        Anda diizinkan mengedit ticket ini karena Policy check berhasil.
    </p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Edit Ticket #{{ $ticket->id }} (SECURE)
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bac-lab.secure.tickets.update', $ticket) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               value="{{ old('title', $ticket->title) }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4"
                                  required>{{ old('description', $ticket->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select @error('priority') is-invalid @enderror"
                                    id="priority"
                                    name="priority"
                                    required>
                                @foreach(['low', 'medium', 'high'] as $p)
                                    <option value="{{ $p }}" {{ $ticket->priority == $p ? 'selected' : '' }}>
                                        {{ ucfirst($p) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status hanya untuk Admin/Staff --}}
                        @if(auth()->user()->isAdmin())
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status (Admin Only)</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status"
                                        name="status">
                                    @foreach(['open', 'in_progress', 'resolved', 'closed'] as $s)
                                        <option value="{{ $s }}" {{ $ticket->status == $s ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control"
                                       value="{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}"
                                       disabled>
                                <small class="text-muted">Hanya Admin yang bisa mengubah status</small>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Update (SECURE)
                        </button>
                        <a href="{{ route('bac-lab.secure.tickets.show', $ticket) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Code Preview --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-code-slash"></i> Secure Update Code
            </div>
            <div class="card-body code-preview">
                <pre class="mb-0 bg-dark text-light p-3 rounded small"><code><span class="comment">// ✅ SecureController.php - Policy Protected</span>

<span class="keyword">public function</span> update(Request $request, Ticket $ticket)
{
    <span class="comment">// ✅ Authorization via Policy update() - otomatis</span>
    <span class="comment">// Sudah di-handle oleh authorizeResource di constructor</span>

    $validated = $request->validate([...]);

    <span class="comment">// ✅ Status hanya bisa diubah admin</span>
    <span class="keyword">if</span> (auth()->user()->isAdmin()) {
        $validated[<span class="string">'status'</span>] = $request->status;
    }

    $ticket->update($validated);

    <span class="keyword">return</span> redirect()->route(<span class="string">'tickets.show'</span>, $ticket);
}

<span class="comment">// ======= TicketPolicy.php =======</span>
<span class="keyword">public function</span> update(User $user, Ticket $ticket): <span class="keyword">bool</span>
{
    <span class="comment">// ✅ User hanya bisa edit ticket sendiri yang belum closed</span>
    <span class="keyword">return</span> $ticket->user_id === $user->id &&
           $ticket->status !== <span class="string">'closed'</span>;
}</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
