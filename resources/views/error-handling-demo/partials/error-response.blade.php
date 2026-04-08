{{-- Partial: Error Response Demo --}}

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="bi bi-braces"></i> API Error Response</h5>
            <p class="mb-0">
                Saat terjadi error di API, response yang dikembalikan harus generic dan tidak mengekspos detail internal.
                Detail error hanya di-log secara internal untuk debugging.
            </p>
        </div>
    </div>
</div>

{{-- API Response Comparison --}}
<div class="row">
    {{-- Vulnerable Response --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-danger h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> ❌ VULNERABLE Response
                </h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="bi bi-arrow-right"></i> <code>GET /api/tickets/999</code>
                </p>
                <div class="bg-dark rounded p-3">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code>{
    <span class="text-danger">"error"</span>: true,
    <span class="text-danger">"message"</span>: <span class="text-warning">"SQLSTATE[42S02]: Base table
        'db.tickets' doesn't exist"</span>,
    <span class="text-danger">"file"</span>: <span class="text-warning">"/var/www/html/app/Http/
        Controllers/TicketController.php"</span>,
    <span class="text-danger">"line"</span>: <span class="text-info">45</span>,
    <span class="text-danger">"trace"</span>: <span class="text-warning">"#0 /var/www/html/vendor/..."</span>,
    <span class="text-danger">"query"</span>: [
        <span class="text-warning">"SELECT * FROM tickets WHERE id = 999"</span>
    ]
}</code></pre>
                </div>

                <h6 class="mt-4 text-danger">Controller Code:</h6>
                <div class="bg-dark rounded p-3">
<pre class="text-light mb-0" style="font-size: 0.8rem;"><code><span class="text-secondary">// ❌ JANGAN DITIRU!</span>
<span class="text-primary">public function</span> show($id)
{
    <span class="text-primary">try</span> {
        $ticket = Ticket::findOrFail($id);
        <span class="text-primary">return</span> response()->json($ticket);
    } <span class="text-primary">catch</span> (\Exception $e) {
        <span class="text-primary">return</span> response()->json([
            <span class="text-warning">'error'</span> => <span class="text-info">true</span>,
            <span class="text-danger">'message'</span> => $e->getMessage(), <span class="text-secondary">// ❌ Expose!</span>
            <span class="text-danger">'file'</span> => $e->getFile(),       <span class="text-secondary">// ❌ Expose!</span>
            <span class="text-danger">'line'</span> => $e->getLine(),       <span class="text-secondary">// ❌ Expose!</span>
            <span class="text-danger">'trace'</span> => $e->getTraceAsString() <span class="text-secondary">// ❌ Expose!</span>
        ], <span class="text-info">500</span>);
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    {{-- Secure Response --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-success h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check"></i> ✅ SECURE Response
                </h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="bi bi-arrow-right"></i> <code>GET /api/tickets/999</code>
                </p>
                <div class="bg-dark rounded p-3">
<pre class="text-light mb-0" style="font-size: 0.85rem;"><code>{
    <span class="text-success">"success"</span>: <span class="text-info">false</span>,
    <span class="text-success">"message"</span>: <span class="text-warning">"Resource not found"</span>,
    <span class="text-success">"error_id"</span>: <span class="text-warning">"err-550e8400-e29b-41d4"</span>
}</code></pre>
                </div>

                <h6 class="mt-4 text-success">Controller Code:</h6>
                <div class="bg-dark rounded p-3">
<pre class="text-light mb-0" style="font-size: 0.8rem;"><code><span class="text-secondary">// ✅ SECURE - Generic message</span>
<span class="text-primary">public function</span> show($id)
{
    <span class="text-primary">try</span> {
        $ticket = Ticket::findOrFail($id);
        <span class="text-primary">return</span> response()->json([
            <span class="text-warning">'success'</span> => <span class="text-info">true</span>,
            <span class="text-warning">'data'</span> => $ticket
        ]);

    } <span class="text-primary">catch</span> (ModelNotFoundException $e) {
        <span class="text-secondary">// Generic 404</span>
        <span class="text-primary">return</span> response()->json([
            <span class="text-warning">'success'</span> => <span class="text-info">false</span>,
            <span class="text-warning">'message'</span> => <span class="text-success">'Resource not found'</span>
        ], <span class="text-info">404</span>);

    } <span class="text-primary">catch</span> (\Exception $e) {
        <span class="text-secondary">// Log internally, return generic</span>
        $errorId = Str::uuid();
        Log::error(<span class="text-warning">"Error {$errorId}"</span>, [
            <span class="text-warning">'exception'</span> => $e->getMessage(),
            <span class="text-warning">'trace'</span> => $e->getTraceAsString()
        ]);

        <span class="text-primary">return</span> response()->json([
            <span class="text-warning">'success'</span> => <span class="text-info">false</span>,
            <span class="text-warning">'message'</span> => <span class="text-success">'An error occurred'</span>,
            <span class="text-warning">'error_id'</span> => $errorId
        ], <span class="text-info">500</span>);
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Error Types --}}
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Generic Messages untuk Setiap HTTP Status Code</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th width="100">Code</th>
                    <th>Generic Message (ke User)</th>
                    <th>Internal Log (Detail)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge bg-warning text-dark">400</span></td>
                    <td>"Bad request"</td>
                    <td>Detail validation error, input yang salah</td>
                </tr>
                <tr>
                    <td><span class="badge bg-secondary">401</span></td>
                    <td>"Unauthenticated"</td>
                    <td>Token expired, invalid credentials</td>
                </tr>
                <tr>
                    <td><span class="badge bg-danger">403</span></td>
                    <td>"Forbidden"</td>
                    <td>User ID, resource yang diakses, role</td>
                </tr>
                <tr>
                    <td><span class="badge bg-info">404</span></td>
                    <td>"Resource not found"</td>
                    <td>Actual resource ID, table name</td>
                </tr>
                <tr>
                    <td><span class="badge bg-warning text-dark">422</span></td>
                    <td>"Validation failed" + field errors</td>
                    <td>Input data (excluding passwords)</td>
                </tr>
                <tr>
                    <td><span class="badge bg-danger">429</span></td>
                    <td>"Too many requests"</td>
                    <td>IP address, rate limit details</td>
                </tr>
                <tr>
                    <td><span class="badge bg-dark">500</span></td>
                    <td>"Server error" + Error ID</td>
                    <td>Full stack trace, query, exception</td>
                </tr>
                <tr>
                    <td><span class="badge bg-secondary">503</span></td>
                    <td>"Service unavailable"</td>
                    <td>Maintenance details, ETA</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Note about Validation --}}
<div class="alert alert-warning mt-4">
    <i class="bi bi-lightbulb"></i>
    <strong>Catatan:</strong> Validation errors (422) BOLEH detail karena membantu user memperbaiki input.
    Tapi JANGAN sampai mengungkap informasi internal system seperti nama table atau struktur database.
</div>
