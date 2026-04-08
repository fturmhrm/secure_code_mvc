{{-- Partial: Overview File Upload --}}

{{-- Danger Intro --}}
<div class="alert alert-danger mb-4">
    <h5 class="mb-2"><i class="bi bi-exclamation-triangle-fill"></i> File Upload = High Risk Area</h5>
    <p class="mb-1">
        File upload adalah salah satu fitur <strong>paling berbahaya</strong> di web application.
        Jika tidak divalidasi dengan benar, attacker bisa:
    </p>
    <ul class="mb-0">
        <li><strong>Remote Code Execution (RCE)</strong> - Eksekusi kode apapun di server</li>
        <li><strong>Server Takeover</strong> - Kontrol penuh atas server</li>
        <li><strong>Data Breach</strong> - Akses seluruh database dan file</li>
        <li><strong>Lateral Movement</strong> - Menyebar ke sistem internal lain</li>
    </ul>
</div>

{{-- How Vulnerability Works --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Bagaimana Kerentanan Terjadi?</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-2">
                <div class="p-3 bg-primary bg-opacity-10 rounded">
                    <i class="bi bi-cloud-arrow-up display-6 text-primary"></i>
                    <p class="small mb-0 mt-2">1. Upload shell.php</p>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <i class="bi bi-arrow-right fs-3 text-muted"></i>
            </div>
            <div class="col-md-2">
                <div class="p-3 bg-warning bg-opacity-10 rounded">
                    <i class="bi bi-x-circle display-6 text-warning"></i>
                    <p class="small mb-0 mt-2">2. No/Weak Validation</p>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <i class="bi bi-arrow-right fs-3 text-muted"></i>
            </div>
            <div class="col-md-2">
                <div class="p-3 bg-info bg-opacity-10 rounded">
                    <i class="bi bi-folder display-6 text-info"></i>
                    <p class="small mb-0 mt-2">3. Stored in /public</p>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <i class="bi bi-arrow-right fs-3 text-muted"></i>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-danger bg-opacity-10 rounded">
                    <i class="bi bi-terminal display-6 text-danger"></i>
                    <p class="small mb-0 mt-2">4. Access & Execute!</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Common Attack Vectors --}}
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="bi bi-bug"></i> Attack Vectors</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                {{-- RCE via PHP Shell --}}
                <div class="mb-4">
                    <h6><i class="bi bi-terminal text-danger"></i> 1. Remote Code Execution (RCE)</h6>
                    <p class="small text-muted">Upload file PHP yang bisa eksekusi command sistem.</p>
                    <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">&lt;?php</span>
<span class="text-secondary">// Simple PHP web shell</span>
<span class="text-primary">if</span>(isset($_GET[<span class="text-success">'cmd'</span>])) {
    <span class="text-info">system</span>($_GET[<span class="text-success">'cmd'</span>]);
}
<span class="text-secondary">?&gt;</span>

<span class="text-secondary">// Access: /uploads/shell.php?cmd=whoami</span></code></pre>
                    </div>
                </div>

                {{-- Double Extension --}}
                <div class="mb-4">
                    <h6><i class="bi bi-files text-warning"></i> 2. Double Extension</h6>
                    <p class="small text-muted">Bypass dengan nama file seperti <code>shell.php.jpg</code>.</p>
                    <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">// Filenames to try:</span>
shell.php.jpg
shell.php.jpeg
shell.php.png
shell.pHp.jpg  <span class="text-secondary">// Case variation</span>
shell.php5     <span class="text-secondary">// Alternative extension</span></code></pre>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                {{-- XSS via SVG --}}
                <div class="mb-4">
                    <h6><i class="bi bi-file-image text-info"></i> 3. XSS via SVG</h6>
                    <p class="small text-muted">SVG adalah XML, bisa mengandung JavaScript.</p>
                    <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">&lt;?xml version="1.0"?&gt;</span>
<span class="text-primary">&lt;svg</span> xmlns=<span class="text-success">"http://www.w3.org/2000/svg"</span><span class="text-primary">&gt;</span>
  <span class="text-primary">&lt;script&gt;</span>
    <span class="text-info">alert</span>(<span class="text-success">'XSS via SVG!'</span>);
    <span class="text-secondary">// Steal cookies, redirect, etc.</span>
  <span class="text-primary">&lt;/script&gt;</span>
