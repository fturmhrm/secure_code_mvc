@extends('layouts.app')

@section('title', 'Lab Home')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="text-center mb-5">
            <h1 class="display-4">
                <i class="bi bi-shield-exclamation text-warning"></i>
                Lab: Broken Access Control
            </h1>
            <p class="lead text-muted">
                OWASP A01:2025 - #1 di OWASP Top 10!
            </p>
        </div>
    </div>
</div>

{{-- Info Box --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-danger h-100">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle"></i> IDOR Vulnerability
            </div>
            <div class="card-body">
                <p><strong>IDOR</strong> (Insecure Direct Object Reference) terjadi ketika:</p>
                <ul>
                    <li>Aplikasi menggunakan ID yang <strong>predictable</strong></li>
                    <li><strong>Tidak ada</strong> authorization check</li>
                    <li>User bisa ganti ID di URL untuk akses data orang lain</li>
                </ul>
                <div class="alert alert-danger small mb-0">
                    <code>/tickets/1</code> → <code>/tickets/2</code> (bukan miliknya)
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success h-100">
            <div class="card-header bg-success text-white">
                <i class="bi bi-shield-check"></i> Solusi: Laravel Policy
            </div>
            <div class="card-body">
                <p><strong>Policy</strong> adalah cara Laravel untuk authorization:</p>
                <ul>
                    <li>Centralized authorization logic</li>
                    <li>Otomatis check ownership</li>
                    <li>Consistent & testable</li>
                </ul>
                <div class="alert alert-success small mb-0">
                    <code>$this->authorize('view', $ticket);</code>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Test Account Info --}}
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-person-badge"></i> Test Accounts untuk BAC/IDOR Lab
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Tickets (ID)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>admin@wikrama.sch.id</code></td>
                    <td><code>password</code></td>
                    <td><span class="badge bg-danger">admin</span></td>
                    <td>#10</td>
                    <td>Bisa akses semua</td>
                </tr>
                <tr class="table-warning">
                    <td><code>victim@test.com</code></td>
                    <td><code>password</code></td>
                    <td><span class="badge bg-secondary">user</span></td>
                    <td><strong>#6, #7</strong></td>
                    <td>TARGET - Data sensitif</td>
                </tr>
                <tr class="table-danger">
                    <td><code>attacker@test.com</code></td>
                    <td><code>password</code></td>
                    <td><span class="badge bg-secondary">user</span></td>
                    <td>#8, #9</td>
                    <td>ATTACKER - Akan coba IDOR</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Comparison Section --}}
<div class="row">
    {{-- Vulnerable --}}
    <div class="col-md-6 mb-4">
        <div class="card border-danger h-100">
            <div class="card-header vulnerable-badge text-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> VULNERABLE VERSION
                </h5>
            </div>
            <div class="card-body">
                <p class="text-danger"><strong>JANGAN DITIRU!</strong></p>

                <h6>Cara Test IDOR:</h6>
                <ol>
                    <li>Login sebagai <code>attacker@test.com</code></li>
                    <li>Buka tiket milik sendiri: <code>/bac-lab/vulnerable/tickets/8</code></li>
                    <li>Ganti ID menjadi <code>6</code>: <code>/bac-lab/vulnerable/tickets/6</code></li>
                    <li><span class="text-danger">✓ BERHASIL</span> lihat data victim!</li>
                </ol>

                <div class="code-preview p-3 small">
                    <span class="comment">// ❌ VULNERABLE</span><br>
                    <span class="keyword">public function</span> show($id)<br>
                    {<br>
                    &nbsp;&nbsp;$ticket = Ticket::find($id);<br>
                    &nbsp;&nbsp;<span class="comment">// Tidak ada check!</span><br>
                    &nbsp;&nbsp;<span class="keyword">return</span> view(...);<br>
                    }
                </div>

                @auth
                    <a href="{{ route('bac-lab.vulnerable.tickets.index') }}" class="btn btn-danger mt-3">
                        <i class="bi bi-exclamation-triangle"></i> Coba Vulnerable
                    </a>
                @else
                    <a href="{{ route('bac-lab.login') }}" class="btn btn-outline-danger mt-3">
                        Login untuk test
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Secure --}}
    <div class="col-md-6 mb-4">
        <div class="card border-success h-100">
            <div class="card-header secure-badge text-white">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check"></i> SECURE VERSION
                </h5>
            </div>
            <div class="card-body">
                <p class="text-success"><strong>GUNAKAN INI!</strong></p>

                <h6>Cara Test Policy:</h6>
                <ol>
                    <li>Login sebagai <code>attacker@test.com</code></li>
                    <li>Buka tiket milik sendiri: <code>/bac-lab/secure/tickets/8</code></li>
                    <li>Ganti ID menjadi <code>6</code>: <code>/bac-lab/secure/tickets/6</code></li>
                    <li><span class="text-success">✗ 403 FORBIDDEN</span> - Dilindungi Policy!</li>
                </ol>

                <div class="code-preview p-3 small">
                    <span class="comment">// ✅ SECURE dengan Policy</span><br>
                    <span class="keyword">public function</span> show(Ticket $ticket)<br>
                    {<br>
                    &nbsp;&nbsp;<span class="comment">// authorizeResource() otomatis check</span><br>
                    &nbsp;&nbsp;<span class="keyword">return</span> view(...);<br>
                    }
                </div>

                @auth
                    <a href="{{ route('bac-lab.secure.tickets.index') }}" class="btn btn-success mt-3">
                        <i class="bi bi-shield-check"></i> Coba Secure
                    </a>
                @else
                    <a href="{{ route('bac-lab.login') }}" class="btn btn-outline-success mt-3">
                        Login untuk test
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- Quick Login --}}
@guest
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-box-arrow-in-right"></i> Login untuk Demo
    </div>
    <div class="card-body text-center">
        <p class="mb-3">Silakan login untuk mencoba demo IDOR vulnerable vs secure</p>
        <a href="{{ route('bac-lab.login') }}" class="btn btn-warning btn-lg">
            <i class="bi bi-box-arrow-in-right"></i> Login ke BAC Lab
        </a>
    </div>
</div>
@endguest
@endsection
