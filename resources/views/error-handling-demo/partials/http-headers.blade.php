{{-- Partial: HTTP Headers Demo --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="bi bi-hdd-network"></i> HTTP Headers & Server Information</h5>
            <p class="mb-0">
                HTTP response headers dapat mengekspos informasi tentang server, framework, dan versi software.
                Attacker dapat menggunakan informasi ini untuk mencari vulnerability yang sudah diketahui (CVE).
            </p>
        </div>
    </div>
</div>

{{-- Headers Comparison --}}
<div class="row">
    {{-- Vulnerable Headers --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-danger h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> ❌ VULNERABLE Headers
                </h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="bi bi-terminal"></i> <code>curl -I https://vulnerable-app.com</code>
                </p>
                <div class="bg-dark rounded p-3">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code>HTTP/1.1 200 OK
Date: Mon, 10 Mar 2026 08:00:00 GMT
<span class="text-danger">Server: Apache/2.4.54 (Ubuntu)</span>
<span class="text-danger">X-Powered-By: PHP/8.2.0</span>
<span class="text-danger">X-Laravel-Debug: true</span>
Content-Type: text/html; charset=UTF-8
Set-Cookie: XSRF-TOKEN=...; Path=/
Set-Cookie: laravel_session=...; Path=/</code></pre>
                </div>
                <div class="mt-3">
                    <h6 class="text-danger">Informasi yang terekspos:</h6>
                    <ul class="small mb-0">
                        <li><code>Server: Apache/2.4.54 (Ubuntu)</code> → Versi Apache & OS</li>
                        <li><code>X-Powered-By: PHP/8.2.0</code> → Versi PHP</li>
                        <li><code>X-Laravel-Debug: true</code> → Framework & debug mode</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Secure Headers --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-success h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check"></i> ✅ SECURE Headers
                </h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="bi bi-terminal"></i> <code>curl -I https://secure-app.com</code>
                </p>
                <div class="bg-dark rounded p-3">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code>HTTP/1.1 200 OK
Date: Mon, 10 Mar 2026 08:00:00 GMT
<span class="text-success">Server: nginx</span>
Content-Type: text/html; charset=UTF-8
<span class="text-info">X-Content-Type-Options: nosniff</span>
<span class="text-info">X-Frame-Options: SAMEORIGIN</span>
<span class="text-info">X-XSS-Protection: 1; mode=block</span>
<span class="text-info">Strict-Transport-Security: max-age=31536000</span>
Set-Cookie: session=...; Path=/; HttpOnly; Secure</code></pre>
                </div>
                <div class="mt-3">
                    <h6 class="text-success">Perbaikan:</h6>
                    <ul class="small mb-0">
                        <li>Tidak ada X-Powered-By</li>
                        <li>Server version disembunyikan</li>
                        <li>Security headers ditambahkan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Middleware Code --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-code-slash"></i> SecureHeaders Middleware</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0"><code><span class="text-secondary">&lt;?php</span>
<span class="text-secondary">// app/Http/Middleware/SecureHeaders.php</span>

<span class="text-primary">namespace</span> App\Http\Middleware;

<span class="text-primary">use</span> Closure;

<span class="text-primary">class</span> <span class="text-warning">SecureHeaders</span>
{
    <span class="text-primary">public function</span> <span class="text-info">handle</span>($request, Closure $next)
    {
        $response = $next($request);

        <span class="text-secondary">// Remove server info headers</span>
        $response->headers->remove(<span class="text-success">'X-Powered-By'</span>);
        $response->headers->remove(<span class="text-success">'Server'</span>);

        <span class="text-secondary">// Add security headers</span>
        $response->headers->set(<span class="text-success">'X-Content-Type-Options'</span>, <span class="text-success">'nosniff'</span>);
        $response->headers->set(<span class="text-success">'X-Frame-Options'</span>, <span class="text-success">'SAMEORIGIN'</span>);
        $response->headers->set(<span class="text-success">'X-XSS-Protection'</span>, <span class="text-success">'1; mode=block'</span>);
        $response->headers->set(<span class="text-success">'Referrer-Policy'</span>, <span class="text-success">'strict-origin-when-cross-origin'</span>);

        <span class="text-primary">return</span> $response;
    }
}</code></pre>
    </div>
</div>

{{-- PHP & Server Configuration --}}
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-filetype-php"></i> php.ini (Production)
            </div>
            <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code><span class="text-secondary">; Disable error display</span>
display_errors = <span class="text-success">Off</span>
display_startup_errors = <span class="text-success">Off</span>

<span class="text-secondary">; Log errors to file</span>
log_errors = <span class="text-success">On</span>
error_log = /var/log/php/error.log

<span class="text-secondary">; Hide PHP version</span>
expose_php = <span class="text-success">Off</span>

<span class="text-secondary">; Disable dangerous functions</span>
disable_functions = exec,passthru,
    shell_exec,system,proc_open</code></pre>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-server"></i> Apache/Nginx Config
            </div>
            <div class="card-body bg-dark">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code><span class="text-secondary"># Apache - httpd.conf</span>
ServerTokens <span class="text-success">Prod</span>
ServerSignature <span class="text-success">Off</span>

<span class="text-secondary"># Nginx - nginx.conf</span>
server_tokens <span class="text-success">off</span>;

<span class="text-secondary"># Block sensitive files</span>
location ~ /\.(?!well-known).* {
    deny all;
}

location ~* \.(env|git|log)$ {
    deny all;
}</code></pre>
            </div>
        </div>
    </div>
</div>

{{-- Block Sensitive Files --}}
<div class="card mt-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-file-earmark-lock"></i> Block Access ke File Sensitif</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-danger">File yang HARUS diblock:</h6>
                <ul class="small">
                    <li><code>.env</code> - Environment variables & credentials</li>
                    <li><code>.git/</code> - Git repository (source code)</li>
                    <li><code>composer.json</code> - Dependencies & versions</li>
                    <li><code>phpinfo.php</code> - PHP configuration</li>
                    <li><code>storage/logs/</code> - Application logs</li>
                    <li><code>.htaccess</code> - Server configuration</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-success">Test dengan curl:</h6>
                <div class="bg-dark rounded p-2">
<pre class="text-light mb-0 small"><code><span class="text-secondary"># Semua harus return 403 atau 404</span>
curl https://yourapp.com/.env
curl https://yourapp.com/.git/config
curl https://yourapp.com/composer.json
curl https://yourapp.com/phpinfo.php</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
