{{-- Partial: Exception Handler Introduction --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="bi bi-code-slash"></i> Laravel Exception Handler</h5>
            <p class="mb-0">
                File <code>app/Exceptions/Handler.php</code> adalah pusat penanganan semua exception di Laravel.
                Di sini kita bisa mengatur bagaimana error di-log dan di-render ke user.
            </p>
        </div>
    </div>
</div>

{{-- Visual Concept --}}
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Bagaimana Exception Handler Bekerja?</h5>
    </div>
    <div class="card-body">
        <div class="row text-center align-items-center">
            <div class="col-md-2">
                <div class="p-3 bg-danger text-white rounded">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                    <p class="mb-0 small mt-2">Exception Terjadi</p>
                </div>
            </div>
            <div class="col-md-1">
                <i class="bi bi-arrow-right fs-3 text-muted"></i>
            </div>
            <div class="col-md-2">
                <div class="p-3 bg-warning rounded">
                    <i class="bi bi-funnel fs-1"></i>
                    <p class="mb-0 small mt-2">Handler.php</p>
                </div>
            </div>
            <div class="col-md-1">
                <i class="bi bi-arrow-right fs-3 text-muted"></i>
            </div>
            <div class="col-md-3">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2 bg-info text-white rounded small">
                            <i class="bi bi-journal-text"></i> Log
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-success text-white rounded small">
                            <i class="bi bi-display"></i> Response
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <i class="bi bi-arrow-right fs-3 text-muted"></i>
            </div>
            <div class="col-md-2">
                <div class="p-3 bg-light rounded">
                    <i class="bi bi-person fs-1 text-primary"></i>
                    <p class="mb-0 small mt-2">User</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Key Concepts --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100 border-info">
            <div class="card-header bg-info text-white">
                <i class="bi bi-key"></i> Konsep Penting
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>$dontReport</strong> - Exception yang tidak perlu di-log</li>
                    <li><strong>$dontFlash</strong> - Input yang di-mask (password, dll)</li>
                    <li><strong>register()</strong> - Setup custom handlers</li>
                    <li><strong>render()</strong> - Tentukan response ke user</li>
                    <li><strong>reportable()</strong> - Kirim ke logging service</li>
                    <li><strong>renderable()</strong> - Custom response per exception</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border-warning">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-lightbulb"></i> Prinsip Utama
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li><strong>Log detail internal</strong> - Untuk debugging</li>
                    <li><strong>Return generic</strong> - Ke user</li>
                    <li><strong>Error ID</strong> - Link user ke log</li>
                    <li><strong>Different per env</strong> - Dev vs Prod</li>
                </ol>
                <div class="alert alert-danger py-2 mt-3 mb-0 small">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>JANGAN</strong> tampilkan stack trace, SQL, atau file path ke user!
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Handler Structure Overview --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-file-earmark-code"></i> Struktur Dasar Handler.php</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code><span class="text-secondary">&lt;?php</span>
<span class="text-secondary">// app/Exceptions/Handler.php</span>

<span class="text-primary">namespace</span> App\Exceptions;

<span class="text-primary">use</span> Illuminate\Foundation\Exceptions\Handler <span class="text-primary">as</span> ExceptionHandler;
<span class="text-primary">use</span> Throwable;

<span class="text-primary">class</span> <span class="text-warning">Handler</span> <span class="text-primary">extends</span> ExceptionHandler
{
    <span class="text-secondary">/**
     * Exceptions yang TIDAK perlu di-report ke log
     * (bukan error sebenarnya, hanya flow normal)
     */</span>
    <span class="text-primary">protected</span> $dontReport = [
        AuthenticationException::<span class="text-primary">class</span>,
        AuthorizationException::<span class="text-primary">class</span>,
        ModelNotFoundException::<span class="text-primary">class</span>,
        ValidationException::<span class="text-primary">class</span>,
    ];

    <span class="text-secondary">/**
     * Input fields yang di-mask saat logging
     * (jangan log password!)
     */</span>
    <span class="text-primary">protected</span> $dontFlash = [
        <span class="text-success">'password'</span>,
        <span class="text-success">'password_confirmation'</span>,
        <span class="text-success">'current_password'</span>,
    ];

    <span class="text-secondary">/**
     * Register exception handlers
     */</span>
    <span class="text-primary">public function</span> <span class="text-info">register</span>(): <span class="text-primary">void</span>
    {
        <span class="text-secondary">// Setup custom handlers di sini</span>
        <span class="text-secondary">// Lihat tab "Validation & DB" untuk contoh</span>
    }

    <span class="text-secondary">/**
     * Render exception ke response
     */</span>
    <span class="text-primary">public function</span> <span class="text-info">render</span>($request, Throwable $e)
    {
        <span class="text-secondary">// Custom rendering di sini</span>
        <span class="text-secondary">// Lihat tab "Complete" untuk contoh lengkap</span>
        <span class="text-primary">return</span> parent::<span class="text-info">render</span>($request, $e);
    }
}</code></pre>
    </div>
</div>

{{-- Production vs Development --}}
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-warning h-100">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-laptop"></i> Development (APP_DEBUG=true)
            </div>
            <div class="card-body">
                <p class="small text-muted">Tampilkan detail untuk membantu debugging:</p>
                <ul class="small mb-0">
                    <li>✅ Whoops error page</li>
                    <li>✅ Stack trace lengkap</li>
                    <li>✅ Environment variables</li>
                    <li>✅ Query log</li>
                    <li>✅ File path & line numbers</li>
                </ul>
                <div class="bg-dark text-light p-2 rounded mt-3 small">
                    <code>return parent::render($request, $e);</code>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card border-success h-100">
            <div class="card-header bg-success text-white">
                <i class="bi bi-server"></i> Production (APP_DEBUG=false)
            </div>
            <div class="card-body">
                <p class="small text-muted">Sembunyikan detail dari user:</p>
                <ul class="small mb-0">
                    <li>✅ Custom error page</li>
                    <li>✅ Generic message</li>
                    <li>✅ Error ID untuk tracking</li>
                    <li>❌ NO stack trace</li>
                    <li>❌ NO file paths</li>
                </ul>
                <div class="bg-dark text-light p-2 rounded mt-3 small">
                    <code>return response()->view('errors.500');</code>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Navigation hint --}}
<div class="alert alert-success">
    <i class="bi bi-arrow-right-circle"></i>
    <strong>Selanjutnya:</strong> Pelajari cara handle Validation & Database exceptions di tab
    <a href="#" onclick="showTab('validation-db-tab')" class="alert-link">"Validation & DB"</a>,
    atau lihat kode lengkap di tab <a href="#" onclick="showTab('complete-handler-tab')" class="alert-link">"Complete"</a>.
</div>
