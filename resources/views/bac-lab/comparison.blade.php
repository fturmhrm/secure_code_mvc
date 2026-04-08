{{--
    Comparison View untuk BAC/IDOR Lab
    Perbandingan implementasi Secure vs Vulnerable
--}}

@extends('layouts.app')

@section('title', 'Comparison - BAC/IDOR Lab')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">

        {{-- Header --}}
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">
                <i class="bi bi-arrows-angle-expand"></i>
                Comparison: Secure vs Vulnerable
            </h1>
            <p class="lead text-muted">
                Perbandingan implementasi authorization yang aman dan rentan (IDOR)
            </p>
        </div>

        {{-- Section 1: Controller Implementation --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-code-slash"></i> 1. Controller - show() Method
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <h6>SecureController.php:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-success">// Constructor - Auto authorize!</span>
public function __construct()
{
    $this->authorizeResource(
        <span class="text-info">Ticket::class</span>, 'ticket'
    );
}

public function show(<span class="text-info">Ticket</span> $ticket)
{
    <span class="text-success">// Route model binding</span>
    <span class="text-success">// Policy sudah dipanggil otomatis!</span>

    $ticket->load('user');
    return view('...', compact('ticket'));
}</code></pre>
                                <span class="badge bg-success mt-2">AMAN - Ada authorization</span>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <h6>VulnerableController.php:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-danger">// Tidak ada constructor authorization!</span>

public function show(<span class="text-warning">$id</span>)
{
    <span class="text-danger">// Langsung find tanpa check!</span>
    $ticket = Ticket::findOrFail($id);

    <span class="text-danger">// TIDAK ADA:</span>
    <span class="text-danger">// if ($ticket->user_id !== auth()->id())</span>

    return view('...', compact('ticket'));
}</code></pre>
                                <span class="badge bg-danger mt-2">RENTAN - IDOR Attack!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Policy --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-lock"></i> 2. Laravel Policy (Authorization)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE - Menggunakan Policy
                            </div>
                            <div class="card-body">
                                <h6>TicketPolicy.php:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code>class TicketPolicy
{
    <span class="text-success">// Siapa boleh lihat ticket?</span>
    public function view(User $user, Ticket $ticket): bool
    {
        <span class="text-success">// Hanya owner atau admin</span>
        return $ticket->user_id === $user->id
            || $user->isAdmin();
    }

    <span class="text-success">// Siapa boleh update?</span>
    public function update(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id === $user->id
            || $user->hasAnyRole(['admin', 'staff']);
    }

    <span class="text-success">// Siapa boleh delete?</span>
    public function delete(User $user, Ticket $ticket): bool
    {
        <span class="text-success">// Hanya admin!</span>
        return $user->isAdmin();
    }
}</code></pre>
                                <div class="alert alert-success small mb-0 mt-2">
                                    <strong>Hasil:</strong> User hanya bisa akses data miliknya sendiri
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE - Tidak Ada Policy
                            </div>
                            <div class="card-body">
                                <h6>VulnerableController.php:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code>class VulnerableController
{
    <span class="text-danger">// Tidak pakai Policy!</span>
    <span class="text-danger">// Tidak ada authorizeResource()</span>

    public function show($id)
    {
        <span class="text-danger">// Ambil data langsung tanpa check</span>
        $ticket = Ticket::findOrFail($id);

        <span class="text-danger">// User bisa akses ticket SIAPAPUN</span>
        <span class="text-danger">// dengan mengganti ID di URL:</span>
        <span class="text-danger">// /tickets/1 → /tickets/2 → /tickets/3</span>

        return view('...', compact('ticket'));
    }
}</code></pre>
                                <div class="alert alert-danger small mb-0 mt-2">
                                    <strong>Hasil:</strong> Attacker bisa akses semua data!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Index Page Query --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i> 3. Index Page - Query Scoping
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>public function index()
{
    $user = auth()->user();

    <span class="text-success">// Filter berdasarkan role</span>
    if ($user->isAdmin()) {
        <span class="text-success">// Admin lihat semua</span>
        $tickets = Ticket::with('user')
            ->latest()->get();
    } else {
        <span class="text-success">// User hanya lihat milik sendiri</span>
        $tickets = $user->tickets()
            ->with('user')->latest()->get();
    }

    return view('...', compact('tickets'));
}</code></pre>
                                <div class="alert alert-success small mb-0 mt-2">
                                    User biasa: Lihat 2 ticket (milik sendiri)<br>
                                    Admin: Lihat semua ticket
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <pre class="bg-dark text-light p-3 rounded small"><code>public function index()
{
    <span class="text-danger">// TIDAK ADA FILTER!</span>
    <span class="text-danger">// Semua user bisa lihat SEMUA ticket</span>

    $tickets = Ticket::with('user')
        ->latest()
        ->get();

    <span class="text-danger">// Information disclosure:</span>
    <span class="text-danger">// User bisa lihat data sensitif</span>
    <span class="text-danger">// dari SEMUA user lain!</span>

    return view('...', compact('tickets'));
}</code></pre>
                                <div class="alert alert-danger small mb-0 mt-2">
                                    Semua user: Lihat SEMUA 10 ticket!<br>
                                    <span class="text-danger">⚠️ Data leak!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Route Model Binding --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-signpost-split"></i> 4. Route & Model Binding
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Secure --}}
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-shield-check"></i> SECURE
                            </div>
                            <div class="card-body">
                                <h6>Route:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-success">// Menggunakan Route Model Binding</span>