<span class="text-primary">&lt;/svg&gt;</span></code></pre>
                    </div>
                </div>

                {{-- Path Traversal --}}
                <div class="mb-4">
                    <h6><i class="bi bi-folder2-open text-success"></i> 4. Path Traversal</h6>
                    <p class="small text-muted">Write file ke lokasi berbahaya.</p>
                    <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">// Dangerous filenames:</span>
../../../config/.env
..\\..\\..\\config\\.env  <span class="text-secondary">// Windows</span>
....//....//shell.php

<span class="text-secondary">// Cron/SSH injection:</span>
../../../../var/spool/cron/root</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Common Bypass Techniques --}}
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-unlock"></i> Bypass Techniques yang Sering Berhasil</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered small">
                <thead class="table-warning">
                    <tr>
                        <th>Teknik</th>
                        <th>Apa yang di-bypass</th>
                        <th>Contoh</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Case Variation</strong></td>
                        <td>Blacklist case-sensitive</td>
                        <td><code>shell.pHP</code>, <code>shell.Php5</code></td>
                    </tr>
                    <tr>
                        <td><strong>Null Byte Injection</strong></td>
                        <td>Truncate filename</td>
                        <td><code>shell.php%00.jpg</code> (old PHP)</td>
                    </tr>
                    <tr>
                        <td><strong>MIME Type Spoof</strong></td>
                        <td>Content-Type check only</td>
                        <td>Send PHP with <code>image/jpeg</code> header</td>
                    </tr>
                    <tr>
                        <td><strong>Magic Bytes</strong></td>
                        <td>getimagesize() check</td>
                        <td><code>GIF89a&lt;?php system($_GET['cmd']); ?&gt;</code></td>
                    </tr>
                    <tr>
                        <td><strong>Double Extension</strong></td>
                        <td>Extension blacklist</td>
                        <td><code>shell.php.jpg</code>, <code>shell.php.png</code></td>
                    </tr>
                    <tr>
                        <td><strong>Alternative Ext</strong></td>
                        <td>Incomplete blacklist</td>
                        <td><code>.php3</code>, <code>.php5</code>, <code>.phtml</code>, <code>.phar</code></td>
                    </tr>
                    <tr>
                        <td><strong>.htaccess Upload</strong></td>
                        <td>Apache config</td>
                        <td>Upload <code>.htaccess</code> agar <code>.jpg</code> dieksekusi PHP</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Validation Layers --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-shield-check"></i> Defense in Depth - Multiple Validation Layers</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <small><i class="bi bi-1-circle"></i> Client-Side (Weak)</small>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2">Hanya UX, mudah di-bypass!</p>
                        <div class="bg-dark text-light p-2 rounded">
<pre class="mb-0"><code>&lt;input type="file"
       accept=".jpg,.png"&gt;

<span class="text-secondary">// Easily bypassed with DevTools
// or Burp Suite</span></code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning">
                        <small><i class="bi bi-2-circle"></i> MIME Type (Medium)</small>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2">Check Content-Type header.</p>
                        <div class="bg-dark text-light p-2 rounded">
<pre class="mb-0"><code>$request->validate([
    <span class="text-success">'file'</span> => <span class="text-success">'mimes:jpg,png'</span>
]);

<span class="text-secondary">// Can be spoofed in request!</span></code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <small><i class="bi bi-3-circle"></i> Magic Bytes (Strong)</small>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2">Read actual file content.</p>
                        <div class="bg-dark text-light p-2 rounded">
<pre class="mb-0"><code>$finfo = <span class="text-primary">new</span> finfo(FILEINFO_MIME);
$mime = $finfo->file($path);

<span class="text-secondary">// Check real content,
// not headers</span></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secure Implementation --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-code-square"></i> Secure File Upload Implementation</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// app/Services/SecureUploadService.php</span>

