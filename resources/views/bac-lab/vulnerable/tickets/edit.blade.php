@extends('layouts.app')

@section('title', 'Vulnerable - Edit Ticket')

@section('content')
{{-- Warning Banner --}}
<div class="alert alert-danger danger-box">
    <h5><i class="bi bi-exclamation-triangle-fill"></i>IDOR pada UPDATE - SANGAT BERBAHAYA!</h5>
    <p class="mb-0">
        @if($ticket->user_id !== auth()->id())
            <strong>BERBAHAYA!</strong> Anda bisa mengedit ticket milik <strong>{{ $ticket->user->name }}</strong>!
            Attacker bisa memodifikasi/merusak data orang lain.
        @else
            Ini ticket milik Anda. Coba ganti ID di URL untuk mengedit ticket orang lain.
        @endif
    </p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Edit Ticket #{{ $ticket->id }} (VULNERABLE)
                </h5>
            </div>
            <div class="card-body">
                @if($ticket->user_id !== auth()->id())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Ticket milik:</strong> {{ $ticket->user->name }} ({{ $ticket->user->email }})
                    </div>
                @endif

                <form action="{{ route('bac-lab.vulnerable.tickets.update', $ticket) }}" method="POST">
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

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
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
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-save"></i> Update (VULNERABLE)
                        </button>
                        <a href="{{ route('bac-lab.vulnerable.tickets.show', $ticket) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Code Preview --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-code-slash"></i> Vulnerable Update Code
            </div>
            <div class="card-body code-preview">
                <pre class="mb-0 bg-dark text-light p-3 rounded small"><code><span class="comment">// ❌ VULNERABLE - User bisa update ticket siapa saja!</span>

<span class="keyword">public function</span> update(Request $request, $id)
{
    $validated = $request->validate([...]);

    <span class="comment">// ❌ VULNERABLE: Langsung update tanpa check ownership</span>
    $ticket = Ticket::findOrFail($id);
    $ticket->update($validated);

    <span class="comment">// Attacker bisa mengubah data victim!</span>
    <span class="keyword">return</span> redirect()->route(<span class="string">'tickets.show'</span>, $ticket);
}</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
