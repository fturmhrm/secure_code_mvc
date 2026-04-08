{{-- Partial: Error ID Pattern --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-primary">
            <h5><i class="bi bi-bookmark-star"></i> Error ID Pattern - Best Practice</h5>
            <p class="mb-0">
                Teknik memberikan ID unik untuk setiap error, sehingga user bisa report error tanpa kita expose detail teknis.
                Support team bisa cari error di log menggunakan ID tersebut.
            </p>
        </div>
    </div>
</div>

{{-- Visual Concept --}}
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-diagram-2"></i> Konsep Error ID</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            {{-- User Side --}}
            <div class="col-md-5">
                <div class="card bg-light">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-person"></i> Yang User Lihat
                    </div>
                    <div class="card-body text-center">
                        <div class="display-1 text-danger mb-3">500</div>
                        <h5>Server Error</h5>
                        <p class="text-muted">Oops! Something went wrong.</p>
                        <div class="alert alert-secondary py-2">
                            <small>Reference: <code>err-550e8400-e29b-41d4</code></small>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            User bisa report ID ini ke support
                        </small>
                    </div>
                </div>
            </div>

            {{-- Arrow --}}
            <div class="col-md-2 text-center">
                <i class="bi bi-arrow-left-right fs-1 text-primary"></i>
                <p class="small text-muted mt-2">Linked by ID</p>
            </div>

            {{-- Developer Side --}}
            <div class="col-md-5">
                <div class="card bg-dark text-light">
                    <div class="card-header bg-success">
                        <i class="bi bi-terminal"></i> Yang Developer Lihat (Log)
                    </div>
                    <div class="card-body">
<pre class="text-light mb-0 small"><code>[2024-01-15 10:30:45] ERROR: <span class="text-warning">err-550e8400-e29b-41d4</span>
Message: SQLSTATE[23000] Duplicate entry...
SQL: INSERT INTO users (email)...
File: TicketController.php:45
User ID: 123
IP: 192.168.1.100
URL: /api/tickets
Stack Trace: #0 vendor/laravel/...</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Why Use Error ID --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100 border-success">
            <div class="card-header bg-success text-white">
                <i class="bi bi-check-circle"></i> Keuntungan Error ID
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Tracking:</strong> Mudah cari error di log file</li>
                    <li><strong>Correlation:</strong> Link user report dengan detail teknis</li>
                    <li><strong>Security:</strong> Tidak expose info sensitif ke user</li>
                    <li><strong>Support:</strong> User bisa report dengan ID saja</li>
                    <li><strong>Analytics:</strong> Track error patterns</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border-info">
            <div class="card-header bg-info text-white">
                <i class="bi bi-code-slash"></i> Format Error ID
            </div>
            <div class="card-body">
                <p class="small">Beberapa format yang umum digunakan:</p>
                <ul class="small mb-0">
                    <li><code>err-550e8400-e29b-41d4</code> (UUID)</li>
                    <li><code>ERR-20240115-103045-ABC123</code> (Timestamp)</li>
                    <li><code>REF-XXXXXX</code> (Random string)</li>
                    <li><code>error_abc123xyz</code> (Simple random)</li>
                </ul>
                <div class="alert alert-warning py-2 mt-3 small mb-0">
                    <i class="bi bi-lightbulb"></i> UUID recommended karena unique & standard
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Implementation --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-code-slash"></i> Implementasi Error ID Pattern</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code><span class="text-secondary">// app/Exceptions/Handler.php</span>

<span class="text-primary">use</span> Illuminate\Support\Str;
<span class="text-primary">use</span> Illuminate\Support\Facades\Log;

<span class="text-primary">class</span> <span class="text-warning">Handler</span> <span class="text-primary">extends</span> ExceptionHandler
{
    <span class="text-secondary">/**
     * Log error detail dan return unique error ID
     */</span>
    <span class="text-primary">private function</span> <span class="text-info">logErrorWithId</span>(Throwable $e, $request): <span class="text-warning">string</span>
    {
        <span class="text-secondary">// Generate unique ID</span>
        $errorId = <span class="text-success">'err-'</span> . Str::<span class="text-info">uuid</span>()-><span class="text-info">toString</span>();

        <span class="text-secondary">// Log SEMUA detail ke internal log</span>
        Log::<span class="text-info">error</span>(<span class="text-success">"Exception [{$errorId}]"</span>, [
            <span class="text-success">'error_id'</span>   => $errorId,
            <span class="text-success">'message'</span>    => $e-><span class="text-info">getMessage</span>(),
            <span class="text-success">'exception'</span>  => <span class="text-info">get_class</span>($e),
            <span class="text-success">'file'</span>       => $e-><span class="text-info">getFile</span>(),
            <span class="text-success">'line'</span>       => $e-><span class="text-info">getLine</span>(),
            <span class="text-success">'trace'</span>      => $e-><span class="text-info">getTraceAsString</span>(),
            <span class="text-success">'url'</span>        => $request-><span class="text-info">fullUrl</span>(),
            <span class="text-success">'method'</span>     => $request-><span class="text-info">method</span>(),
            <span class="text-success">'user_id'</span>    => auth()-><span class="text-info">id</span>(),
            <span class="text-success">'ip'</span>         => $request-><span class="text-info">ip</span>(),
            <span class="text-success">'user_agent'</span> => $request-><span class="text-info">userAgent</span>(),
        ]);

        <span class="text-primary">return</span> $errorId;
    }

    <span class="text-secondary">/**
     * Return safe error response
     */</span>
    <span class="text-primary">public function</span> <span class="text-info">render</span>($request, Throwable $e)
    {
        <span class="text-secondary">// Development: tampilkan detail</span>
        <span class="text-primary">if</span> (config(<span class="text-success">'app.debug'</span>)) {
            <span class="text-primary">return</span> parent::<span class="text-info">render</span>($request, $e);
        }

        <span class="text-secondary">// Production: log dan return generic</span>
        $errorId = $this-><span class="text-info">logErrorWithId</span>($e, $request);
        $statusCode = $this-><span class="text-info">getStatusCode</span>($e);

        <span class="text-primary">if</span> ($request-><span class="text-info">expectsJson</span>()) {
            <span class="text-primary">return</span> response()-><span class="text-info">json</span>([
                <span class="text-success">'success'</span> => <span class="text-info">false</span>,
                <span class="text-success">'message'</span> => <span class="text-success">'An error occurred'</span>,
                <span class="text-success">'error_id'</span> => $errorId  <span class="text-secondary">// ✅ Kirim ID ke user</span>
            ], $statusCode);
        }

        <span class="text-primary">return</span> response()-><span class="text-info">view</span>(<span class="text-success">"errors.{$statusCode}"</span>, [
            <span class="text-success">'errorId'</span> => $errorId  <span class="text-secondary">// ✅ Pass ke view</span>
        ], $statusCode);
    }
}</code></pre>
    </div>
</div>

{{-- Search Log --}}
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-search"></i> Cara Cari Error di Log</h5>
    </div>
    <div class="card-body">
        <p>Ketika user report error ID, support team bisa cari:</p>
        <div class="bg-dark text-light p-3 rounded">
<pre class="mb-0"><code><span class="text-secondary"># Di server, cari error ID di log file</span>
<span class="text-success">grep</span> <span class="text-warning">"err-550e8400-e29b-41d4"</span> /var/log/laravel.log

<span class="text-secondary"># Atau dengan artisan</span>
<span class="text-success">php artisan</span> log:tail --filter=<span class="text-warning">"err-550e8400-e29b-41d4"</span>

<span class="text-secondary"># Output: semua detail error termasuk stack trace</span></code></pre>
        </div>
    </div>
</div>

{{-- Error Page with ID --}}
<div class="card">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="bi bi-file-earmark-code"></i> Error Page dengan Error ID</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">&lt;!-- resources/views/errors/500.blade.php --&gt;</span>
&lt;div class="error-container text-center"&gt;
    &lt;h1 class="display-1 text-danger"&gt;500&lt;/h1&gt;
    &lt;h2&gt;Server Error&lt;/h2&gt;
    &lt;p class="text-muted"&gt;
        Oops! Something went wrong on our end.
    &lt;/p&gt;

    <span class="text-secondary">&lt;!-- Tampilkan Error ID jika ada --&gt;</span>
    <span class="text-info">@@if</span>(isset($errorId))
        &lt;div class="alert alert-secondary"&gt;
            &lt;small&gt;Reference: &lt;code&gt;@{{ $errorId }}&lt;/code&gt;&lt;/small&gt;
            &lt;br&gt;
            &lt;small class="text-muted"&gt;
                Simpan kode ini jika perlu menghubungi support.
            &lt;/small&gt;
        &lt;/div&gt;
    <span class="text-info">@@endif</span>

    &lt;a href="@{{ url('/') }}" class="btn btn-primary"&gt;
        Go to Homepage
    &lt;/a&gt;
&lt;/div&gt;</code></pre>
    </div>
</div>
