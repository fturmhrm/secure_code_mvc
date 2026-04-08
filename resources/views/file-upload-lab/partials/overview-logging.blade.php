{{-- Partial: Overview Logging --}}

{{-- Why Logging Matters --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-question-circle"></i> Mengapa Logging Penting?</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-danger bg-opacity-10 rounded h-100">
                    <h6><i class="bi bi-shield-x text-danger"></i> Incident Response</h6>
                    <ul class="small mb-0">
                        <li>Deteksi serangan real-time</li>
                        <li>Forensic analysis</li>
                        <li>Memahami timeline breach</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-primary bg-opacity-10 rounded h-100">
                    <h6><i class="bi bi-clipboard-check text-primary"></i> Compliance</h6>
                    <ul class="small mb-0">
                        <li>PCI-DSS, HIPAA, GDPR</li>
                        <li>Audit trail</li>
                        <li>Legal evidence</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-warning bg-opacity-10 rounded h-100">
                    <h6><i class="bi bi-bell text-warning"></i> Monitoring & Alerting</h6>
                    <ul class="small mb-0">
                        <li>Anomaly detection</li>
                        <li>Automated alerts</li>
                        <li>Performance monitoring</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-success bg-opacity-10 rounded h-100">
                    <h6><i class="bi bi-bug text-success"></i> Debugging</h6>
                    <ul class="small mb-0">
                        <li>Production troubleshooting</li>
                        <li>Understand behavior</li>
                        <li>Track errors</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Log Levels --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-layers"></i> Log Levels (RFC 5424)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Level</th>
                        <th>Value</th>
                        <th>Penggunaan</th>
                        <th>Contoh</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <tr class="table-danger">
                        <td><code>emergency</code></td>
                        <td>0</td>
                        <td>System unusable</td>
                        <td><code>Log::emergency('Database server down');</code></td>
                    </tr>
                    <tr class="table-danger">
                        <td><code>alert</code></td>
                        <td>1</td>
                        <td>Immediate action required</td>
                        <td><code>Log::alert('Brute force attack detected');</code></td>
                    </tr>
                    <tr class="table-warning">
                        <td><code>critical</code></td>
                        <td>2</td>
                        <td>Critical conditions</td>
                        <td><code>Log::critical('Payment gateway unreachable');</code></td>
                    </tr>
                    <tr class="table-warning">
                        <td><code>error</code></td>
                        <td>3</td>
                        <td>Error conditions</td>
                        <td><code>Log::error('Failed to process ticket #123');</code></td>
                    </tr>
                    <tr>
                        <td><code>warning</code></td>
                        <td>4</td>
                        <td>Warning conditions</td>
                        <td><code>Log::warning('User rate limit reached');</code></td>
                    </tr>
                    <tr>
                        <td><code>notice</code></td>
                        <td>5</td>
                        <td>Normal but significant</td>
                        <td><code>Log::notice('New admin user created');</code></td>
                    </tr>
                    <tr class="table-info">
                        <td><code>info</code></td>
                        <td>6</td>
                        <td>Informational messages</td>
                        <td><code>Log::info('User logged in', ['user_id' => 1]);</code></td>
                    </tr>
                    <tr class="table-secondary">
                        <td><code>debug</code></td>
                        <td>7</td>
                        <td>Debug-level messages</td>
                        <td><code>Log::debug('Query executed', ['sql' => $query]);</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-info py-2 mb-0">
            <i class="bi bi-lightbulb"></i>
            <strong>Tips:</strong> Production gunakan <code>LOG_LEVEL=warning</code> atau <code>error</code>.
            Development gunakan <code>LOG_LEVEL=debug</code>.
        </div>
    </div>
</div>

{{-- What to Log vs Not Log --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Yang HARUS di-Log</h5>
            </div>
            <div class="card-body small">
                <h6><i class="bi bi-key"></i> Authentication Events</h6>
                <ul>
                    <li>Login success & failure</li>
                    <li>Logout, Password change</li>
                    <li>Account lockout</li>
                </ul>

                <h6><i class="bi bi-shield"></i> Authorization Events</h6>
                <ul>
                    <li>Access denied / 403</li>
                    <li>Privilege escalation attempts</li>
                    <li>Role changes</li>
                </ul>

                <h6><i class="bi bi-exclamation-triangle"></i> Security Events</h6>
                <ul>
                    <li>Input validation failures</li>
                    <li>Rate limit violations</li>
                    <li>File uploads</li>
                </ul>

                <h6><i class="bi bi-database"></i> High-Value Operations</h6>
                <ul class="mb-0">
                    <li>Admin actions</li>
                    <li>Data modifications (CRUD)</li>
                    <li>Financial transactions</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-x-circle"></i> Yang TIDAK BOLEH di-Log!</h5>
            </div>
            <div class="card-body small">
                <div class="alert alert-danger py-2">
                    <strong>⚠️ Log files sering:</strong> disimpan plaintext, diakses banyak orang,
                    dikirim ke third-party, disimpan lama.
                </div>

                <h6><i class="bi bi-key-fill text-danger"></i> Credentials</h6>
                <ul>
                    <li>Passwords (plain atau hashed)</li>
                    <li>API keys / secrets</li>
                    <li>Session tokens</li>
                </ul>

                <h6><i class="bi bi-credit-card text-danger"></i> Financial Data</h6>
                <ul>
                    <li>Credit card numbers</li>
                    <li>CVV codes</li>
                    <li>Bank account info</li>
                </ul>

                <h6><i class="bi bi-person-badge text-danger"></i> Personal Data (PII)</h6>
                <ul class="mb-0">
                    <li>Social Security Numbers</li>
                    <li>Medical records</li>
                    <li>Biometric data</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Structured Logging --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-braces"></i> Structured Logging</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-danger">❌ BAD - Unstructured Log</h6>
                <div class="bg-dark text-light p-3 rounded small">
<pre class="mb-0"><code>Log::info("User 1 logged in from 192.168.1.1 at 2024-01-15 10:30:00");

<span class="text-secondary">// Sulit di-parse dan search</span></code></pre>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-success">✅ GOOD - Structured dengan Context</h6>
                <div class="bg-dark text-light p-3 rounded small">
<pre class="mb-0"><code>Log::info('User logged in', [
    'user_id' => $user->id,
    'email' => $user->email,
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);

<span class="text-secondary">// Mudah di-search: "user_id":1</span></code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Laravel Logging Config --}}
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-gear"></i> Laravel Logging Configuration</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// config/logging.php</span>

<span class="text-primary">return</span> [
    <span class="text-success">'default'</span> => env(<span class="text-success">'LOG_CHANNEL'</span>, <span class="text-success">'stack'</span>),

    <span class="text-success">'channels'</span> => [
        <span class="text-secondary">// Stack: combine multiple channels</span>
        <span class="text-success">'stack'</span> => [
            <span class="text-success">'driver'</span> => <span class="text-success">'stack'</span>,
            <span class="text-success">'channels'</span> => [<span class="text-success">'daily'</span>, <span class="text-success">'security'</span>],
        ],

        <span class="text-secondary">// Daily rotation</span>
        <span class="text-success">'daily'</span> => [
            <span class="text-success">'driver'</span> => <span class="text-success">'daily'</span>,
            <span class="text-success">'path'</span> => storage_path(<span class="text-success">'logs/laravel.log'</span>),
            <span class="text-success">'level'</span> => env(<span class="text-success">'LOG_LEVEL'</span>, <span class="text-success">'debug'</span>),
            <span class="text-success">'days'</span> => <span class="text-info">14</span>,  <span class="text-secondary">// Keep 14 days</span>
        ],

        <span class="text-secondary">// Security events channel</span>
        <span class="text-success">'security'</span> => [
            <span class="text-success">'driver'</span> => <span class="text-success">'daily'</span>,
            <span class="text-success">'path'</span> => storage_path(<span class="text-success">'logs/security.log'</span>),
            <span class="text-success">'level'</span> => <span class="text-success">'info'</span>,
            <span class="text-success">'days'</span> => <span class="text-info">90</span>,  <span class="text-secondary">// Longer retention</span>
        ],
    ],
];

<span class="text-secondary">// Penggunaan:</span>
Log::channel(<span class="text-success">'security'</span>)->warning(<span class="text-success">'Failed login attempt'</span>, [
    <span class="text-success">'email'</span> => $email,
    <span class="text-success">'ip'</span> => request()->ip(),
]);</code></pre>
    </div>
</div>

{{-- Sensitive Data Masking --}}
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-eye-slash"></i> Sensitive Data Masking</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// app/Logging/SensitiveDataProcessor.php</span>

<span class="text-primary">class</span> <span class="text-warning">SensitiveDataProcessor</span>
{
    <span class="text-primary">private array</span> $sensitiveFields = [
        <span class="text-success">'password'</span>, <span class="text-success">'password_confirmation'</span>, <span class="text-success">'current_password'</span>,
        <span class="text-success">'credit_card'</span>, <span class="text-success">'cvv'</span>, <span class="text-success">'card_number'</span>,
        <span class="text-success">'api_key'</span>, <span class="text-success">'secret'</span>, <span class="text-success">'token'</span>,
        <span class="text-success">'ssn'</span>, <span class="text-success">'social_security'</span>,
    ];

    <span class="text-primary">public function</span> <span class="text-info">__invoke</span>(LogRecord $record): LogRecord
    {
        $record->context = $this-><span class="text-info">maskSensitiveData</span>($record->context);
        <span class="text-primary">return</span> $record;
    }

    <span class="text-primary">private function</span> <span class="text-info">maskSensitiveData</span>(<span class="text-primary">array</span> $data): <span class="text-primary">array</span>
    {
        <span class="text-primary">foreach</span> ($data <span class="text-primary">as</span> $key => $value) {
            <span class="text-primary">if</span> (<span class="text-info">in_array</span>(strtolower($key), $this->sensitiveFields)) {
                $data[$key] = <span class="text-success">'[REDACTED]'</span>;
            }
        }
        <span class="text-primary">return</span> $data;
    }
}

<span class="text-secondary">// Result:</span>
<span class="text-secondary">// Input: ['email' => 'user@test.com', 'password' => 'secret123']</span>
<span class="text-secondary">// Output: ['email' => 'user@test.com', 'password' => '[REDACTED]']</span></code></pre>
    </div>
</div>

{{-- Auth Event Logging --}}
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-person-check"></i> Log Authentication Events</h5>
    </div>
    <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// app/Listeners/LogAuthenticationEvents.php</span>

<span class="text-primary">class</span> <span class="text-warning">LogAuthenticationEvents</span>
{
    <span class="text-primary">public function</span> <span class="text-info">handleLogin</span>(Login $event)
    {
        Log::channel(<span class="text-success">'security'</span>)->info(<span class="text-success">'User logged in'</span>, [
            <span class="text-success">'user_id'</span> => $event->user->id,
            <span class="text-success">'email'</span> => $event->user->email,
            <span class="text-success">'ip'</span> => request()->ip(),
            <span class="text-success">'user_agent'</span> => request()->userAgent(),
        ]);
    }

    <span class="text-primary">public function</span> <span class="text-info">handleFailed</span>(Failed $event)
    {
        Log::channel(<span class="text-success">'security'</span>)->warning(<span class="text-success">'Failed login attempt'</span>, [
            <span class="text-success">'email'</span> => $event->credentials[<span class="text-success">'email'</span>] ?? <span class="text-success">'unknown'</span>,
            <span class="text-success">'ip'</span> => request()->ip(),
            <span class="text-success">'user_agent'</span> => request()->userAgent(),
        ]);
    }

    <span class="text-primary">public function</span> <span class="text-info">handleLockout</span>(Lockout $event)
    {
        Log::channel(<span class="text-success">'security'</span>)->alert(<span class="text-success">'User account locked'</span>, [
            <span class="text-success">'email'</span> => $event->request->input(<span class="text-success">'email'</span>),
            <span class="text-success">'ip'</span> => $event->request->ip(),
            <span class="text-success">'reason'</span> => <span class="text-success">'Too many failed attempts'</span>,
        ]);
    }
}</code></pre>
    </div>
</div>
