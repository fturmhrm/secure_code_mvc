{{-- ============================================ --}}
{{-- SQLI LAB: Secure Search Demo --}}
{{-- 4 Metode Aman Query Database di Laravel --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Secure Search - SQL Injection Prevention')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sqli-lab.index') }}">SQLi Lab</a></li>
                    <li class="breadcrumb-item active">Secure Search</li>
                </ol>
            </nav>

            {{-- Header --}}
            <div class="text-center mb-4">
                <h1 class="display-6 fw-bold">
                    <i class="bi bi-shield-check text-success"></i>
                    Secure Search Methods
                </h1>
                <p class="text-muted">
                    4 cara aman melakukan query database di Laravel
                </p>
            </div>

            {{-- Search Form --}}
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-search"></i> Cari Produk (Secure)
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('sqli-lab.secure-search') }}">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text"
                                       name="search"
                                       class="form-control form-control-lg"
                                       placeholder="Cari nama produk..."
                                       value="{{ $search ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <select name="method" class="form-select form-select-lg">
                                    <option value="eloquent" {{ ($method ?? '') == 'eloquent' ? 'selected' : '' }}>
                                        Eloquent ORM
                                    </option>
                                    <option value="query_builder" {{ ($method ?? '') == 'query_builder' ? 'selected' : '' }}>
                                        Query Builder
                                    </option>
                                    <option value="param_binding" {{ ($method ?? '') == 'param_binding' ? 'selected' : '' }}>
                                        Parameter Binding (?)
                                    </option>
                                    <option value="named_binding" {{ ($method ?? '') == 'named_binding' ? 'selected' : '' }}>
                                        Named Binding (:name)
                                    </option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3">
                            <i class="bi bi-search"></i> Cari dengan Metode Terpilih
                        </button>
                    </form>
                </div>
            </div>

            {{-- Test with Injection Payload --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-exclamation"></i> Test dengan Payload Injection
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Coba payload yang sama dengan vulnerable search untuk membuktikan keamanan:
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-secondary payload-btn" data-payload="'">
                            <code>'</code>
                        </button>
                        <button class="btn btn-outline-secondary payload-btn" data-payload="' OR '1'='1">
                            <code>' OR '1'='1</code>
                        </button>
                        <button class="btn btn-outline-secondary payload-btn" data-payload="' UNION SELECT 1,2,3,4,5-- ">
                            <code>' UNION SELECT...</code>
                        </button>
                        <button class="btn btn-outline-secondary payload-btn" data-payload="admin'-- ">
                            <code>admin'-- </code>
                        </button>
                    </div>
                    <div class="alert alert-success mt-3 mb-0 small">
                        <i class="bi bi-info-circle"></i>
                        Perhatikan: Payload akan diperlakukan sebagai <strong>text biasa</strong>,
                        bukan SQL command!
                    </div>
                </div>
            </div>

            {{-- Code Used --}}
            @if(isset($codeUsed))
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-code"></i>
                        Metode: {{ ucfirst(str_replace('_', ' ', $method ?? 'eloquent')) }}
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded mb-0"><code>{!! $codeUsed !!}</code></pre>
                </div>
            </div>
            @endif

            {{-- Results --}}
            @if(isset($products) && count($products) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-table"></i>
                        Hasil Pencarian ({{ count($products) }} item)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                    <th>Deskripsi</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id ?? $product['id'] ?? '-' }}</td>
                                    <td><strong>{{ $product->name ?? $product['name'] ?? '-' }}</strong></td>
                                    <td>Rp {{ number_format($product->price ?? $product['price'] ?? 0) }}</td>
                                    <td class="small">{{ Str::limit($product->description ?? $product['description'] ?? '-', 30) }}</td>
                                    <td>{{ $product->stock ?? $product['stock'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @elseif(isset($search))
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Tidak ada produk ditemukan untuk "{{ $search }}"
                <br>
                <small class="text-muted">
                    Perhatikan: Input diperlakukan sebagai text literal, bukan SQL command.
                </small>
            </div>
            @endif

            {{-- Method Comparison --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-diagram-3"></i> Perbandingan 4 Metode Secure
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Eloquent --}}
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">1. Eloquent ORM</h6>
                                    <small class="text-success">⭐ Recommended</small>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-dark text-light p-2 rounded small"><code>SqliLabProduct::where('name', 'LIKE', '%' . $search . '%')
    ->get();</code></pre>
                                    <ul class="small mb-0 mt-2">
                                        <li>Otomatis escape input</li>
                                        <li>Type-safe dan readable</li>
                                        <li>Built-in model features</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Query Builder --}}
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">2. Query Builder</h6>
                                    <small class="text-success">⭐ Recommended</small>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-dark text-light p-2 rounded small"><code>DB::table('sqli_lab_products')
    ->where('name', 'LIKE', '%' . $search . '%')
    ->get();</code></pre>
                                    <ul class="small mb-0 mt-2">
                                        <li>Fluent interface</li>
                                        <li>Tidak perlu model</li>
                                        <li>Otomatis parameterized</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Positional Binding --}}
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">3. Parameter Binding (?)</h6>
                                    <small class="text-info">Good</small>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-dark text-light p-2 rounded small"><code>DB::select(
    "SELECT * FROM sqli_lab_products
     WHERE name LIKE ?",
    ['%' . $search . '%']
);</code></pre>
                                    <ul class="small mb-0 mt-2">
                                        <li>Raw SQL dengan binding</li>
                                        <li>? = positional parameter</li>
                                        <li>Urutan penting!</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Named Binding --}}
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">4. Named Binding (:name)</h6>
                                    <small class="text-info">Good</small>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-dark text-light p-2 rounded small"><code>DB::select(
    "SELECT * FROM sqli_lab_products
     WHERE name LIKE :search",
    ['search' => '%' . $search . '%']
);</code></pre>
                                    <ul class="small mb-0 mt-2">
                                        <li>Raw SQL dengan named params</li>
                                        <li>Lebih readable</li>
                                        <li>Urutan tidak penting</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Why It's Secure --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle"></i> Mengapa Ini Aman?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">✓ Prepared Statements</h6>
                            <p class="small text-muted">
                                Query dan data dikirim terpisah ke database.
                                Database tahu mana yang query structure dan mana yang data.
                            </p>
                            <pre class="bg-light p-2 rounded small"><code>// Step 1: Prepare
