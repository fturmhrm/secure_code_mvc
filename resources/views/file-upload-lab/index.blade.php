{{--
    File Upload Lab - Minggu 5 Hari 3 & 4
    Overview + Hands-on Lab untuk File Upload Security
--}}

@extends('layouts.app')

@section('title', 'File Upload Lab')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">
            <i class="bi bi-cloud-upload text-primary"></i>
            File Upload Security Lab
        </h1>
        <p class="lead text-muted">
            Minggu 5 - Hari 3 & 4: Logging, Monitoring & File Upload Vulnerability
        </p>
        <div class="d-flex justify-content-center gap-2">
            <span class="badge bg-info fs-6 px-3 py-2">
                <i class="bi bi-journal-text"></i> Overview + Materi
            </span>
            <span class="badge bg-danger fs-6 px-3 py-2">
                <i class="bi bi-bug"></i> Hands-on Lab
            </span>
        </div>
    </div>

    {{-- OWASP Reference --}}
    <div class="alert alert-danger mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle fs-3 me-3"></i>
            <div>
                <strong>OWASP A09:2025 - Security Logging and Monitoring Failures</strong>
                <p class="mb-0 small">
                    File upload yang tidak aman dapat menyebabkan Remote Code Execution (RCE),
                    Server-Side Request Forgery (SSRF), XSS, dan berbagai serangan lainnya.
                </p>
            </div>
        </div>
    </div>

    {{-- Ethics Warning --}}
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-start">
            <i class="bi bi-shield-exclamation fs-3 me-3"></i>
            <div>
                <strong>Ethical Hacking Disclaimer</strong>
                <p class="small mb-2">Lab ini HANYA untuk tujuan pembelajaran di environment lokal!</p>
                <div class="row small">
                    <div class="col-md-6">
                        <span class="text-success">✅ Boleh:</span>
                        <ul class="mb-0">
                            <li>Praktik di localhost/environment sendiri</li>
                            <li>Gunakan ilmu untuk defensive purpose</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <span class="text-danger">❌ Dilarang:</span>
                        <ul class="mb-0">
                            <li>Menyerang sistem tanpa izin</li>
                            <li>Menyebarkan malware</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Learning Path Cards --}}
    <div class="row g-4 mb-5">

        {{-- Card 1: Overview Logging --}}
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Logging</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title">Secure Logging</h6>
                    <p class="card-text small text-muted">
                        Memahami pentingnya logging, konfigurasi Monolog, dan apa yang boleh/tidak boleh di-log.
                    </p>
                    <ul class="small mb-3">
                        <li>Log Levels (RFC 5424)</li>
                        <li>Structured Logging</li>
                        <li>Sensitive Data Masking</li>
                        <li>Log Rotation</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.overview', ['section' => 'logging']) }}" class="btn btn-info btn-sm w-100">
                        <i class="bi bi-book"></i> Lihat Materi
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 2: Overview File Upload --}}
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-cloud-arrow-up"></i> File Upload Basics</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title">File Upload Basics</h6>
                    <p class="card-text small text-muted">
                        Memahami risiko keamanan file upload dan dasar-dasar implementasi yang aman.
                    </p>
                    <ul class="small mb-3">
                        <li>Upload Security Risks</li>
                        <li>Validation Techniques</li>
                        <li>Secure Storage</li>
                        <li>Serving Files Safely</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.overview', ['section' => 'upload-basics']) }}" class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-book"></i> Lihat Materi
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3: Vulnerable Lab --}}
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-bug"></i> Lab Vulnerable</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title">Vulnerable Upload</h6>
                    <p class="card-text small text-muted">
                        Hands-on lab untuk memahami berbagai teknik bypass dan eksploitasi file upload.
                    </p>
                    <ul class="small mb-3">
                        <li>Level 1: No Validation</li>
                        <li>Level 2: Client-side Only</li>
                        <li>Level 3: Blacklist Bypass</li>
                        <li>Level 4: MIME Type Bypass</li>
                        <li>Level 5: Magic Bytes</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.vulnerable.index') }}" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-unlock"></i> Masuk Lab
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 4: Secure Implementation --}}
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-shield-check"></i> Lab Secure</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title">Secure Upload</h6>
                    <p class="card-text small text-muted">
                        Implementasi file upload yang aman dengan defense in depth.
                    </p>
                    <ul class="small mb-3">
                        <li>Whitelist Validation</li>
                        <li>Magic Bytes Check</li>
                        <li>Content Analysis</li>
                        <li>Secure Storage</li>
                        <li>Safe Serving</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.secure.index') }}" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-lock"></i> Lihat Implementasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Attack Overview --}}
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-diagram-3"></i> File Upload Attack Vectors</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 bg-danger bg-opacity-10 rounded h-100">
                        <h6 class="text-danger"><i class="bi bi-terminal"></i> Remote Code Execution (RCE)</h6>
                        <p class="small mb-0">
                            Upload file PHP/script yang dieksekusi server → Full server compromise.
                            <br><code class="small">shell.php → system($_GET['cmd'])</code>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 bg-warning bg-opacity-10 rounded h-100">
                        <h6 class="text-warning"><i class="bi bi-code-slash"></i> Cross-Site Scripting (XSS)</h6>
                        <p class="small mb-0">
                            Upload SVG/HTML dengan embedded JavaScript → Steal cookies, session hijacking.
                            <br><code class="small">&lt;svg onload="alert(1)"&gt;</code>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 bg-info bg-opacity-10 rounded h-100">
                        <h6 class="text-info"><i class="bi bi-folder-symlink"></i> Directory Traversal</h6>
                        <p class="small mb-0">
                            Filename manipulation → Overwrite system files.
                            <br><code class="small">../../../etc/passwd</code>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- OS Compatibility Note --}}
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-pc-display"></i> Catatan Kompatibilitas OS</h5>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-3">
                Lab ini dirancang untuk kompatibel di <strong>Linux</strong> dan <strong>Windows</strong>.
                Beberapa perbedaan yang perlu diperhatikan:
            </p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Aspek</th>
                            <th><i class="bi bi-ubuntu"></i> Linux</th>
                            <th><i class="bi bi-windows"></i> Windows</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <tr>
                            <td>Command Info User</td>
                            <td><code>id</code> atau <code>whoami</code></td>
                            <td><code>whoami</code></td>
                        </tr>
                        <tr>
                            <td>List Directory</td>
                            <td><code>ls -la</code></td>
                            <td><code>dir</code></td>
                        </tr>
                        <tr>
                            <td>Read File</td>
                            <td><code>cat /etc/passwd</code></td>
                            <td><code>type C:\Windows\win.ini</code></td>
                        </tr>
                        <tr>
                            <td>Path Separator</td>
                            <td><code>/</code></td>
                            <td><code>\</code></td>
                        </tr>
                        <tr>
                            <td>PHP Execution</td>
                            <td>Sama (Apache/Nginx + PHP)</td>
                            <td>Sama (XAMPP/Laragon)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info py-2 mb-0 small">
                <i class="bi bi-info-circle"></i>
                Lab akan otomatis mendeteksi OS dan menyesuaikan command yang ditampilkan.
            </div>
        </div>
    </div>

</div>
@endsection
