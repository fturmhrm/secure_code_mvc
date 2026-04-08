{{--
    Login View untuk Minggu 4 Hari 2: Authorization Lab

    INTEGRASI MATERI:
    - Minggu 2: Blade templating dengan @extends, @section
    - Minggu 3: @csrf, @error, old() untuk form handling
    - Minggu 4 Hari 1: Security features info
    - Minggu 4 Hari 2: Test accounts dengan roles
--}}

@extends('layouts.app')

@section('title', 'Login - Authorization Lab')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">

        {{-- MINGGU 4 HARI 2: Authorization Badge --}}
        <div class="text-center mb-4">
            <span class="badge bg-primary px-4 py-2 fs-6">
                <i class="bi bi-shield-lock"></i> AUTHORIZATION LAB
            </span>
        </div>

        <div class="card shadow">
            <div class="card-header text-center bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </h4>
            </div>
            <div class="card-body p-4">

                {{-- MINGGU 4 HARI 1: Security Features Info --}}
                <div class="alert alert-info small mb-4">
                    <strong><i class="bi bi-shield-check"></i> Security Features:</strong>
                    <ul class="mb-0 mt-1">
                        <li>Rate limiting (5 attempts/min)</li>
                        <li>Password hashing verification</li>
                        <li>Session regeneration</li>
                        <li>CSRF protection</li>
                    </ul>
                </div>

                {{-- MINGGU 3: Form dengan @csrf --}}
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
                        {{-- MINGGU 3: Error display dengan @error directive --}}
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

                    {{-- Remember Me --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>
            </div>
        </div>

        {{-- MINGGU 4 HARI 2: Test Accounts dengan Role --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-person-badge"></i> Test Accounts (Login untuk test Authorization)
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Akses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-danger">
                            <td><code>admin@wikrama.sch.id</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge bg-danger">admin</span></td>
                            <td><small>Full access + admin panel</small></td>
                        </tr>
                        <tr class="table-primary">
                            <td><code>staff@wikrama.sch.id</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge bg-primary">staff</span></td>
                            <td><small>Manage assigned tickets</small></td>
                        </tr>
                        <tr>
                            <td><code>budi@student.wikrama.sch.id</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge bg-secondary">user</span></td>
                            <td><small>Own tickets only</small></td>
                        </tr>
                        <tr>
                            <td><code>siti@student.wikrama.sch.id</code></td>
                            <td><code>password</code></td>
                            <td><span class="badge bg-secondary">user</span></td>
                            <td><small>Own tickets only</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">
                <i class="bi bi-lightbulb"></i>
                <strong>Tips:</strong> Coba login dengan user berbeda untuk melihat perbedaan akses!
            </div>
        </div>

        {{-- Quick Fill Buttons --}}
        <div class="card mt-3">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-lightning"></i> Quick Fill (Klik untuk auto-fill)
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm quick-fill"
                            data-email="admin@wikrama.sch.id" data-password="password">
                        <i class="bi bi-person-gear"></i> Admin
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm quick-fill"
                            data-email="staff@wikrama.sch.id" data-password="password">
                        <i class="bi bi-person-badge"></i> Staff
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm quick-fill"
                            data-email="budi@student.wikrama.sch.id" data-password="password">
                        <i class="bi bi-person"></i> User (Budi)
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm quick-fill"
                            data-email="siti@student.wikrama.sch.id" data-password="password">
                        <i class="bi bi-person"></i> User (Siti)
                    </button>
                </div>
            </div>
        </div>

        {{-- Authorization Code Preview --}}
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <small><i class="bi bi-code-slash"></i> Authorization Code Preview</small>
            </div>
            <div class="card-body p-0">
                <pre class="bg-dark text-light p-3 mb-0 small"><code><span class="text-info">// TicketPolicy.php - Authorization Check</span>
public function update(User $user, Ticket $ticket): bool
{
    <span class="text-success">// Admin bisa update semua</span>
    if ($user-&gt;isAdmin()) {
        return true;
    }

    <span class="text-success">// Staff bisa update yang assigned ke mereka</span>
    if ($user-&gt;isStaff()) {
        return $ticket-&gt;assigned_to === $user-&gt;id;
    }

    <span class="text-success">// User hanya bisa update milik sendiri</span>
    return $ticket-&gt;belongsToUser($user) &amp;&amp;
           $ticket-&gt;isEditable();
}

<span class="text-info">// Blade: Conditional dengan @@can</span>
@@can('update', $ticket)
    &lt;a href="..."&gt;Edit&lt;/a&gt;
@@endcan</code></pre>
            </div>
        </div>

        {{-- Back to Lab Index --}}
        <div class="text-center mt-4">
            <a href="{{ route('authorization-lab.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Authorization Lab
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Quick fill functionality
    document.querySelectorAll('.quick-fill').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('email').value = this.dataset.email;
            document.getElementById('password').value = this.dataset.password;

            // Visual feedback
            this.classList.add('active');
            setTimeout(() => this.classList.remove('active'), 200);
        });
    });
</script>
@endpush
