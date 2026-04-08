{{-- ============================================ --}}
{{-- SQLI LAB: Vulnerable Search Demo --}}
{{-- PERHATIAN: HANYA UNTUK PEMBELAJARAN! --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'Vulnerable Search - SQL Injection Demo')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sqli-lab.index') }}">SQLi Lab</a></li>
                    <li class="breadcrumb-item active">Vulnerable Search</li>
                </ol>
            </nav>

            {{-- Warning Banner --}}
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-2 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">VULNERABLE CODE!</h5>
                        <p class="mb-0 small">
                            Halaman ini menggunakan kode yang <strong>SENGAJA VULNERABLE</strong> untuk pembelajaran.
                            Jangan gunakan pattern ini di production!
                        </p>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="text-center mb-4">
                <h1 class="display-6 fw-bold">
                    <i class="bi bi-search text-danger"></i>
                    Vulnerable Product Search
                </h1>
                <p class="text-muted">
                    Demo search endpoint dengan SQL Injection vulnerability
                </p>
            </div>

            {{-- Search Form --}}
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-search"></i> Cari Produk
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('sqli-lab.vulnerable-search') }}">
                        <div class="input-group input-group-lg">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Cari nama produk..."
                                   value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                {{-- Payload Examples --}}
                <div class="col-md-4">
                    <div class="card mb-4 h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning"></i> Contoh Payload
                            </h6>
                        </div>
                        <div class="card-body small">
                            <p class="text-muted">Coba inject payload berikut:</p>

                            {{-- Database Info --}}
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="bi bi-database"></i>
                                <strong>Tabel:</strong> sqli_lab_products (7 kolom)<br>
                                <code class="small">id, name, price, description, stock, created_at, updated_at</code>
                            </div>

                            <div class="mb-3">
                                <strong>1. Test Vulnerability:</strong>
                                <button class="btn btn-sm btn-outline-danger d-block w-100 mt-1 text-start payload-btn"
                                        data-payload="'">
                                    <code>'</code>
                                </button>
                                <small class="text-muted">Jika muncul error SQL = vulnerable!</small>
                            </div>

                            <div class="mb-3">
                                <strong>2. OR True (Tampilkan Semua):</strong>
                                <button class="btn btn-sm btn-outline-danger d-block w-100 mt-1 text-start payload-btn"
                                        data-payload="' OR '1'='1">
                                    <code>' OR '1'='1</code>
                                </button>
                                <small class="text-muted">Bypass filter, tampilkan semua produk</small>
                            </div>

                            <div class="mb-3">
                                <strong>3. UNION - Inject Fake Product:</strong>
                                <button class="btn btn-sm btn-outline-danger d-block w-100 mt-1 text-start payload-btn"
                                        data-payload="' UNION SELECT 999, 'HACKED!', 0.00, 'Injected via SQLi', 0, NOW(), NOW()-- ">
                                    <code class="small">UNION SELECT 999,'HACKED!',...</code>
                                </button>
                                <small class="text-muted">Inject produk palsu ke hasil</small>
                            </div>

                            <div class="mb-3">
                                <strong>4. UNION - Lihat Database Version:</strong>
                                <button class="btn btn-sm btn-outline-danger d-block w-100 mt-1 text-start payload-btn"
                                        data-payload="' UNION SELECT 1, version(), 0, current_database(), 0, NOW(), NOW()-- ">
                                    <code class="small">UNION SELECT version()...</code>
                                </button>
                                <small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Bocorkan info database!</small>
                            </div>

                            <div class="mb-3">
                                <strong>5. Comment Out (Abaikan Sisa Query):</strong>
                                <button class="btn btn-sm btn-outline-danger d-block w-100 mt-1 text-start payload-btn"
                                        data-payload="laptop' OR 1=1-- ">
                                    <code>laptop' OR 1=1-- </code>
                                </button>
                                <small class="text-muted">Cari "laptop" + bypass dengan OR 1=1</small>
                            </div>

                            {{-- MySQL vs PostgreSQL Note --}}
                            <div class="alert alert-warning py-2 small mt-3 mb-0">
                                <i class="bi bi-info-circle"></i> <strong>Catatan:</strong><br>
                                • <code>--</code> (double dash + space) = Comment di PostgreSQL & MySQL<br>
                                • <code>#</code> = Comment hanya di MySQL
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Results --}}
                <div class="col-md-8">
                    {{-- Query Executed --}}
                    @if(isset($query))
                    <div class="card mb-4 border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-code"></i> Query yang Dieksekusi
                            </h6>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded mb-0 small text-wrap"><code>{{ $query }}</code></pre>
                            <div class="mt-2 small text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Perhatikan bagaimana input user langsung masuk ke query!
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Error Display --}}
                    @if(isset($error))
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">
                            <i class="bi bi-x-circle"></i> SQL Error
                        </h6>
                        <pre class="mb-0 small text-wrap">{{ $error }}</pre>
                        <hr>
                        <small>
                            <i class="bi bi-lightbulb"></i>
                            Error ini menunjukkan vulnerability! Attacker bisa menggunakan informasi ini.
                        </small>
                    </div>
                    @endif

                    {{-- Results Table --}}
                    @if(isset($products) && count($products) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-table"></i>
                                Hasil Pencarian ({{ count($products) }} item)
                            </h6>
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
                                            <td>{{ $product->id }}</td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if(str_contains($product->name ?? '', '@'))
                                                    <span class="badge bg-danger ms-1">LEAKED!</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(is_numeric($product->price))
                                                    Rp {{ number_format($product->price) }}
                                                @else
                                                    <span class="text-danger">{{ $product->price }}</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ Str::limit($product->description ?? '-', 30) }}</td>
                                            <td>{{ $product->stock ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @elseif(isset($search) && !isset($error))
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Tidak ada produk ditemukan untuk "{{ $search }}"
                    </div>
                    @endif

                    {{-- No Search Yet --}}
                    @if(!isset($search))
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-search fs-1 text-muted"></i>
                            <h5 class="mt-3">Mulai Pencarian</h5>
                            <p class="text-muted">
                                Masukkan kata kunci atau coba payload SQL Injection
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vulnerable Code Explanation --}}
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-code-slash"></i> Kode Vulnerable (Controller)
                    </h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded"><code><span class="text-secondary">// JANGAN LAKUKAN INI!</span>
public function vulnerableSearch(Request $request)
{
    $search = $request->input('search', '');

    <span class="text-danger">// VULNERABLE: String concatenation</span>
    $query = "SELECT * FROM sqli_lab_products
              WHERE name LIKE '%<span class="text-danger">{$search}</span>%'";

    try {
        $products = DB::select($query);
    } catch (\Exception $e) {
        <span class="text-warning">// Exposing error = SECURITY RISK!</span>
        $error = $e->getMessage();
    }
}</code></pre>
                    <div class="alert alert-danger mt-3 mb-0">
                        <strong>Masalah:</strong>
                        <ul class="mb-0">
                            <li>Input user langsung dimasukkan ke query tanpa escape/sanitize</li>
                            <li>Error message ditampilkan ke user (information disclosure)</li>
                            <li>Tidak ada validasi input sama sekali</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('sqli-lab.how-it-works') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Cara Kerja SQLi
                </a>
                <a href="{{ route('sqli-lab.vulnerable-login') }}" class="btn btn-danger">
                    Vulnerable Login <i class="bi bi-arrow-right"></i>
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
