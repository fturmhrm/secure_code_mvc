@extends('layouts.app')

@section('title', 'Vulnerable Upload Lab')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-bug text-danger"></i> Vulnerable File Upload Lab</h2>
            <p class="text-muted mb-0">Hands-on practice dengan berbagai tingkat kerentanan upload</p>
        </div>
        <div>
            <a href="{{ route('file-upload-lab.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Warning Banner --}}
    <div class="alert alert-danger mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
            <div>
                <h5 class="mb-1">Educational Environment Only!</h5>
                <p class="mb-0 small">
                    Lab ini SENGAJA dibuat vulnerable. Files yang di-upload bisa dieksekusi di server.
                    <strong>Jangan upload file berbahaya ke server production!</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- Clear Files Button --}}
    <div class="mb-4">
        <form action="{{ route('file-upload-lab.vulnerable.clear') }}" method="POST" class="d-inline"
              onsubmit="return confirm('Clear semua file yang sudah di-upload?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i> Clear All Uploaded Files
            </button>
        </form>
        <a href="{{ route('file-upload-lab.vulnerable.files') }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-folder"></i> View Uploaded Files
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Lab Levels --}}
    <div class="row">
        {{-- Level 1 --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-1-circle"></i> Level 1: No Validation
                        <span class="badge bg-light text-danger float-end">Sangat Mudah</span>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small">Tidak ada validasi sama sekali. File langsung disimpan di public directory.</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>// No validation at all!</code><br>
                        <code>$file->move(public_path('uploads'));</code>
                    </div>
                    <h6 class="text-danger">Attack Vector:</h6>
                    <ul class="small mb-0">
                        <li>Upload shell.php langsung</li>
                        <li>Akses: /uploads/vulnerable/shell.php?cmd=whoami</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.vulnerable.level1') }}" class="btn btn-danger w-100">
                        <i class="bi bi-play-fill"></i> Try Level 1
                    </a>
                </div>
            </div>
        </div>

        {{-- Level 2 --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-2-circle"></i> Level 2: Client-Side Only
                        <span class="badge bg-light text-warning float-end">Mudah</span>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small">Hanya validasi JavaScript di browser. Tidak ada server-side validation.</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>&lt;input accept=".jpg,.png"&gt;</code><br>
                        <code>// But no server validation!</code>
                    </div>
                    <h6 class="text-warning">Bypass:</h6>
                    <ul class="small mb-0">
                        <li>Disable JavaScript di browser</li>
                        <li>Modify accept attribute via DevTools</li>
                        <li>Gunakan Burp Suite / curl</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.vulnerable.level2') }}" class="btn btn-warning w-100">
                        <i class="bi bi-play-fill"></i> Try Level 2
                    </a>
                </div>
            </div>
        </div>

        {{-- Level 3 --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-3-circle"></i> Level 3: Blacklist
                        <span class="badge bg-light text-info float-end">Medium</span>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small">Menggunakan blacklist extension yang tidak lengkap dan case-sensitive.</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>$blacklist = ['php', 'exe'];</code><br>
                        <code>// Case-sensitive check!</code>
                    </div>
                    <h6 class="text-info">Bypass:</h6>
                    <ul class="small mb-0">
                        <li><code>shell.PHP</code> (case variation)</li>
                        <li><code>shell.php5</code>, <code>.phtml</code></li>
                        <li><code>shell.php.jpg</code> (double ext)</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.vulnerable.level3') }}" class="btn btn-info w-100">
                        <i class="bi bi-play-fill"></i> Try Level 3
                    </a>
                </div>
            </div>
        </div>

        {{-- Level 4 --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-4-circle"></i> Level 4: MIME Type
                        <span class="badge bg-light text-primary float-end">Medium</span>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small">Validasi Content-Type header dari request. Header bisa di-spoof!</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>$mime = $file->getMimeType();</code><br>
                        <code>// Reads from header, not content!</code>
                    </div>
                    <h6 class="text-primary">Bypass:</h6>
                    <ul class="small mb-0">
                        <li>Intercept dengan Burp Suite</li>
                        <li>Change Content-Type to image/jpeg</li>
                        <li>Upload shell.php dengan fake MIME</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.vulnerable.level4') }}" class="btn btn-primary w-100">
                        <i class="bi bi-play-fill"></i> Try Level 4
                    </a>
                </div>
            </div>
        </div>

        {{-- Level 5 --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-5-circle"></i> Level 5: Magic Bytes
                        <span class="badge bg-light text-success float-end">Hard</span>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small">Validasi magic bytes dari isi file. Tapi masih menyimpan original filename!</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>$finfo = new finfo(FILEINFO_MIME);</code><br>
                        <code>// Checks content but keeps extension</code>
                    </div>
                    <h6 class="text-success">Bypass:</h6>
                    <ul class="small mb-0">
                        <li>Polyglot file: GIF89a + PHP code</li>
                        <li>File dimulai dengan magic bytes valid</li>
                        <li>PHP code ditambahkan setelahnya</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.vulnerable.level5') }}" class="btn btn-success w-100">
                        <i class="bi bi-play-fill"></i> Try Level 5
                    </a>
                </div>
            </div>
        </div>

        {{-- Secure Implementation Link --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check"></i> Secure Implementation
                        <span class="badge bg-light text-secondary float-end">Solution</span>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small">Lihat bagaimana implementasi yang aman dengan multiple validation layers.</p>
                    <ul class="small mb-0">
                        <li>Whitelist extension & MIME</li>
                        <li>Magic bytes validation</li>
                        <li>Random filename</li>
                        <li>Store outside public</li>
                        <li>Serve via controller</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('file-upload-lab.secure.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-right"></i> View Secure Implementation
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- OS Command Reference --}}
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-terminal"></i> OS Command Reference untuk RCE Testing</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm small">
                    <thead class="table-dark">
                        <tr>
                            <th>Action</th>
                            <th><i class="bi bi-ubuntu"></i> Linux</th>
                            <th><i class="bi bi-windows"></i> Windows</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Current User</td>
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
                            <td>Current Directory</td>
                            <td><code>pwd</code></td>
                            <td><code>cd</code></td>
                        </tr>
                        <tr>
                            <td>Network Info</td>
                            <td><code>ifconfig</code> atau <code>ip a</code></td>
                            <td><code>ipconfig</code></td>
                        </tr>
                        <tr>
                            <td>System Info</td>
                            <td><code>uname -a</code></td>
                            <td><code>systeminfo</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-warning py-2 mb-0">
                <i class="bi bi-lightbulb"></i>
                <strong>Tip:</strong> Setelah upload shell.php, akses dengan parameter:
                <code>?cmd=whoami</code> (Linux & Windows) atau <code>?cmd=id</code> (Linux only)
            </div>
        </div>
    </div>
</div>
@endsection
