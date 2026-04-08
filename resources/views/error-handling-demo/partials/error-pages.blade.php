{{-- Partial: Custom Error Pages Demo --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="bi bi-file-earmark-x"></i> Custom Error Pages</h5>
            <p class="mb-0">
                Laravel otomatis mencari view di <code>resources/views/errors/{status}.blade.php</code>.
                Custom error pages memberikan user experience yang lebih baik dan tidak mengekspos informasi teknis.
            </p>
        </div>
    </div>
</div>

{{-- File Structure --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-folder2"></i> Struktur File Error Pages
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0"><code>📁 resources/views/errors/
├── <span class="text-info">401.blade.php</span>    <span class="text-secondary">← Unauthenticated</span>
├── <span class="text-warning">403.blade.php</span>    <span class="text-secondary">← Forbidden</span>
├── <span class="text-info">404.blade.php</span>    <span class="text-secondary">← Not Found</span>
├── <span class="text-warning">419.blade.php</span>    <span class="text-secondary">← Page Expired (CSRF)</span>
├── <span class="text-warning">429.blade.php</span>    <span class="text-secondary">← Too Many Requests</span>
├── <span class="text-danger">500.blade.php</span>    <span class="text-secondary">← Server Error</span>
└── <span class="text-secondary">503.blade.php</span>    <span class="text-secondary">← Service Unavailable</span></code></pre>
    </div>
</div>

{{-- Error Page Previews --}}
<div class="row">
    {{-- 404 Page --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <span class="badge bg-light text-dark">404</span> Not Found
            </div>
            <div class="card-body text-center p-4 bg-light">
                <div class="display-4 text-info mb-2">404</div>
                <h5 class="text-secondary">Page Not Found</h5>
                <p class="small text-muted">
                    The page you are looking for might have been removed or is temporarily unavailable.
                </p>
                <a href="#" class="btn btn-sm btn-info disabled">Go to Homepage</a>
            </div>
        </div>
    </div>

    {{-- 403 Page --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                <span class="badge bg-dark">403</span> Forbidden
            </div>
            <div class="card-body text-center p-4 bg-light">
                <div class="display-4 text-warning mb-2">403</div>
                <h5 class="text-secondary">Access Denied</h5>
                <p class="small text-muted">
                    You don't have permission to access this resource.
                </p>
                <a href="#" class="btn btn-sm btn-warning disabled">Go Back</a>
            </div>
        </div>
    </div>

    {{-- 500 Page --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <span class="badge bg-light text-dark">500</span> Server Error
            </div>
            <div class="card-body text-center p-4 bg-light">
                <div class="display-4 text-danger mb-2">500</div>
                <h5 class="text-secondary">Server Error</h5>
                <p class="small text-muted">
                    Oops! Something went wrong.<br>
                    <code class="small">Ref: err-550e8400</code>
                </p>
                <a href="#" class="btn btn-sm btn-danger disabled">Try Again</a>
            </div>
        </div>
    </div>

    {{-- 419 Page --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <span class="badge bg-light text-dark">419</span> Page Expired
            </div>
            <div class="card-body text-center p-4 bg-light">
                <div class="display-4 text-secondary mb-2">419</div>
                <h5 class="text-secondary">Session Expired</h5>
                <p class="small text-muted">
                    Your session has expired. Please refresh and try again.
                </p>
                <a href="#" class="btn btn-sm btn-secondary disabled">Refresh Page</a>
            </div>
        </div>
    </div>

    {{-- 429 Page --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                <span class="badge bg-dark">429</span> Too Many Requests
            </div>
            <div class="card-body text-center p-4 bg-light">
                <div class="display-4 text-warning mb-2">429</div>
                <h5 class="text-secondary">Slow Down!</h5>
                <p class="small text-muted">
                    Too many requests. Please wait before trying again.
                </p>
                <a href="#" class="btn btn-sm btn-warning disabled">Wait 60s</a>
            </div>
        </div>
    </div>

    {{-- 503 Page --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-dark text-white">
                <span class="badge bg-light text-dark">503</span> Maintenance
            </div>
            <div class="card-body text-center p-4 bg-light">
                <div class="display-4 text-dark mb-2">503</div>
                <h5 class="text-secondary">Under Maintenance</h5>
                <p class="small text-muted">
                    We're performing scheduled maintenance. Be back soon!
                </p>
                <a href="#" class="btn btn-sm btn-dark disabled">Check Status</a>
            </div>
        </div>
    </div>
</div>

{{-- Example Code --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-code-slash"></i> Contoh: 500.blade.php</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code><span class="text-secondary">&lt;!-- resources/views/errors/500.blade.php --&gt;</span>
<span class="text-info">@@extends</span>(<span class="text-success">'layouts.error'</span>)

<span class="text-info">@@section</span>(<span class="text-success">'title'</span>, <span class="text-success">'Server Error'</span>)

<span class="text-info">@@section</span>(<span class="text-success">'content'</span>)
&lt;div class="error-container text-center"&gt;
    &lt;h1 class="display-1 text-danger"&gt;500&lt;/h1&gt;
    &lt;h2&gt;Server Error&lt;/h2&gt;
    &lt;p class="text-muted"&gt;
        Oops! Something went wrong on our end.&lt;br&gt;
        Please try again later.
    &lt;/p&gt;

    <span class="text-secondary">&lt;!-- Error ID untuk tracking (TANPA detail teknis) --&gt;</span>
    <span class="text-info">@@if</span>(isset($errorId))
        &lt;p class="small text-muted"&gt;
            Reference: &lt;code&gt;@{{ $errorId }}&lt;/code&gt;
        &lt;/p&gt;
    <span class="text-info">@@endif</span>

    &lt;a href="@{{ url('/') }}" class="btn btn-primary mt-3"&gt;
        Go to Homepage
    &lt;/a&gt;
&lt;/div&gt;
<span class="text-info">@@endsection</span></code></pre>
    </div>
</div>

{{-- Important Notes --}}
<div class="alert alert-success mt-4">
    <h6><i class="bi bi-lightbulb"></i> Tips untuk Custom Error Pages:</h6>
    <ul class="mb-0 small">
        <li><strong>User-friendly:</strong> Gunakan bahasa yang mudah dipahami, bukan error teknis</li>
        <li><strong>Error ID:</strong> Sertakan reference ID untuk tracking tanpa expose detail</li>
        <li><strong>Navigation:</strong> Berikan link untuk kembali ke homepage atau halaman sebelumnya</li>
        <li><strong>Branding:</strong> Gunakan design yang konsisten dengan aplikasi Anda</li>
        <li><strong>No technical details:</strong> JANGAN tampilkan stack trace, query, atau file path</li>
    </ul>
</div>
