@extends('layouts.app')

@section('title', 'Level 2: Client-Side Only')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <span class="badge bg-warning text-dark me-2">Level 2</span>
                Client-Side Validation Only
            </h2>
            <p class="text-muted mb-0">Validasi JavaScript di browser - mudah di-bypass!</p>
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
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> "Protected" Upload Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('file-upload-lab.vulnerable.level2') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select Image File</label>
                            {{-- Client-side validation via accept attribute --}}
                            <input type="file" name="file" class="form-control" id="fileInput"
                                   accept=".jpg,.jpeg,.png,.gif" required>
                            <div class="form-text text-success">
                                <i class="bi bi-shield-check"></i>
                                Only .jpg, .jpeg, .png, .gif files allowed (client-side)
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning" id="submitBtn">
                            <i class="bi bi-upload"></i> Upload Image
                        </button>
                    </form>

                    {{-- JavaScript Validation --}}
                    <script>
                        document.getElementById('uploadForm').addEventListener('submit', function(e) {
                            const fileInput = document.getElementById('fileInput');
                            const file = fileInput.files[0];

                            if (file) {
                                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                                const allowedExts = ['.jpg', '.jpeg', '.png', '.gif'];

                                // Check extension
                                const fileName = file.name.toLowerCase();
                                const hasValidExt = allowedExts.some(ext => fileName.endsWith(ext));

                                if (!hasValidExt) {
                                    e.preventDefault();
                                    alert('Only image files (.jpg, .png, .gif) are allowed!');
                                    return false;
                                }
                            }
                        });
                    </script>

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
<pre class="text-light mb-0 small"><code><span class="text-secondary">&lt;!-- HTML Form --&gt;</span>
<span class="text-primary">&lt;input</span> type=<span class="text-success">"file"</span> accept=<span class="text-success">".jpg,.png,.gif"</span><span class="text-primary">&gt;</span>

<span class="text-secondary">&lt;!-- JavaScript Validation --&gt;</span>
<span class="text-primary">&lt;script&gt;</span>
form.addEventListener(<span class="text-success">'submit'</span>, <span class="text-primary">function</span>(e) {
    <span class="text-primary">const</span> allowedExts = [<span class="text-success">'.jpg'</span>, <span class="text-success">'.png'</span>];
    <span class="text-primary">if</span> (!hasValidExt) {
        e.preventDefault();
        alert(<span class="text-success">'Only images allowed!'</span>);
    }
});
<span class="text-primary">&lt;/script&gt;</span>

<span class="text-secondary">// Server: Still no validation!</span>
$file->move(public_path(<span class="text-success">'uploads'</span>), $name);</code></pre>
                </div>
            </div>
        </div>

        {{-- Bypass Instructions --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-unlock"></i> Bypass Techniques</h5>
                </div>
                <div class="card-body">
                    <h6><span class="badge bg-success">✓ Tested</span> Method 1: Disable JavaScript</h6>
                    <ol class="small">
                        <li>Buka DevTools (F12)</li>
                        <li>Go to Settings → Preferences</li>
                        <li>Scroll ke "Debugger" → Disable JavaScript</li>
                        <li>Upload file PHP langsung</li>
                    </ol>

                    <h6><span class="badge bg-secondary">Alternative</span> Method 2: Modify accept Attribute</h6>
                    <ol class="small text-muted">
                        <li>Buka DevTools (F12)</li>
                        <li>Inspect input element</li>
                        <li>Ubah <code>accept=".jpg,.png"</code> menjadi <code>accept="*"</code></li>
                        <li>Atau hapus attribute accept</li>
                    </ol>
                    <div class="alert alert-secondary py-1 small mb-3">
                        <i class="bi bi-info-circle"></i>
                        <em>Note: accept attribute hanya hint untuk file picker, JS validation tetap berjalan. Combine dengan Method 1.</em>
                    </div>

                    <h6><span class="badge bg-success">✓ Tested</span> Method 3: Intercept dengan Burp Suite</h6>
                    <ol class="small">
                        <li>Setup Burp sebagai proxy</li>
                        <li>Upload file .jpg (valid) atau langsung shell.php</li>
                        <li>Intercept request</li>
                        <li>Ubah filename dan content menjadi PHP shell</li>
                        <li>Forward request</li>
                    </ol>

                    <h6><span class="badge bg-secondary">Reference</span> Method 4: cURL Request</h6>
                    <div class="bg-dark text-light p-2 rounded small mb-2">
<pre class="mb-0"><code>curl -X POST \
  -F "file=@shell.php" \
  -F "_token={{ csrf_token() }}" \
  {{ route('file-upload-lab.vulnerable.level2') }}</code></pre>
                    </div>
                    <div class="alert alert-warning py-1 small mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <em>cURL memerlukan valid session cookie untuk CSRF. Gunakan Burp Suite untuk kemudahan.</em>
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

    {{-- Lesson Learned --}}
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Lesson Learned</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning mb-3">
                <h6><i class="bi bi-exclamation-triangle"></i> Golden Rule:</h6>
                <p class="mb-0">
                    <strong>NEVER trust client-side validation!</strong>
                    Client-side validation hanya untuk UX (User Experience), bukan security.
                    Attacker memiliki kontrol penuh atas browser dan request.
                </p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-danger">Client-Side Validation:</h6>
                    <ul class="small">
                        <li>Bisa di-disable dengan JavaScript off</li>
                        <li>Bisa di-bypass dengan DevTools</li>
                        <li>Bisa di-bypass dengan proxy tools</li>
                        <li>Bisa di-bypass dengan direct request</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success">Server-Side Validation:</h6>
                    <ul class="small">
                        <li>Tidak bisa di-bypass dari browser</li>
                        <li>Memeriksa file yang benar-benar diterima</li>
                        <li>Merupakan satu-satunya defense yang reliable</li>
                        <li>Harus selalu diimplementasikan!</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
