{{-- ============================================ --}}
{{-- VALIDATION LAB: Secure Form --}}
{{-- ✅ Best Practice - Server-Side Validation --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Form Secure - Validation Lab')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('validation-lab.index') }}">Validation Lab</a></li>
            <li class="breadcrumb-item active">Form Secure</li>
        </ol>
    </nav>

    {{-- Success Banner --}}
    <div class="alert alert-success mb-4">
        <h4 class="alert-heading">
            <i class="bi bi-shield-check"></i> HALAMAN SECURE
        </h4>
        <p>
            Form ini memiliki <strong>server-side validation</strong> menggunakan Laravel. 
            Meskipun client-side validation di-bypass, server akan tetap menolak data invalid!
        </p>
        <hr>
        <p class="mb-0 small">
            <strong>Coba bypass:</strong> Hapus atribut HTML5 via DevTools dan submit - 
            server akan tetap mengembalikan error!
        </p>
    </div>

    <div class="row">
        {{-- Form --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check"></i> 
                        Form DENGAN Server Validation
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Global Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle"></i> Ada kesalahan:</h6>
                            <ul class="mb-0 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('validation-lab.secure.submit') }}" method="POST">
                        @csrf

                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   required
                                   minlength="2"
                                   maxlength="100"
                                   placeholder="Masukkan nama">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Server: required, string, min:2, max:100
                            </small>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="contoh@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Server: required, email:rfc,dns
                            </small>
                        </div>

                        {{-- Age --}}
                        <div class="mb-3">
                            <label for="age" class="form-label">
                                Umur <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="age" 
                                   id="age"
                                   class="form-control @error('age') is-invalid @enderror"
                                   value="{{ old('age') }}"
                                   required
                                   min="17"
                                   max="100"
                                   placeholder="Masukkan umur">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Server: required, integer, min:17, max:100
                            </small>
                        </div>

                        {{-- Priority --}}
                        <div class="mb-3">
                            <label for="priority" class="form-label">
                                Prioritas <span class="text-danger">*</span>
                            </label>
                            <select name="priority" 
                                    id="priority" 
                                    class="form-select @error('priority') is-invalid @enderror" 
                                    required>
                                <option value="">-- Pilih --</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                    Low
                                </option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                    Medium
                                </option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                    High
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Server: required, in:low,medium,high (WHITELIST!)
                            </small>
                        </div>

                        {{-- Message --}}
                        <div class="mb-3">
                            <label for="message" class="form-label">
                                Pesan <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" 
                                      id="message"
                                      class="form-control @error('message') is-invalid @enderror"
                                      required
                                      minlength="10"
                                      maxlength="1000"
                                      rows="3"
                                      placeholder="Tulis pesan Anda...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Server: required, string, min:10, max:1000
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Submit (Dengan Server Validation)
                            </button>
                            <a href="{{ route('validation-lab.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Validation Code --}}
            <div class="card border-info mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-code-slash"></i> Server-Side Validation Code
                    </h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded small mb-0"><code>$validated = $request->validate([
    'name'     => 'required|string|min:2|max:100',
    'email'    => 'required|email:rfc,dns',
    'age'      => 'required|integer|min:17|max:100',
    'priority' => 'required|in:low,medium,high',
    'message'  => 'required|string|min:10|max:1000',
], [
    'name.required' => 'Nama wajib diisi.',
    'name.min'      => 'Nama minimal :min karakter.',
    // ... custom messages lainnya
]);</code></pre>
                </div>
            </div>
        </div>

        {{-- Submissions --}}
        <div class="col-lg-6">
            <div class="card border-success">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check"></i> Data Valid yang Masuk
                    </h5>
                    @if(count($submissions) > 0)
                        <form action="{{ route('validation-lab.secure.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Clear
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($submissions as $index => $data)
                        <div class="border border-success rounded p-3 mb-3 bg-success bg-opacity-10">
                            <div class="d-flex justify-content-between mb-2">
                                <strong class="text-success">#{{ $index + 1 }}</strong>
                                <small class="text-muted">{{ $data['submitted_at'] ?? '-' }}</small>
                            </div>
                            <table class="table table-sm table-borderless mb-0 small">
                                <tr>
                                    <td width="80"><strong>Name:</strong></td>
                                    <td>{{ $data['name'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $data['email'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td>{{ $data['age'] }} tahun</td>
                                </tr>
                                <tr>
                                    <td><strong>Priority:</strong></td>
                                    <td>
                                        @if($data['priority'] == 'high')
                                            <span class="badge bg-danger">High</span>
                                        @elseif($data['priority'] == 'medium')
                                            <span class="badge bg-warning text-dark">Medium</span>
                                        @else
                                            <span class="badge bg-success">Low</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Message:</strong></td>
                                    <td>{{ $data['message'] }}</td>
                                </tr>
                            </table>
                            <div class="alert alert-success py-1 mt-2 mb-0 small">
                                <i class="bi bi-check-circle"></i>
                                <strong>Verified!</strong> Data telah divalidasi server.
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Belum ada data valid. Submit form untuk melihat!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Comparison Card --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-arrow-left-right"></i> Perbandingan
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm small mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-danger">Vulnerable</th>
                                <th class="text-success">Secure</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Server Validation</td>
                                <td class="text-danger">❌ Tidak</td>
                                <td class="text-success">✅ Ya</td>
                            </tr>
                            <tr>
                                <td>Bisa Bypass?</td>
                                <td class="text-danger">❌ Ya</td>
                                <td class="text-success">✅ Tidak</td>
                            </tr>
                            <tr>
                                <td>Data Aman?</td>
                                <td class="text-danger">❌ Tidak</td>
                                <td class="text-success">✅ Ya</td>
                            </tr>
                            <tr>
                                <td>Production Ready?</td>
                                <td class="text-danger">❌ TIDAK!</td>
                                <td class="text-success">✅ Ya</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
