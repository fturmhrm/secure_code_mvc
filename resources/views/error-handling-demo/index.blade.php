{{--
    Demo Error Handling - Minggu 5 Hari 2
    Halaman showcase untuk menampilkan perbedaan Secure vs Vulnerable Error Handling
    BUKAN lab interaktif - hanya demonstrasi visual dengan penjelasan step-by-step
--}}

@extends('layouts.app')

@section('title', 'Demo: Error Handling & Information Disclosure')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">
            <i class="bi bi-exclamation-octagon text-danger"></i>
            Error Handling & Information Disclosure
        </h1>
        <p class="lead text-muted">
            Minggu 5 - Hari 2: "Errors Should Never Reach Users"
        </p>
        <div class="badge bg-info fs-6 px-3 py-2">
            <i class="bi bi-eye"></i> Demo Showcase - Bukan Lab Interaktif
        </div>
    </div>

    {{-- OWASP Reference --}}
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-exclamation fs-3 me-3"></i>
            <div>
                <strong>OWASP A02:2025 - Security Misconfiguration</strong>
                <p class="mb-0 small">
                    Information Disclosure melalui error messages adalah salah satu misconfiguration paling umum.
                    Debug mode yang aktif di production dapat mengekspos informasi sensitif ke attacker.
                </p>
            </div>
        </div>
    </div>

    {{-- Learning Path --}}
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-map"></i> Learning Path - 8 Topik</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-danger w-100 text-start" onclick="showTab('info-disclosure-tab')">
                        <span class="badge bg-danger me-2">1</span> Information Disclosure
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-warning w-100 text-start" onclick="showTab('error-response-tab')">
                        <span class="badge bg-warning text-dark me-2">2</span> Error Response API
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-info w-100 text-start" onclick="showTab('exception-handler-tab')">
                        <span class="badge bg-info me-2">3</span> Exception Handler
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-success w-100 text-start" onclick="showTab('validation-db-tab')">
                        <span class="badge bg-success me-2">4</span> Validation & DB Errors
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-secondary w-100 text-start" onclick="showTab('error-pages-tab')">
                        <span class="badge bg-secondary me-2">5</span> Custom Error Pages
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-primary w-100 text-start" onclick="showTab('error-id-tab')">
                        <span class="badge bg-primary me-2">6</span> Error ID Pattern
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-dark w-100 text-start" onclick="showTab('http-headers-tab')">
                        <span class="badge bg-dark me-2">7</span> Hide Server Info
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="btn btn-outline-success w-100 text-start" onclick="showTab('complete-handler-tab')">
                        <span class="badge bg-success me-2">8</span> Complete Handler
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs mb-4" id="demoTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-dark" id="info-disclosure-tab" data-bs-toggle="tab" data-bs-target="#info-disclosure" type="button">
                <i class="bi bi-eye-slash"></i> <span class="d-none d-md-inline">1.</span> Info Disclosure
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="error-response-tab" data-bs-toggle="tab" data-bs-target="#error-response" type="button">
                <i class="bi bi-braces"></i> <span class="d-none d-md-inline">2.</span> Error Response
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="exception-handler-tab" data-bs-toggle="tab" data-bs-target="#exception-handler" type="button">
                <i class="bi bi-code-slash"></i> <span class="d-none d-md-inline">3.</span> Exception Handler
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="validation-db-tab" data-bs-toggle="tab" data-bs-target="#validation-db" type="button">
                <i class="bi bi-database-exclamation"></i> <span class="d-none d-md-inline">4.</span> Validation & DB
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="error-pages-tab" data-bs-toggle="tab" data-bs-target="#error-pages" type="button">
                <i class="bi bi-file-earmark-x"></i> <span class="d-none d-md-inline">5.</span> Error Pages
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="error-id-tab" data-bs-toggle="tab" data-bs-target="#error-id" type="button">
                <i class="bi bi-bookmark"></i> <span class="d-none d-md-inline">6.</span> Error ID
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="http-headers-tab" data-bs-toggle="tab" data-bs-target="#http-headers" type="button">
                <i class="bi bi-hdd-network"></i> <span class="d-none d-md-inline">7.</span> Headers
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-dark" id="complete-handler-tab" data-bs-toggle="tab" data-bs-target="#complete-handler" type="button">
                <i class="bi bi-check2-all"></i> <span class="d-none d-md-inline">8.</span> Complete
            </button>
        </li>
    </ul>

    {{-- Tab Contents --}}
    <div class="tab-content" id="demoTabsContent">

        {{-- Tab 1: Information Disclosure --}}
        <div class="tab-pane fade show active" id="info-disclosure" role="tabpanel">
            @include('error-handling-demo.partials.info-disclosure')
        </div>

        {{-- Tab 2: Error Response --}}
        <div class="tab-pane fade" id="error-response" role="tabpanel">
            @include('error-handling-demo.partials.error-response')
        </div>

        {{-- Tab 3: Exception Handler --}}
        <div class="tab-pane fade" id="exception-handler" role="tabpanel">
            @include('error-handling-demo.partials.exception-handler')
        </div>

        {{-- Tab 4: Validation & Database Errors --}}
        <div class="tab-pane fade" id="validation-db" role="tabpanel">
            @include('error-handling-demo.partials.validation-db')
        </div>

        {{-- Tab 5: Custom Error Pages --}}
        <div class="tab-pane fade" id="error-pages" role="tabpanel">
            @include('error-handling-demo.partials.error-pages')
        </div>

        {{-- Tab 6: Error ID Pattern --}}
        <div class="tab-pane fade" id="error-id" role="tabpanel">
            @include('error-handling-demo.partials.error-id')
        </div>

        {{-- Tab 7: HTTP Headers --}}
        <div class="tab-pane fade" id="http-headers" role="tabpanel">
            @include('error-handling-demo.partials.http-headers')
        </div>

        {{-- Tab 8: Complete Handler --}}
        <div class="tab-pane fade" id="complete-handler" role="tabpanel">
            @include('error-handling-demo.partials.complete-handler')
        </div>

    </div>

    {{-- Summary Card --}}
    <div class="card mt-5 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-check2-square"></i> Checklist Production</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success"><i class="bi bi-shield-check"></i> Yang HARUS dilakukan:</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> <code>APP_DEBUG=false</code></li>
                        <li><i class="bi bi-check-circle text-success"></i> <code>APP_ENV=production</code></li>
                        <li><i class="bi bi-check-circle text-success"></i> Custom error pages (403, 404, 500)</li>
                        <li><i class="bi bi-check-circle text-success"></i> Generic error messages untuk user</li>
                        <li><i class="bi bi-check-circle text-success"></i> Detail error hanya di internal log</li>
                        <li><i class="bi bi-check-circle text-success"></i> Hapus X-Powered-By header</li>
                        <li><i class="bi bi-check-circle text-success"></i> Block akses ke .env, .git</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger"><i class="bi bi-x-octagon"></i> Yang TIDAK BOLEH dilakukan:</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-x-circle text-danger"></i> Tampilkan stack trace ke user</li>
                        <li><i class="bi bi-x-circle text-danger"></i> Expose database error message</li>
                        <li><i class="bi bi-x-circle text-danger"></i> Tampilkan file path server</li>
                        <li><i class="bi bi-x-circle text-danger"></i> Expose versi framework/library</li>
                        <li><i class="bi bi-x-circle text-danger"></i> Tinggalkan phpinfo.php di server</li>
                        <li><i class="bi bi-x-circle text-danger"></i> HTML comments berisi info sensitif</li>
                        <li><i class="bi bi-x-circle text-danger"></i> API response dengan query SQL</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function showTab(tabId) {
    const tab = document.getElementById(tabId);
    if (tab) {
        const bsTab = new bootstrap.Tab(tab);
        bsTab.show();
    }
}
</script>
@endsection
