{{--
    Login View untuk Vulnerable Version - BAC/IDOR Lab
    Halaman login untuk versi yang SENGAJA VULNERABLE
--}}

@extends('layouts.app')

@section('title', 'Login Vulnerable - BAC/IDOR Lab')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">

        {{-- BAC Lab Badge --}}
        <div class="text-center mb-4">
            <span class="badge bg-danger px-4 py-2 fs-6">
                <i class="bi bi-exclamation-triangle"></i> VULNERABLE VERSION
            </span>
            <p class="text-muted mt-2 small">BAC/IDOR Lab - Tidak Ada Authorization!</p>
        </div>

        <div class="card shadow border-danger">
            <div class="card-header text-center bg-danger text-white">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i> Login (Vulnerable)
                </h4>
            </div>
            <div class="card-body p-4">

                {{-- Vulnerable Warning --}}
                <div class="alert alert-danger small mb-4">
                    <strong><i class="bi bi-exclamation-triangle"></i> Versi IDOR!</strong>
                    <p class="mb-1 mt-2">
                        Setelah login, coba akses ticket milik orang lain dengan mengganti ID di URL - <strong>BERHASIL!</strong>
                    </p>
                    <code class="d-block mt-2">/bac-lab/vulnerable/tickets/6 → Terlihat!</code>
                </div>

                {{-- Login Form --}}
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    {{-- Email Field --}}
                    <div class="form-floating mb-3">
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="email@example.com"
                               required
                               autofocus>
                        <label for="email">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password Field --}}
                    <div class="form-floating mb-3">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Password"
                               required>
                        <label for="password">
                            <i class="bi bi-key"></i> Password
                        </label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Hidden redirect field --}}
                    <input type="hidden" name="redirect_to" value="{{ route('bac-lab.vulnerable.tickets.index') }}">

                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login ke Vulnerable Version
                    </button>
                </form>
            </div>
        </div>

        {{-- Quick Fill Buttons --}}
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-lightning"></i> Quick Fill (Klik untuk auto-fill)
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm quick-fill"
                            data-email="attacker@test.com" data-password="password">
                        <i class="bi bi-person-fill-exclamation"></i> Attacker
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm quick-fill"
                            data-email="victim@test.com" data-password="password">
                        <i class="bi bi-bullseye"></i> Victim
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm quick-fill"
                            data-email="admin@wikrama.sch.id" data-password="password">
                        <i class="bi bi-person-gear"></i> Admin
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Disarankan login sebagai Attacker untuk demo IDOR</small>
            </div>
        </div>

        {{-- IDOR Attack Steps --}}
        <div class="card mt-3 border-danger">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-bug"></i> Skenario IDOR Attack
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Login sebagai <code>attacker@test.com</code></li>
                    <li>Lihat ticket milik sendiri: <code>/bac-lab/vulnerable/tickets/8</code></li>
                    <li>Ganti ID menjadi <code>6</code> (milik victim)</li>
                    <li class="text-danger fw-bold">
                        <i class="bi bi-exclamation-triangle"></i> BERHASIL melihat data victim!
                    </li>
                </ol>
            </div>
        </div>

        {{-- Code Preview --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <small><i class="bi bi-code-slash"></i> Vulnerable Implementation</small>
            </div>
            <div class="card-body p-0">
                <pre class="bg-dark text-light p-3 mb-0 small"><code><span class="text-danger">// VulnerableController.php</span>
public function show($id)
{
    <span class="text-danger">// TIDAK ADA authorization check!</span>
    $ticket = Ticket::findOrFail($id);

    <span class="text-danger">// User bisa akses ticket SIAPAPUN</span>
    <span class="text-danger">// hanya dengan mengganti ID di URL!</span>

    return view('...', compact('ticket'));
}

<span class="text-warning">// Yang SEHARUSNYA ada:</span>
<span class="text-success">// if ($ticket->user_id !== auth()->id()) {</span>
<span class="text-success">//     abort(403);</span>
<span class="text-success">// }</span></code></pre>
            </div>
        </div>

        {{-- Target Tickets --}}
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-bullseye"></i> Target Tickets (Victim's Data)
            </div>
            <div class="card-body">
                <p class="small mb-2">Setelah login sebagai attacker, coba akses URL berikut:</p>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <code>/bac-lab/vulnerable/tickets/<strong>6</strong></code>
                        <span class="badge bg-warning text-dark">victim's ticket</span>
                    </li>
                    <li>
                        <code>/bac-lab/vulnerable/tickets/<strong>7</strong></code>
                        <span class="badge bg-warning text-dark">victim's ticket</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Back Links --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('bac-lab.home') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Lab
            </a>
            <a href="{{ route('bac-lab.secure.login') }}" class="btn btn-outline-success">
                Coba Secure <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.quick-fill').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('email').value = this.dataset.email;
            document.getElementById('password').value = this.dataset.password;
            this.classList.add('active');
            setTimeout(() => this.classList.remove('active'), 200);
        });
    });
</script>
@endpush
