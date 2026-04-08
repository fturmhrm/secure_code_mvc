{{-- Partial: Complete Exception Handler --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-success">
            <h5><i class="bi bi-check2-all"></i> Complete Exception Handler - Production Ready</h5>
            <p class="mb-0">
                Berikut adalah contoh lengkap Exception Handler yang aman untuk production.
                Copy dan sesuaikan dengan kebutuhan aplikasi Anda.
            </p>
        </div>
    </div>
</div>

{{-- File Structure --}}
<div class="card mb-4 border-info">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-folder2"></i> File Structure</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0"><code>📁 app/Exceptions/
└── <span class="text-warning">Handler.php</span>  <span class="text-secondary">← File utama exception handler</span>

📁 resources/views/errors/
├── <span class="text-info">401.blade.php</span>
├── <span class="text-info">403.blade.php</span>
├── <span class="text-info">404.blade.php</span>
├── <span class="text-info">419.blade.php</span>
├── <span class="text-info">429.blade.php</span>
├── <span class="text-danger">500.blade.php</span>
└── <span class="text-secondary">503.blade.php</span>

📁 app/Http/Middleware/
└── <span class="text-warning">SecureHeaders.php</span>  <span class="text-secondary">← Hide server info</span></code></pre>
    </div>
</div>

{{-- Part 1: Class Structure --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <span class="badge bg-primary me-2">1/4</span>
            <i class="bi bi-code-slash"></i> Class Structure & Properties
        </h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.8rem;"><code><span class="text-secondary">&lt;?php</span>
<span class="text-secondary">// app/Exceptions/Handler.php</span>

<span class="text-primary">namespace</span> App\Exceptions;

<span class="text-primary">use</span> Illuminate\Foundation\Exceptions\Handler <span class="text-primary">as</span> ExceptionHandler;
<span class="text-primary">use</span> Illuminate\Auth\AuthenticationException;
<span class="text-primary">use</span> Illuminate\Auth\Access\AuthorizationException;
<span class="text-primary">use</span> Illuminate\Database\Eloquent\ModelNotFoundException;
<span class="text-primary">use</span> Illuminate\Validation\ValidationException;
<span class="text-primary">use</span> Illuminate\Database\QueryException;
<span class="text-primary">use</span> Illuminate\Http\Exceptions\ThrottleRequestsException;
<span class="text-primary">use</span> Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
<span class="text-primary">use</span> Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
<span class="text-primary">use</span> Illuminate\Support\Str;
<span class="text-primary">use</span> Illuminate\Support\Facades\Log;
<span class="text-primary">use</span> Throwable;

<span class="text-primary">class</span> <span class="text-warning">Handler</span> <span class="text-primary">extends</span> ExceptionHandler
{
    <span class="text-secondary">/**
     * Exceptions yang TIDAK perlu di-report (normal flow)
     */</span>
    <span class="text-primary">protected</span> $dontReport = [
        AuthenticationException::<span class="text-primary">class</span>,
        AuthorizationException::<span class="text-primary">class</span>,
        ModelNotFoundException::<span class="text-primary">class</span>,
        ValidationException::<span class="text-primary">class</span>,
        NotFoundHttpException::<span class="text-primary">class</span>,
        ThrottleRequestsException::<span class="text-primary">class</span>,
    ];

    <span class="text-secondary">/**
     * Input yang TIDAK boleh di-log (sensitive data)
     */</span>
    <span class="text-primary">protected</span> $dontFlash = [
        <span class="text-success">'password'</span>,
        <span class="text-success">'password_confirmation'</span>,
        <span class="text-success">'current_password'</span>,
        <span class="text-success">'credit_card'</span>,
        <span class="text-success">'cvv'</span>,
        <span class="text-success">'token'</span>,
        <span class="text-success">'secret'</span>,
    ];

    <span class="text-secondary">// ... lanjut ke bagian berikutnya</span>
}</code></pre>
    </div>
</div>

{{-- Part 2: Register Method --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <span class="badge bg-primary me-2">2/4</span>
            <i class="bi bi-plus-circle"></i> register() Method
        </h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.8rem;"><code><span class="text-secondary">/**
 * Register exception handlers
 */</span>
<span class="text-primary">public function</span> <span class="text-info">register</span>(): <span class="text-primary">void</span>
{
    <span class="text-secondary">// 1. Report ke external service (Sentry/Bugsnag)</span>
    $this-><span class="text-info">reportable</span>(<span class="text-primary">function</span> (Throwable $e) {
        <span class="text-primary">if</span> (app()-><span class="text-info">bound</span>(<span class="text-success">'sentry'</span>)) {
            app(<span class="text-success">'sentry'</span>)-><span class="text-info">captureException</span>($e);
        }
    });

    <span class="text-secondary">// 2. Handle Database Errors</span>
    $this-><span class="text-info">renderable</span>(<span class="text-primary">function</span> (QueryException $e, $request) {
        $errorId = $this-><span class="text-info">logErrorWithId</span>($e, $request);

        <span class="text-primary">if</span> ($request-><span class="text-info">expectsJson</span>()) {
            <span class="text-primary">return</span> response()-><span class="text-info">json</span>([
                <span class="text-success">'success'</span> => <span class="text-info">false</span>,
                <span class="text-success">'message'</span> => <span class="text-success">'A database error occurred'</span>,
                <span class="text-success">'error_id'</span> => $errorId
            ], <span class="text-info">500</span>);
        }
        <span class="text-primary">return</span> response()-><span class="text-info">view</span>(<span class="text-success">'errors.500'</span>, [<span class="text-success">'errorId'</span> => $errorId], <span class="text-info">500</span>);
    });

    <span class="text-secondary">// 3. Handle 404 Not Found</span>
    $this-><span class="text-info">renderable</span>(<span class="text-primary">function</span> (ModelNotFoundException $e, $request) {
        <span class="text-primary">if</span> ($request-><span class="text-info">expectsJson</span>()) {
            <span class="text-primary">return</span> response()-><span class="text-info">json</span>([
                <span class="text-success">'success'</span> => <span class="text-info">false</span>,
                <span class="text-success">'message'</span> => <span class="text-success">'Resource not found'</span>
            ], <span class="text-info">404</span>);
        }
    });

    <span class="text-secondary">// 4. Handle Rate Limiting</span>
    $this-><span class="text-info">renderable</span>(<span class="text-primary">function</span> (ThrottleRequestsException $e, $request) {
        <span class="text-primary">if</span> ($request-><span class="text-info">expectsJson</span>()) {
            <span class="text-primary">return</span> response()-><span class="text-info">json</span>([
                <span class="text-success">'success'</span> => <span class="text-info">false</span>,
                <span class="text-success">'message'</span> => <span class="text-success">'Too many requests. Please slow down.'</span>,
                <span class="text-success">'retry_after'</span> => $e-><span class="text-info">getHeaders</span>()[<span class="text-success">'Retry-After'</span>] ?? <span class="text-info">60</span>
            ], <span class="text-info">429</span>);
        }
    });
}</code></pre>
    </div>
</div>

{{-- Part 3: Helper Methods --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <span class="badge bg-primary me-2">3/4</span>
            <i class="bi bi-tools"></i> Helper Methods
        </h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.8rem;"><code><span class="text-secondary">/**
 * Log error dengan unique ID untuk tracking
 */</span>
<span class="text-primary">private function</span> <span class="text-info">logErrorWithId</span>(Throwable $e, $request): <span class="text-warning">string</span>
{
    $errorId = <span class="text-success">'err-'</span> . Str::<span class="text-info">uuid</span>()-><span class="text-info">toString</span>();

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
    ]);

    <span class="text-primary">return</span> $errorId;
}

<span class="text-secondary">/**
 * Get HTTP status code from exception
 */</span>
<span class="text-primary">private function</span> <span class="text-info">getStatusCode</span>(Throwable $e): <span class="text-warning">int</span>
{
    <span class="text-primary">if</span> (method_exists($e, <span class="text-success">'getStatusCode'</span>)) {
        <span class="text-primary">return</span> $e-><span class="text-info">getStatusCode</span>();
    }

    <span class="text-primary">return match</span>(<span class="text-info">true</span>) {
        $e <span class="text-primary">instanceof</span> AuthenticationException => <span class="text-info">401</span>,
        $e <span class="text-primary">instanceof</span> AuthorizationException => <span class="text-info">403</span>,
        $e <span class="text-primary">instanceof</span> ModelNotFoundException => <span class="text-info">404</span>,
        $e <span class="text-primary">instanceof</span> NotFoundHttpException => <span class="text-info">404</span>,
        $e <span class="text-primary">instanceof</span> ValidationException => <span class="text-info">422</span>,
        $e <span class="text-primary">instanceof</span> ThrottleRequestsException => <span class="text-info">429</span>,
        <span class="text-primary">default</span> => <span class="text-info">500</span>
    };
}

<span class="text-secondary">/**
 * Get generic message berdasarkan status code
 */</span>
<span class="text-primary">private function</span> <span class="text-info">getGenericMessage</span>(<span class="text-warning">int</span> $code): <span class="text-warning">string</span>
{
    <span class="text-primary">return match</span>($code) {
        <span class="text-info">400</span> => <span class="text-success">'Bad request'</span>,
        <span class="text-info">401</span> => <span class="text-success">'Please login to continue'</span>,
        <span class="text-info">403</span> => <span class="text-success">'You do not have permission'</span>,
        <span class="text-info">404</span> => <span class="text-success">'Resource not found'</span>,
        <span class="text-info">419</span> => <span class="text-success">'Session expired, please refresh'</span>,
        <span class="text-info">422</span> => <span class="text-success">'Validation failed'</span>,
        <span class="text-info">429</span> => <span class="text-success">'Too many requests'</span>,
        <span class="text-info">500</span> => <span class="text-success">'Server error'</span>,
        <span class="text-info">503</span> => <span class="text-success">'Service unavailable'</span>,
        <span class="text-primary">default</span> => <span class="text-success">'An error occurred'</span>
    };
}</code></pre>
    </div>
</div>

{{-- Part 4: Render Method --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <span class="badge bg-primary me-2">4/4</span>
            <i class="bi bi-eye"></i> render() Method - Final
        </h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.8rem;"><code><span class="text-secondary">/**
 * Render exception response (production-safe)
 */</span>
<span class="text-primary">public function</span> <span class="text-info">render</span>($request, Throwable $e)
{
    <span class="text-secondary">// Development: tampilkan detail untuk debugging</span>
    <span class="text-primary">if</span> (config(<span class="text-success">'app.debug'</span>)) {
        <span class="text-primary">return</span> parent::<span class="text-info">render</span>($request, $e);
    }

    <span class="text-secondary">// Skip untuk validation (sudah di-handle Laravel)</span>
    <span class="text-primary">if</span> ($e <span class="text-primary">instanceof</span> ValidationException) {
        <span class="text-primary">return</span> parent::<span class="text-info">render</span>($request, $e);
    }

    <span class="text-secondary">// Production: log detail, return generic</span>
    $statusCode = $this-><span class="text-info">getStatusCode</span>($e);
    $errorId = <span class="text-info">null</span>;

    <span class="text-secondary">// Hanya log untuk 500 errors (server errors)</span>
    <span class="text-primary">if</span> ($statusCode >= <span class="text-info">500</span>) {
        $errorId = $this-><span class="text-info">logErrorWithId</span>($e, $request);
    }

    <span class="text-secondary">// API Response</span>
    <span class="text-primary">if</span> ($request-><span class="text-info">expectsJson</span>()) {
        $response = [
            <span class="text-success">'success'</span> => <span class="text-info">false</span>,
            <span class="text-success">'message'</span> => $this-><span class="text-info">getGenericMessage</span>($statusCode),
        ];

        <span class="text-primary">if</span> ($errorId) {
            $response[<span class="text-success">'error_id'</span>] = $errorId;
        }

        <span class="text-primary">return</span> response()-><span class="text-info">json</span>($response, $statusCode);
    }

    <span class="text-secondary">// Web Response</span>
    $viewData = $errorId ? [<span class="text-success">'errorId'</span> => $errorId] : [];

    <span class="text-primary">if</span> (view()-><span class="text-info">exists</span>(<span class="text-success">"errors.{$statusCode}"</span>)) {
        <span class="text-primary">return</span> response()-><span class="text-info">view</span>(<span class="text-success">"errors.{$statusCode}"</span>, $viewData, $statusCode);
    }

    <span class="text-secondary">// Fallback ke generic 500 page</span>
    <span class="text-primary">return</span> response()-><span class="text-info">view</span>(<span class="text-success">'errors.500'</span>, $viewData, $statusCode);
}</code></pre>
    </div>
</div>

{{-- Quick Reference --}}
<div class="card border-success">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Reference</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h6><i class="bi bi-1-circle text-primary"></i> Properties</h6>
                <ul class="small mb-0">
                    <li><code>$dontReport</code> - Skip logging</li>
                    <li><code>$dontFlash</code> - Mask input</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6><i class="bi bi-2-circle text-primary"></i> Methods</h6>
                <ul class="small mb-0">
                    <li><code>register()</code> - Setup handlers</li>
                    <li><code>render()</code> - Return response</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6><i class="bi bi-3-circle text-primary"></i> Callbacks</h6>
                <ul class="small mb-0">
                    <li><code>reportable()</code> - Log/Send</li>
                    <li><code>renderable()</code> - Custom response</li>
                </ul>
            </div>
        </div>
    </div>
</div>
