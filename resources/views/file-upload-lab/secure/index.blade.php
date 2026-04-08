@extends('layouts.app')

@section('title', 'Secure File Upload')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-shield-check text-success"></i> Secure File Upload Implementation</h2>
            <p class="text-muted mb-0">Defense-in-depth file upload dengan multiple validation layers</p>
        </div>
        <div>
            <a href="{{ route('file-upload-lab.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Lab
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Upload Form --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Secure Upload Form</h5>
                </div>
                <div class="card-body">
                    {{-- Validation Rules --}}
                    <div class="alert alert-success py-2 mb-3">
                        <strong><i class="bi bi-shield-check"></i> Security Layers:</strong>
                        <ul class="mb-0 small mt-2">
                            <li>✅ Whitelist extensions: {{ implode(', ', $allowedExtensions) }}</li>
                            <li>✅ Magic bytes validation</li>
                            <li>✅ Image integrity check</li>
                            <li>✅ Max size: {{ $maxFileSize }}</li>
                            <li>✅ Random filename</li>
                            <li>✅ Stored outside public</li>
                        </ul>
                    </div>

                    <form action="{{ route('file-upload-lab.secure.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select Image File</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                                   accept=".jpg,.jpeg,.png,.gif,.webp" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-success">
                                <i class="bi bi-info-circle"></i>
                                Only valid images are accepted. Files are stored securely.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Upload Securely
                        </button>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Secure Code --}}
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-code-slash"></i> Secure Implementation</h6>
                </div>
                <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// SecureUploadController.php</span>

<span class="text-primary">public function</span> <span class="text-info">upload</span>(Request $request)
{
    <span class="text-secondary">// 1. Basic validation</span>
    $request->validate([<span class="text-success">'file'</span> => <span class="text-success">'required|file|max:5120'</span>]);

    <span class="text-secondary">// 2. Whitelist extension (case-insensitive)</span>
    $ext = strtolower($file->getClientOriginalExtension());
    <span class="text-primary">if</span> (!in_array($ext, $this->allowedExtensions)) {
        <span class="text-primary">return</span> back()->withErrors([<span class="text-success">'Invalid extension'</span>]);
    }

    <span class="text-secondary">// 3. Magic bytes validation</span>
    $finfo = <span class="text-primary">new</span> finfo(FILEINFO_MIME_TYPE);
    $realMime = $finfo->file($file->getPathname());
    <span class="text-primary">if</span> (!in_array($realMime, $this->allowedMimeTypes)) {
        <span class="text-primary">return</span> back()->withErrors([<span class="text-success">'Invalid content'</span>]);
    }

    <span class="text-secondary">// 4. Image integrity check</span>
    <span class="text-primary">if</span> (!$this->isValidImage($file->getPathname())) {
        <span class="text-primary">return</span> back()->withErrors([<span class="text-success">'Corrupted image'</span>]);
    }

    <span class="text-secondary">// 5. Random filename (prevent traversal)</span>
    $filename = Str::uuid() . <span class="text-success">'.'</span> . $ext;

    <span class="text-secondary">// 6. Store OUTSIDE public</span>
    $file->storeAs(<span class="text-success">'secure-uploads'</span>, $filename, <span class="text-success">'local'</span>);

    <span class="text-secondary">// 7. Log the upload</span>
    Log::info(<span class="text-success">'File uploaded'</span>, [...]);
}</code></pre>
                </div>
            </div>
        </div>

        {{-- Uploaded Files --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-folder-check"></i> Securely Uploaded Files</h5>
                    @if(count($uploadedFiles) > 0)
                        <form action="{{ route('file-upload-lab.secure.clear') }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Clear all files?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light">
                                <i class="bi bi-trash"></i> Clear All
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    @if(count($uploadedFiles) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Original Name</th>
                                        <th>Stored As</th>
                                        <th>Size</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @foreach($uploadedFiles as $file)
                                        <tr>
                                            <td>{{ Str::limit($file['original_name'], 20) }}</td>
                                            <td><code class="small">{{ Str::limit($file['stored_name'], 15) }}</code></td>
                                            <td>{{ number_format($file['size'] / 1024, 1) }} KB</td>
                                            <td>
                                                <a href="{{ route('file-upload-lab.secure.serve', $file['stored_name']) }}"
                                                   target="_blank" class="btn btn-sm btn-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('file-upload-lab.secure.download', $file['stored_name']) }}"
                                                   class="btn btn-sm btn-info" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form action="{{ route('file-upload-lab.secure.delete', $file['stored_name']) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete this file?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-folder2 display-4"></i>
                            <p class="mt-3 mb-0">No files uploaded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Security Comparison --}}
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-shield-exclamation"></i> Vulnerable vs Secure Comparison</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Aspect</th>
                            <th class="table-danger">Vulnerable</th>
                            <th class="table-success">Secure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Extension Check</strong></td>
                            <td>None / Blacklist</td>
                            <td>✅ Whitelist, case-insensitive</td>
                        </tr>
                        <tr>
                            <td><strong>MIME Validation</strong></td>
                            <td>Header only (spoofable)</td>
                            <td>✅ Magic bytes (file content)</td>
                        </tr>
                        <tr>
                            <td><strong>Image Validation</strong></td>
                            <td>None</td>
                            <td>✅ getimagesize() check</td>
                        </tr>
                        <tr>
                            <td><strong>Filename</strong></td>
                            <td>Original (traversal risk)</td>
                            <td>✅ Random UUID</td>
                        </tr>
                        <tr>
                            <td><strong>Storage Location</strong></td>
                            <td>/public (directly accessible)</td>
                            <td>✅ /storage (not accessible)</td>
                        </tr>
                        <tr>
                            <td><strong>File Serving</strong></td>
                            <td>Direct URL</td>
                            <td>✅ Via controller with auth</td>
                        </tr>
                        <tr>
                            <td><strong>Response Headers</strong></td>
                            <td>None</td>
                            <td>✅ X-Content-Type-Options, CSP</td>
                        </tr>
                        <tr>
                            <td><strong>Logging</strong></td>
                            <td>Basic or none</td>
                            <td>✅ Detailed audit trail</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Secure Serving Code --}}
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-cloud-download"></i> Secure File Serving</h5>
        </div>
        <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// Serve file with security headers</span>