Route::resource('tickets', SecureController::class);

<span class="text-success">// Laravel otomatis resolve:</span>
<span class="text-success">// {ticket} → Ticket model instance</span></code></pre>

                                <h6 class="mt-3">Controller:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-success">// Type-hint dengan Model</span>
public function show(<span class="text-info">Ticket</span> $ticket)
{
    <span class="text-success">// $ticket sudah resolved</span>
    <span class="text-success">// Policy dipanggil SEBELUM method</span>
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Vulnerable --}}
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle"></i> VULNERABLE
                            </div>
                            <div class="card-body">
                                <h6>Route:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-danger">// Menggunakan ID langsung</span>
Route::get('/tickets/{id}', [
    VulnerableController::class, 'show'
]);

<span class="text-danger">// ID bisa dimanipulasi di URL!</span></code></pre>

                                <h6 class="mt-3">Controller:</h6>
                                <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-danger">// Parameter $id langsung</span>
public function show(<span class="text-warning">$id</span>)
{
    <span class="text-danger">// Manual find - tidak ada protection</span>
    $ticket = Ticket::findOrFail($id);
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5: Attack Demo --}}
        <div class="card mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-bug"></i> 5. IDOR Attack Demo
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-person-fill-exclamation text-danger"></i> Attacker (attacker@test.com)</h6>
                        <p class="small text-muted">Punya ticket ID: #8, #9</p>

                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="small mb-2"><strong>Step 1:</strong> Login sebagai attacker</p>
                                <p class="small mb-2"><strong>Step 2:</strong> Buka ticket sendiri:</p>
                                <code class="d-block bg-dark text-light p-2 rounded mb-2">
                                    /bac-lab/vulnerable/tickets/8 ✅
                                </code>
                                <p class="small mb-2"><strong>Step 3:</strong> Ganti ID di URL:</p>
                                <code class="d-block bg-dark text-light p-2 rounded mb-2">
                                    /bac-lab/vulnerable/tickets/<span class="text-danger">6</span> ✅
                                </code>
                                <p class="small mb-0 text-danger">
                                    <strong>⚠️ BERHASIL</strong> melihat ticket milik victim!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6><i class="bi bi-bullseye text-warning"></i> Victim (victim@test.com)</h6>
                        <p class="small text-muted">Punya ticket ID: #6, #7 (data sensitif!)</p>

                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="small mb-2"><strong>Ticket #6:</strong></p>
                                <div class="alert alert-warning small py-2 mb-2">
                                    <strong>Masalah gaji tidak dibayar</strong><br>
                                    <small class="text-muted">Informasi keuangan pribadi</small>
                                </div>
                                <p class="small mb-2"><strong>Ticket #7:</strong></p>
                                <div class="alert alert-warning small py-2 mb-0">
                                    <strong>Bug di sistem payroll</strong><br>
                                    <small class="text-muted">Celah keamanan internal</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="text-success"><i class="bi bi-shield-check"></i> Di Versi Secure:</h6>
                <code class="d-block bg-dark text-light p-2 rounded">
                    /bac-lab/secure/tickets/6 → <span class="text-danger">403 FORBIDDEN</span>
                </code>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Summary Comparison
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Aspek</th>
                            <th class="text-success">✅ Secure</th>
                            <th class="text-danger">❌ Vulnerable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Authorization</td>
                            <td>Policy + authorizeResource()</td>
                            <td>Tidak ada</td>
                        </tr>
                        <tr>
                            <td>Route Binding</td>
                            <td>Type-hint (Ticket $ticket)</td>
                            <td>Raw ID ($id)</td>
                        </tr>
                        <tr>
                            <td>Index Query</td>
                            <td>Scoped by role/ownership</td>
                            <td>Ambil semua data</td>
                        </tr>
                        <tr>
                            <td>IDOR Protection</td>
                            <td>403 Forbidden</td>
                            <td>Bisa akses data siapapun</td>
                        </tr>
                        <tr>
                            <td>Data Exposure</td>
                            <td>Minimal</td>
                            <td>Full disclosure</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="{{ route('bac-lab.home') }}" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left"></i> Kembali ke Lab
            </a>
            <a href="{{ route('bac-lab.vulnerable.login') }}" class="btn btn-danger btn-lg">
                <i class="bi bi-exclamation-triangle"></i> Coba Vulnerable
            </a>
            <a href="{{ route('bac-lab.secure.login') }}" class="btn btn-success btn-lg">
                <i class="bi bi-shield-check"></i> Coba Secure
            </a>
        </div>

    </div>
</div>
@endsection