<span class="text-primary">class</span> <span class="text-warning">SecureUploadService</span>
{
    <span class="text-secondary">// 1. Whitelist extensions (NOT blacklist!)</span>
    <span class="text-primary">private array</span> $allowedExtensions = [<span class="text-success">'jpg'</span>, <span class="text-success">'jpeg'</span>, <span class="text-success">'png'</span>, <span class="text-success">'gif'</span>];

    <span class="text-secondary">// 2. Whitelist MIME types</span>
    <span class="text-primary">private array</span> $allowedMimeTypes = [
        <span class="text-success">'image/jpeg'</span>, <span class="text-success">'image/png'</span>, <span class="text-success">'image/gif'</span>
    ];

    <span class="text-primary">public function</span> <span class="text-info">upload</span>(UploadedFile $file): <span class="text-primary">string</span>
    {
        <span class="text-secondary">// 3. Validate extension (lowercase)</span>
        $ext = strtolower($file->getClientOriginalExtension());
        <span class="text-primary">if</span> (!in_array($ext, $this->allowedExtensions)) {
            <span class="text-primary">throw new</span> ValidationException(<span class="text-success">'Invalid file type'</span>);
        }

        <span class="text-secondary">// 4. Validate MIME type from file content (NOT header)</span>
        $finfo = <span class="text-primary">new</span> finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getPathname());
        <span class="text-primary">if</span> (!in_array($realMime, $this->allowedMimeTypes)) {
            <span class="text-primary">throw new</span> ValidationException(<span class="text-success">'Invalid file content'</span>);
        }

        <span class="text-secondary">// 5. Generate random filename (prevent traversal)</span>
        $filename = Str::uuid() . <span class="text-success">'.'</span> . $ext;

        <span class="text-secondary">// 6. Store OUTSIDE public directory</span>
        $path = $file->storeAs(<span class="text-success">'uploads'</span>, $filename, <span class="text-success">'private'</span>);

        <span class="text-secondary">// 7. Log the upload</span>
        Log::info(<span class="text-success">'File uploaded'</span>, [
            <span class="text-success">'user_id'</span> => auth()->id(),
            <span class="text-success">'original_name'</span> => $file->getClientOriginalName(),
            <span class="text-success">'stored_as'</span> => $filename,
            <span class="text-success">'size'</span> => $file->getSize(),
        ]);

        <span class="text-primary">return</span> $path;
    }
}</code></pre>
    </div>
</div>

{{-- Storage Location --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-folder-check"></i> Storage Location Best Practices</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-danger">❌ Vulnerable: Store in /public</h6>
                <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">// Files langsung accessible!</span>
$file->move(public_path(<span class="text-success">'uploads'</span>), $name);

<span class="text-secondary">// Attacker: /uploads/shell.php</span></code></pre>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-success">✅ Secure: Store in /storage</h6>
                <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">// Files tidak bisa diakses langsung</span>
$file->storeAs(<span class="text-success">'uploads'</span>, $name, <span class="text-success">'private'</span>);

<span class="text-secondary">// Serve via controller dengan auth check</span></code></pre>
                </div>
            </div>
        </div>

        <div class="alert alert-success mt-3 mb-0 py-2">
            <i class="bi bi-lightbulb"></i>
            <strong>Best Practice:</strong> Selalu simpan di <code>storage/app/private</code> dan
            serve via controller dengan authorization check!
        </div>
    </div>
</div>

{{-- Serving Files Securely --}}
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-cloud-download"></i> Serving Files Securely</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// routes/web.php</span>
Route::get(<span class="text-success">'/files/{filename}'</span>, [FileController::<span class="text-primary">class</span>, <span class="text-success">'show'</span>])
    ->middleware(<span class="text-success">'auth'</span>);

<span class="text-secondary">// app/Http/Controllers/FileController.php</span>
<span class="text-primary">public function</span> <span class="text-info">show</span>(<span class="text-primary">string</span> $filename): Response
{
    <span class="text-secondary">// 1. Validate filename (no path traversal)</span>
    <span class="text-primary">if</span> (str_contains($filename, <span class="text-success">'..'</span>) || str_contains($filename, <span class="text-success">'/'</span>)) {
        <span class="text-info">abort</span>(<span class="text-info">403</span>);
    }

    <span class="text-secondary">// 2. Check file exists</span>
    $path = storage_path(<span class="text-success">'app/private/uploads/'</span> . $filename);
    <span class="text-primary">if</span> (!file_exists($path)) {
        <span class="text-info">abort</span>(<span class="text-info">404</span>);
    }

    <span class="text-secondary">// 3. Check authorization (user owns file, etc.)</span>
    $this-><span class="text-info">authorize</span>(<span class="text-success">'view'</span>, $file);

    <span class="text-secondary">// 4. Return with security headers</span>
    <span class="text-primary">return</span> response()->file($path, [
        <span class="text-success">'Content-Disposition'</span> => <span class="text-success">'inline'</span>,
        <span class="text-success">'X-Content-Type-Options'</span> => <span class="text-success">'nosniff'</span>,  <span class="text-secondary">// Prevent MIME sniffing</span>
        <span class="text-success">'Content-Security-Policy'</span> => <span class="text-success">"default-src 'none'"</span>,
    ]);
}</code></pre>
    </div>
</div>