<span class="text-primary">public function</span> <span class="text-info">serve</span>(<span class="text-primary">string</span> $filename): Response
{
    <span class="text-secondary">// 1. Validate filename format (prevent traversal)</span>
    <span class="text-primary">if</span> (!preg_match(<span class="text-success">'/^[a-f0-9\-]+\.(jpg|jpeg|png|gif|webp)$/i'</span>, $filename)) {
        <span class="text-info">abort</span>(<span class="text-info">400</span>);
    }

    <span class="text-secondary">// 2. Check file exists</span>
    $path = storage_path(<span class="text-success">'app/secure-uploads/'</span> . $filename);
    <span class="text-primary">if</span> (!file_exists($path)) {
        <span class="text-info">abort</span>(<span class="text-info">404</span>);
    }

    <span class="text-secondary">// 3. Authorization check (in real app)</span>
    <span class="text-secondary">// $this->authorize('view', $file);</span>

    <span class="text-secondary">// 4. Return with security headers</span>
    <span class="text-primary">return</span> response()->file($path, [
        <span class="text-success">'Content-Type'</span> => $mimeType,
        <span class="text-success">'X-Content-Type-Options'</span> => <span class="text-success">'nosniff'</span>,        <span class="text-secondary">// Prevent MIME sniffing</span>
        <span class="text-success">'Content-Security-Policy'</span> => <span class="text-success">"default-src 'none'"</span>, <span class="text-secondary">// Block scripts</span>
        <span class="text-success">'Cache-Control'</span> => <span class="text-success">'private, max-age=3600'</span>,
    ]);
}</code></pre>
        </div>
    </div>
</div>
@endsection
