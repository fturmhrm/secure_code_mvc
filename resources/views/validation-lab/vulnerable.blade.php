{{-- ============================================ --}}
{{-- VALIDATION LAB: Vulnerable Form --}}
{{-- ⚠️ JANGAN GUNAKAN PATTERN INI DI PRODUCTION! --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Form Vulnerable - Validation Lab')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('validation-lab.index') }}">Validation Lab</a></li>
            <li class="breadcrumb-item active">Form Vulnerable</li>
        </ol>
    </nav>

    {{-- Warning Banner --}}
    <div class="alert alert-danger mb-4">
        <h4 class="alert-heading">
            <i class="bi bi-exclamation-octagon-fill"></i> HALAMAN VULNERABLE
        </h4>
        <p>
            Form ini <strong>TIDAK</strong> memiliki server-side validation. 
            Client-side validation (HTML5) bisa dengan mudah di-bypass menggunakan DevTools!
        </p>
        <hr>
        <p class="mb-0 small">
            <strong>Cara bypass:</strong> Buka DevTools (F12) → Inspect input → Hapus atribut 
            <code>required</code>, <code>pattern</code>, <code>min</code>, <code>max</code> → Submit!
        </p>
    </div>

    <div class="row">
        {{-- Form --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Form TANPA Server Validation
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('validation-lab.vulnerable.submit') }}" method="POST">
                        @csrf

                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-control"
                                   required
                                   minlength="2"
                                   maxlength="100"
                                   placeholder="Masukkan nama">
                            <small class="text-muted">
                                HTML5: required, minlength=2, maxlength=100
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
                                   class="form-control"
                                   required
                                   placeholder="contoh@email.com">
                            <small class="text-muted">
                                HTML5: type=email, required
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
                                   class="form-control"
                                   required
                                   min="17"
                                   max="100"
                                   placeholder="Masukkan umur">
                            <small class="text-muted">
                                HTML5: type=number, min=17, max=100
                            </small>
                        </div>

                        {{-- Priority --}}
                        <div class="mb-3">
                            <label for="priority" class="form-label">
                                Prioritas <span class="text-danger">*</span>
                            </label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <small class="text-muted">
                                HTML5: required (tapi value bisa diubah via DevTools!)
                            </small>
                        </div>

                        {{-- Message --}}
                        <div class="mb-3">
                            <label for="message" class="form-label">
                                Pesan <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" 
                                      id="message"
                                      class="form-control"
                                      required
                                      minlength="10"
                                      maxlength="1000"
                                      rows="3"
                                      placeholder="Tulis pesan Anda..."></textarea>
                            <small class="text-muted">
                                HTML5: required, minlength=10, maxlength=1000
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-send"></i> Submit (Tanpa Server Validation)
                            </button>
                            <a href="{{ route('validation-lab.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bypass Instructions --}}
            <div class="card border-warning mt-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">
                        <i class="bi bi-tools"></i> Cara Bypass Client-Side Validation
                    </h6>
                </div>
                <div class="card-body small">
                    <ol class="mb-0">
                        <li>Buka <strong>DevTools</strong> (F12 atau Ctrl+Shift+I)</li>
                        <li>Klik tab <strong>Elements/Inspector</strong></li>
                        <li>Klik kanan pada input → <strong>Inspect</strong></li>
                        <li>Hapus atribut: <code>required</code>, <code>min</code>, <code>max</code>, <code>pattern</code></li>
                        <li>Ubah <code>type="email"</code> menjadi <code>type="text"</code></li>
                        <li>Submit form dengan data invalid!</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Submissions --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Data yang Masuk
                    </h5>
                    @if(count($submissions) > 0)
                        <form action="{{ route('validation-lab.vulnerable.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Clear
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($submissions as $index => $data)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>#{{ $index + 1 }}</strong>
                                <small class="text-muted">{{ $data['submitted_at'] ?? '-' }}</small>
                            </div>
                            <table class="table table-sm table-borderless mb-0 small">
                                <tr>
                                    <td width="80"><strong>Name:</strong></td>
                                    <td>
                                        <code class="text-break">{{ $data['name'] ?? '(kosong)' }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        <code class="text-break">{{ $data['email'] ?? '(kosong)' }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td>
                                        <code>{{ $data['age'] ?? '(kosong)' }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Priority:</strong></td>
                                    <td>
                                        <code>{{ $data['priority'] ?? '(kosong)' }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Message:</strong></td>
                                    <td>
                                        <code class="text-break">{{ $data['message'] ?? '(kosong)' }}</code>
                                    </td>
                                </tr>
                            </table>
                            
                            {{-- Analisis --}}
                            @php
                                $issues = [];
                                if(empty($data['name'])) $issues[] = 'Nama kosong';
                                if(empty($data['email'])) $issues[] = 'Email kosong';
                                elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $issues[] = 'Email invalid';
                                if(empty($data['age'])) $issues[] = 'Umur kosong';
                                elseif(!is_numeric($data['age'])) $issues[] = 'Umur bukan angka';
                                elseif($data['age'] < 17 || $data['age'] > 100) $issues[] = 'Umur di luar range';
                                if(!in_array($data['priority'] ?? '', ['low', 'medium', 'high'])) $issues[] = 'Priority invalid';
                                if(empty($data['message'])) $issues[] = 'Pesan kosong';
                                elseif(strlen($data['message']) < 10) $issues[] = 'Pesan terlalu pendek';
                            @endphp
                            
                            @if(count($issues) > 0)
                                <div class="alert alert-danger py-1 mt-2 mb-0 small">
                                    <strong>⚠️ Issues:</strong>
                                    {{ implode(', ', $issues) }}
                                </div>
                            @else
                                <div class="alert alert-success py-1 mt-2 mb-0 small">
                                    <strong>✅</strong> Data valid (kebetulan)
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">Belum ada data. Submit form untuk melihat!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
