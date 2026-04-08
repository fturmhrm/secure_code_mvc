{{-- ============================================ --}}
{{-- TICKETS: Edit Form --}}
{{-- Dengan Input Validation & Error Handling --}}
{{-- Materi Minggu 3 - Hari 2 --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Edit Tiket')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="bi bi-pencil-square text-warning"></i>
                    Edit Tiket #{{ $ticket->id }}
                </h2>
                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Card Form --}}
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil"></i> Edit Tiket
                    </h5>
                </div>
                <div class="card-body">

                    {{-- ============================================ --}}
                    {{-- GLOBAL ERROR DISPLAY --}}
                    {{-- ============================================ --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="bi bi-exclamation-triangle"></i>
                                Oops! Ada kesalahan pada input Anda.
                            </h6>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ============================================ --}}
                    {{-- FORM --}}
                    {{-- ============================================ --}}
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                        @csrf
                        {{-- Method Spoofing untuk PUT request --}}
                        @method('PUT')

                        {{-- ======================================== --}}
                        {{-- TITLE FIELD --}}
                        {{-- ======================================== --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Judul Tiket <span class="text-danger">*</span>
                            </label>

                            {{--
                                old('title', $ticket->title)
                                Prioritas: old value > existing value
                            --}}
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $ticket->title) }}"
                                   required
                                   minlength="5"
                                   maxlength="255">

                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Minimal 5 karakter, maksimal 255 karakter
                            </small>
                        </div>

                        {{-- ======================================== --}}
                        {{-- DESCRIPTION FIELD --}}
                        {{-- ======================================== --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                Deskripsi <span class="text-danger">*</span>
                            </label>

                            <textarea name="description"
                                      id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="5"
                                      required
                                      minlength="20">{{ old('description', $ticket->description) }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Minimal 20 karakter
                            </small>
                        </div>

                        {{-- ======================================== --}}
                        {{-- STATUS FIELD (Admin/Staff only) --}}
                        {{-- MINGGU 4 HARI 2: Conditional field --}}
                        {{-- ======================================== --}}
                        @if(auth()->user()->hasAnyRole(['admin', 'staff']))
                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    Status <span class="text-danger">*</span>
                                    <span class="badge bg-info">Admin/Staff only</span>
                                </label>

                                <select name="status"
                                        id="status"
                                        class="form-select @error('status') is-invalid @enderror"
                                        required>
                                    <option value="open"
                                        {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>
                                        🔵 Open - Belum ditangani
                                    </option>
                                    <option value="in_progress"
                                        {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>
                                        🟡 In Progress - Sedang ditangani
                                    </option>
                                    <option value="resolved"
                                        {{ old('status', $ticket->status) == 'resolved' ? 'selected' : '' }}>
                                        🟠 Resolved - Sudah diselesaikan
                                    </option>
                                    <option value="closed"
                                        {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>
                                        🟢 Closed - Selesai
                                    </option>
                                </select>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            {{-- User biasa hanya bisa lihat status --}}
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" 
                                       value="{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}" 
                                       disabled>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    Status hanya bisa diubah oleh Admin/Staff
                                </small>
                            </div>
                        @endif

                        {{-- ======================================== --}}
                        {{-- PRIORITY FIELD --}}
                        {{-- ======================================== --}}
                        <div class="mb-3">
                            <label for="priority" class="form-label">
                                Prioritas <span class="text-danger">*</span>
                            </label>

                            <select name="priority"
                                    id="priority"
                                    class="form-select @error('priority') is-invalid @enderror"
                                    required>
                                <option value="low"
                                    {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>
                                    🟢 Low - Tidak mendesak
                                </option>
                                <option value="medium"
                                    {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>
                                    🟡 Medium - Perlu ditangani
                                </option>
                                <option value="high"
                                    {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>
                                    🔴 High - Sangat mendesak
                                </option>
                            </select>

                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ======================================== --}}
                        {{-- CATEGORY FIELD (Optional) --}}
                        {{-- ======================================== --}}
                        <div class="mb-4">
                            <label for="category" class="form-label">
                                Kategori <span class="text-muted">(opsional)</span>
                            </label>

                            <input type="text"
                                   name="category"
                                   id="category"
                                   class="form-control @error('category') is-invalid @enderror"
                                   value="{{ old('category', $ticket->category) }}"
                                   maxlength="100">

                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ======================================== --}}
                        {{-- SUBMIT BUTTONS --}}
                        {{-- ======================================== --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Info Footer --}}
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-clock"></i>
                        Dibuat: {{ $ticket->created_at->format('d M Y H:i') }}
                        @if($ticket->updated_at != $ticket->created_at)
                            | Diupdate: {{ $ticket->updated_at->format('d M Y H:i') }}
                        @endif
                    </small>
                </div>
            </div>

            {{-- Delete Option --}}
            <div class="card border-danger mt-3">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Zona Berbahaya
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Menghapus tiket bersifat permanen dan tidak dapat dibatalkan.
                    </p>
                    <form action="{{ route('tickets.destroy', $ticket) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus tiket ini? Aksi ini tidak dapat dibatalkan!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i> Hapus Tiket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
