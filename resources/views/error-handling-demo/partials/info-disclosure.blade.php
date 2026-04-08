{{-- Partial: Information Disclosure Demo --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle"></i> Apa Itu Information Disclosure?</h5>
            <p class="mb-0">
                Kebocoran informasi sensitif yang tidak seharusnya terekspos ke pengguna atau attacker.
                Informasi ini bisa berupa: stack trace, database queries, file paths, versi framework, credentials, dll.
            </p>
        </div>
    </div>
</div>

{{-- Debug Mode Comparison --}}
<div class="row">
    {{-- Vulnerable: APP_DEBUG=true --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-danger h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> ❌ VULNERABLE: APP_DEBUG=true
                </h5>
            </div>
            <div class="card-body p-0">
                {{-- Simulated Whoops Error Page --}}
                <div class="bg-dark text-light p-3" style="font-family: monospace; font-size: 0.85rem;">
                    <div class="text-danger fw-bold mb-2">
                        ⚠️ Whoops! There was an error.
                    </div>
                    <div class="border-bottom border-secondary pb-2 mb-2">
                        <span class="text-warning">SQLSTATE[42S02]:</span> Base table or view not found:<br>
                        1146 Table '<span class="text-info">ticketing_db.tickets</span>' doesn't exist
                    </div>
                    <div class="text-secondary small mb-2">
                        in <span class="text-warning">/var/www/html/secure-ticketing/vendor/laravel/framework/src/Illuminate/Database/Connection.php</span>:760
                    </div>
                    <div class="border-top border-secondary pt-2 mt-2">
                        <div class="text-muted mb-1">Stack Trace:</div>
                        <div class="ps-2 text-secondary" style="font-size: 0.75rem;">
                            #0 /var/www/html/secure-ticketing/vendor/laravel/...<br>
                            #1 <span class="text-danger">/var/www/html/secure-ticketing/app/Http/Controllers/TicketController.php:45</span><br>
                            #2 /var/www/html/secure-ticketing/vendor/laravel/...<br>
                            #3 ...
                        </div>
                    </div>
                    <div class="border-top border-secondary pt-2 mt-2">
                        <div class="text-muted mb-1">Environment Variables:</div>
                        <div class="ps-2" style="font-size: 0.75rem;">
                            <span class="text-info">DB_HOST:</span> <span class="text-danger">192.168.1.100</span><br>
                            <span class="text-info">DB_DATABASE:</span> <span class="text-danger">ticketing_db</span><br>
                            <span class="text-info">DB_USERNAME:</span> <span class="text-danger">app_user</span><br>
                            <span class="text-info">APP_KEY:</span> <span class="text-danger">base64:xK9mN2pQr5sT8u...</span><br>
                            <span class="text-info">MAIL_PASSWORD:</span> <span class="text-danger">smtp_p@ssw0rd</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-danger-subtle">
                <small class="text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Bahaya:</strong> Stack trace, DB info, credentials terekspos!
                </small>
            </div>
        </div>
    </div>

    {{-- Secure: APP_DEBUG=false --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-success h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check"></i> ✅ SECURE: APP_DEBUG=false
                </h5>
            </div>
            <div class="card-body p-0">
                {{-- Custom 500 Error Page --}}
                <div class="text-center p-5 bg-light">
                    <div class="display-1 text-muted mb-3">500</div>
                    <h3 class="text-secondary">Server Error</h3>
                    <p class="text-muted">
                        Oops! Something went wrong on our end.<br>
                        Please try again later.
                    </p>
                    <hr class="my-4">
                    <p class="small text-muted mb-0">
                        <i class="bi bi-bookmark"></i> Reference: <code>err-550e8400-e29b-41d4</code>
                    </p>
                    <a href="#" class="btn btn-primary mt-3">
                        <i class="bi bi-house"></i> Go to Homepage
                    </a>
                </div>
            </div>
            <div class="card-footer bg-success-subtle">
                <small class="text-success">
                    <i class="bi bi-shield-check"></i>
                    <strong>Aman:</strong> Generic message + Error ID untuk tracking internal
                </small>
            </div>
        </div>
    </div>
</div>

{{-- What Gets Exposed --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-eye"></i> Informasi yang Terekspos dari Debug Mode</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Jenis Informasi</th>
                    <th>Contoh</th>
                    <th>Risiko bagi Attacker</th>
                </tr>
            </thead>
            <tbody>
                <tr class="table-danger">
                    <td><i class="bi bi-folder2"></i> File Path</td>
                    <td><code>/var/www/html/secure-ticketing/app/...</code></td>
                    <td>Tahu struktur folder, bisa target file sensitive</td>
                </tr>
                <tr class="table-danger">
                    <td><i class="bi bi-database"></i> Database Info</td>
                    <td><code>ticketing_db</code>, IP: <code>192.168.1.100</code></td>
                    <td>Target untuk SQL injection, network pivot</td>
                </tr>
                <tr class="table-danger">
                    <td><i class="bi bi-box"></i> Framework Version</td>
                    <td><code>Laravel Framework 10.48.x</code></td>
                    <td>Cari CVE untuk versi tersebut</td>
                </tr>
                <tr class="table-danger">
                    <td><i class="bi bi-key"></i> Environment Variables</td>
                    <td><code>APP_KEY</code>, <code>MAIL_PASSWORD</code></td>
                    <td>Credentials untuk akses sistem lain</td>
                </tr>
                <tr class="table-danger">
                    <td><i class="bi bi-list-nested"></i> Stack Trace</td>
                    <td>Line numbers, method names</td>
                    <td>Peta logika aplikasi, cari weakness</td>
                </tr>
                <tr class="table-danger">
                    <td><i class="bi bi-code-square"></i> Query Details</td>
                    <td>Table names, column names</td>
                    <td>Struktur database untuk SQL injection</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- .env Configuration --}}
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-danger h-100">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-x-circle"></i> .env - DEVELOPMENT (Local Only)
            </div>
            <div class="card-body bg-dark">
<pre class="text-light mb-0"><code>APP_NAME=SecureTicketing
APP_ENV=<span class="text-warning">local</span>
APP_DEBUG=<span class="text-danger">true</span>     <span class="text-secondary"># ← Debug ON</span>
APP_URL=http://localhost</code></pre>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success h-100">
            <div class="card-header bg-success text-white">
                <i class="bi bi-check-circle"></i> .env - PRODUCTION (Server)
            </div>
            <div class="card-body bg-dark">
<pre class="text-light mb-0"><code>APP_NAME=SecureTicketing
APP_ENV=<span class="text-success">production</span>
APP_DEBUG=<span class="text-success">false</span>    <span class="text-secondary"># ← Debug OFF (WAJIB!)</span>
APP_URL=https://ticketing.company.com</code></pre>
            </div>
        </div>
    </div>
</div>
