{{-- ============================================ --}}
{{-- TICKETS: Create Form --}}
{{-- Dengan Input Validation & Error Handling --}}
{{-- Materi Minggu 3 - Hari 2 --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Buat Tiket Baru')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="bi bi-plus-circle text-primary"></i>
                    Buat Tiket Baru
                </h2>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Card Form --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-ticket"></i> Form Tiket Baru
                    </h5>
                </div>
                <div class="card-body">

                    {{-- ============================================ --}}
                    {{-- GLOBAL ERROR DISPLAY --}}
                    {{-- Menampilkan semua error sekaligus --}}
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
                    <form action="{{ route('tickets.store') }}" method="POST">
                        {{-- CSRF Token - WAJIB untuk keamanan! --}}
                        @csrf

                        {{-- ======================================== --}}
                        {{-- TITLE FIELD --}}
                        {{-- ======================================== --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Judul Tiket <span class="text-danger">*</span>
                            </label>

                            {{--
                                Input dengan:
                                - @error('title') is-invalid @enderror : tambah class jika ada error
                                - value="{{ old('title') }}" : tampilkan input sebelumnya jika redirect back
                            --}}
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="Contoh: Bug pada halaman login"
                                   required
                                   minlength="5"
                                   maxlength="255">

                            {{--
                                Per-field error display
                                @error('field') ... @enderror
                            --}}
                            @error('title')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
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
                                      placeholder="Jelaskan masalah Anda secara detail...&#10;&#10;Contoh:&#10;- Langkah untuk reproduce bug&#10;- Error message yang muncul&#10;- Expected behavior"
                                      required
                                      minlength="20">{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="text-muted">
                                Minimal 20 karakter. Jelaskan dengan detail agar mudah ditangani.
                            </small>
                        </div>

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
                                <option value="">-- Pilih Prioritas --</option>

                                {{--
                                    old('priority') == 'value' ? 'selected' : ''
                                    Mempertahankan pilihan sebelumnya jika ada error
                                --}}
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                    🟢 Low - Tidak mendesak, bisa ditangani nanti
                                </option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                    🟡 Medium - Perlu ditangani dalam waktu dekat
                                </option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                    🔴 High - Sangat mendesak, perlu ditangani segera
                                </option>
                            </select>

                            @error('priority')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
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
                                   value="{{ old('category') }}"
                                   placeholder="Contoh: Bug, Feature Request, Question"
                                   maxlength="100">

                            @error('category')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- ======================================== --}}
                        {{-- SUBMIT BUTTONS --}}
                        {{-- ======================================== --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Kirim Tiket
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                            <a href="{{ route('tickets.index') }}" class="btn btn-link text-muted">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Tips Footer --}}
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        <strong>Tips:</strong> Semakin detail deskripsi, semakin cepat tiket ditangani.
                        Field dengan tanda <span class="text-danger">*</span> wajib diisi.
                    </small>
                </div>
            </div>

            {{-- Security Info --}}
            <div class="alert alert-info mt-3">
                <h6 class="alert-heading">
                    <i class="bi bi-shield-check"></i> Security Info
                </h6>
                <ul class="mb-0 small">
                    <li><strong>CSRF Token:</strong> Form dilindungi dari Cross-Site Request Forgery</li>
                    <li><strong>Server Validation:</strong> Semua input divalidasi di server menggunakan Form Request</li>
                    <li><strong>Sanitization:</strong> HTML tags dihapus dari input untuk mencegah XSS</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