PREPARE stmt FROM 'SELECT * FROM users WHERE name = ?';

// Step 2: Execute dengan data
EXECUTE stmt USING 'John';</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">✓ Automatic Escaping</h6>
                            <p class="small text-muted">
                                Karakter berbahaya otomatis di-escape sehingga diperlakukan sebagai string literal.
                            </p>
                            <pre class="bg-light p-2 rounded small"><code>// Input: ' OR '1'='1
// Setelah escape: \' OR \'1\'=\'1

// Menjadi pencarian literal:
WHERE name = '\' OR \'1\'=\'1'</code></pre>
                        </div>
                    </div>

                    <div class="alert alert-success mt-3 mb-0">
                        <strong>Kesimpulan:</strong>
                        Dengan prepared statements / parameterized queries,
                        <strong>input user TIDAK PERNAH bisa mengubah struktur query</strong>.
                        Input selalu diperlakukan sebagai DATA, bukan SQL CODE.
                    </div>
                </div>
            </div>

            {{-- Best Practices --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check"></i> Best Practices Laravel
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">
                            <strong>Selalu gunakan Eloquent atau Query Builder</strong>
                            <br>
                            <small class="text-muted">
                                Kedua metode ini otomatis parameterized dan aman.
                            </small>
                        </li>
                        <li class="mb-2">
                            <strong>Hindari raw SQL jika tidak diperlukan</strong>
                            <br>
                            <small class="text-muted">
                                Jika harus raw, gunakan parameter binding.
                            </small>
                        </li>
                        <li class="mb-2">
                            <strong>JANGAN pernah concatenate input ke query</strong>
                            <br>
                            <small class="text-danger">
                                <code>❌ "SELECT * FROM users WHERE id = $id"</code>
                            </small>
                        </li>
                        <li class="mb-2">
                            <strong>Validasi input dengan Laravel Validation</strong>
                            <br>
                            <small class="text-muted">
                                Defense in depth - validasi tipe dan format input.
                            </small>
                        </li>
                        <li class="mb-2">
                            <strong>Gunakan type hinting</strong>
                            <br>
                            <small class="text-muted">
                                <code>User::find((int) $id)</code> - cast ke integer untuk ID.
                            </small>
                        </li>
                    </ol>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('sqli-lab.blind-sqli') }}" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-left"></i> Blind SQLi
                </a>
                <a href="{{ route('sqli-lab.cheatsheet') }}" class="btn btn-dark">
                    Lihat Cheatsheet <i class="bi bi-file-code"></i>
                </a>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payload buttons
    document.querySelectorAll('.payload-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var payload = this.getAttribute('data-payload');
            document.querySelector('input[name="search"]').value = payload;
        });
    });
});

function setSearch(payload) {
    document.querySelector('input[name="search"]').value = payload;
}
</script>
@endpush

{{-- Inline script as fallback --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payload buttons
    document.querySelectorAll('.payload-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var payload = this.getAttribute('data-payload');
            document.querySelector('input[name="search"]').value = payload;
        });
    });
});

function setSearch(payload) {
    document.querySelector('input[name="search"]').value = payload;
}
</script>
@endsection
