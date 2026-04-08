{{--
    Login View untuk Secure Version - BAC/IDOR Lab
    Halaman login khusus untuk versi yang dilindungi Policy
--}}

@extends('layouts.app')

@section('title', 'Login Secure - BAC/IDOR Lab')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">

        {{-- BAC Lab Badge --}}
        <div class="text-center mb-4">
            <span class="badge bg-success px-4 py-2 fs-6">
                <i class="bi bi-shield-check"></i> SECURE VERSION
            </span>
            <p class="text-muted mt-2 small">BAC/IDOR Lab - Policy Protected</p>
        </div>

        <div class="card shadow border-success">
            <div class="card-header text-center bg-success text-white">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i> Login (Secure)
                </h4>
            </div>
            <div class="card-body p-4">

                {{-- Secure Info --}}
                <div class="alert alert-success small mb-4">
                    <strong><i class="bi bi-shield-check"></i> Versi Aman</strong>
                    <p class="mb-1 mt-2">
                        Setelah login, coba akses ticket milik orang lain - Anda akan mendapat <strong>403 Forbidden</strong>!
                    </p>
                    <code class="d-block mt-2">/bac-lab/secure/tickets/6 → 403</code>
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
                    <input type="hidden" name="redirect_to" value="{{ route('bac-lab.secure.tickets.index') }}">

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login ke Secure Version
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
            </div>
        </div>

        {{-- Test Scenario --}}
        <div class="card mt-3 border-success">
            <div class="card-header bg-success text-white">
                <i class="bi bi-shield-check"></i> Skenario Test (Policy Protection)
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Login sebagai <code>attacker@test.com</code></li>
                    <li>Lihat ticket milik sendiri (ID #8, #9)</li>
                    <li>Coba akses: <code>/bac-lab/secure/tickets/6</code></li>
                    <li class="text-success fw-bold">
                        <i class="bi bi-shield-check"></i> 403 FORBIDDEN - Dilindungi Policy!
                    </li>
                </ol>
            </div>
        </div>

        {{-- Code Preview --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <small><i class="bi bi-code-slash"></i> Secure Implementation</small>
            </div>
            <div class="card-body p-0">
                <pre class="bg-dark text-light p-3 mb-0 small"><code><span class="text-success">// SecureController.php</span>
public function __construct()
{
    <span class="text-success">// Auto-authorize semua action</span>
    $this->authorizeResource(Ticket::class, 'ticket');
}

public function show(Ticket $ticket)
{
    <span class="text-success">// Policy dipanggil otomatis!</span>
    return view('...', compact('ticket'));
}

<span class="text-info">// TicketPolicy.php</span>
public function view(User $user, Ticket $ticket): bool
{
    <span class="text-success">// Hanya owner atau admin</span>
    return $ticket->user_id === $user->id
        || $user->isAdmin();
}</code></pre>
            </div>
        </div>

        {{-- Back Links --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('bac-lab.home') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Lab
            </a>
            <a href="{{ route('bac-lab.vulnerable.login') }}" class="btn btn-outline-danger">
                Coba Vulnerable <i class="bi bi-arrow-right"></i>
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
