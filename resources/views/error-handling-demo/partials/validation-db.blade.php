{{-- Partial: Validation & Database Exception Handling --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="bi bi-database-exclamation"></i> Handle Validation & Database Errors</h5>
            <p class="mb-0">
                Validation errors <strong>boleh</strong> detail karena membantu user memperbaiki input.
                Database errors <strong>harus</strong> disembunyikan karena mengandung informasi sensitif.
            </p>
        </div>
    </div>
</div>

{{-- Visual Flow --}}
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Alur Penanganan Error</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <i class="bi bi-person-fill fs-1 text-primary"></i>
                    <h6 class="mt-2">User Input</h6>
                    <small class="text-muted">Form / API Request</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-warning rounded">
                    <i class="bi bi-funnel-fill fs-1 text-dark"></i>
                    <h6 class="mt-2">Exception Handler</h6>
                    <small class="text-muted">Filter & Process</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-success text-white rounded">
                    <i class="bi bi-shield-check fs-1"></i>
                    <h6 class="mt-2">Safe Response</h6>
                    <small>Generic / Helpful</small>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <i class="bi bi-arrow-right fs-3 text-muted"></i>
        </div>
    </div>
</div>

{{-- Validation Exception --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="bi bi-check-circle"></i> Handle ValidationException
            <span class="badge bg-light text-success ms-2">Boleh Detail</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-lightbulb text-warning"></i> Kenapa Boleh Detail?</h6>
                <ul class="small">
                    <li>Membantu user memperbaiki input</li>
                    <li>Tidak mengandung info internal sistem</li>
                    <li>Meningkatkan UX (User Experience)</li>
                    <li>Laravel sudah handle secara aman</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6><i class="bi bi-chat-square-text text-info"></i> Contoh Response:</h6>
                <div class="bg-dark text-light p-3 rounded small">
<pre class="mb-0">{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email harus valid."],
    "password": ["Minimal 8 karakter."]
  }
}</pre>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// Di Handler.php - register() method</span>

$this-><span class="text-info">renderable</span>(<span class="text-warning">function</span> (ValidationException $e, $request) {
    <span class="text-warning">if</span> ($request-><span class="text-info">expectsJson</span>()) {
        <span class="text-warning">return</span> response()-><span class="text-info">json</span>([
            <span class="text-success">'success'</span> => <span class="text-info">false</span>,
            <span class="text-success">'message'</span> => <span class="text-success">'Validation failed'</span>,
            <span class="text-success">'errors'</span> => $e-><span class="text-info">errors</span>()  <span class="text-secondary">// ✅ Boleh tampilkan</span>
        ], <span class="text-info">422</span>);
    }
});</code></pre>
    </div>
</div>

{{-- Database Exception --}}
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="bi bi-x-circle"></i> Handle QueryException (Database)
            <span class="badge bg-light text-danger ms-2">HARUS Disembunyikan</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 border-end">
                <h6 class="text-danger"><i class="bi bi-x-octagon"></i> ❌ JANGAN Seperti Ini:</h6>
                <div class="bg-danger bg-opacity-10 p-3 rounded small">
<pre class="mb-0 text-danger">{
  "error": "SQLSTATE[23000]: Duplicate entry
  'admin@company.com' for key 'users_email_unique'",
  "query": "INSERT INTO users (email, password)..."
}</pre>
                </div>
                <ul class="small mt-2 text-danger">
                    <li>Expose nama tabel: <code>users</code></li>
                    <li>Expose kolom: <code>email</code></li>
                    <li>Expose constraint: <code>users_email_unique</code></li>
                    <li>Expose SQL query lengkap</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-success"><i class="bi bi-check-circle"></i> ✅ Seperti Ini:</h6>
                <div class="bg-success bg-opacity-10 p-3 rounded small">
<pre class="mb-0 text-success">{
  "success": false,
  "message": "A database error occurred",
  "error_id": "err-550e8400-e29b"
}</pre>
                </div>
                <ul class="small mt-2 text-success">
                    <li>Message generic</li>
                    <li>Error ID untuk tracking</li>
                    <li>Detail HANYA di internal log</li>
                    <li>User tidak tahu struktur DB</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-footer bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// Di Handler.php - register() method</span>

$this-><span class="text-info">renderable</span>(<span class="text-warning">function</span> (QueryException $e, $request) {
    <span class="text-secondary">// 1. Generate unique error ID</span>
    $errorId = <span class="text-success">'err-'</span> . Str::<span class="text-info">uuid</span>();

    <span class="text-secondary">// 2. Log detail INTERNAL saja</span>
    Log::<span class="text-info">error</span>(<span class="text-success">"Database Error {$errorId}"</span>, [
        <span class="text-success">'message'</span> => $e-><span class="text-info">getMessage</span>(),
        <span class="text-success">'sql'</span> => $e-><span class="text-info">getSql</span>(),
        <span class="text-success">'bindings'</span> => $e-><span class="text-info">getBindings</span>(),
    ]);

    <span class="text-secondary">// 3. Return GENERIC ke user</span>
    <span class="text-warning">if</span> ($request-><span class="text-info">expectsJson</span>()) {
        <span class="text-warning">return</span> response()-><span class="text-info">json</span>([
            <span class="text-success">'success'</span> => <span class="text-info">false</span>,
            <span class="text-success">'message'</span> => <span class="text-success">'A database error occurred'</span>,
            <span class="text-success">'error_id'</span> => $errorId
        ], <span class="text-info">500</span>);
    }

    <span class="text-warning">return</span> response()-><span class="text-info">view</span>(<span class="text-success">'errors.500'</span>, [<span class="text-success">'errorId'</span> => $errorId], <span class="text-info">500</span>);
});</code></pre>
    </div>
</div>

{{-- Other Exceptions --}}
<div class="card">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Exception Lain yang Perlu Di-Handle</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Exception</th>
                        <th>Status Code</th>
                        <th>Message ke User</th>
                        <th>Log Detail?</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>ModelNotFoundException</code></td>
                        <td><span class="badge bg-info">404</span></td>
                        <td>"Resource not found"</td>
                        <td><span class="badge bg-secondary">Optional</span></td>
                    </tr>
                    <tr>
                        <td><code>AuthenticationException</code></td>
                        <td><span class="badge bg-warning text-dark">401</span></td>
                        <td>"Unauthenticated"</td>
                        <td><span class="badge bg-secondary">No</span></td>
                    </tr>
                    <tr>
                        <td><code>AuthorizationException</code></td>
                        <td><span class="badge bg-danger">403</span></td>
                        <td>"Forbidden"</td>
                        <td><span class="badge bg-warning text-dark">Yes</span></td>
                    </tr>
                    <tr>
                        <td><code>QueryException</code></td>
                        <td><span class="badge bg-danger">500</span></td>
                        <td>"A database error occurred"</td>
                        <td><span class="badge bg-success">YES!</span></td>
                    </tr>
                    <tr>
                        <td><code>ThrottleRequestsException</code></td>
                        <td><span class="badge bg-warning text-dark">429</span></td>
                        <td>"Too many requests"</td>
                        <td><span class="badge bg-warning text-dark">Yes</span></td>
                    </tr>
                    <tr>
                        <td><code>Exception</code> (Generic)</td>
                        <td><span class="badge bg-danger">500</span></td>
                        <td>"An error occurred"</td>
                        <td><span class="badge bg-success">YES!</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
