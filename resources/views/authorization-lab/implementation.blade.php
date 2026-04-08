{{--
    Implementation Guide - Minggu 4 Hari 2: Authorization

    Menampilkan code examples untuk Gates, Policies, dan Middleware
--}}

@extends('layouts.app')

@section('title', 'Implementasi Authorization')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="bi bi-code-slash"></i> Implementasi Authorization</h2>
                <p class="text-muted mb-0">Code examples untuk Gates, Policies, dan Middleware</p>
            </div>
            <a href="{{ route('authorization-lab.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Navigation Tabs --}}
        <ul class="nav nav-tabs" id="implementationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="gates-tab" data-bs-toggle="tab" data-bs-target="#gates" type="button">
                    <i class="bi bi-gate"></i> Gates
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="policies-tab" data-bs-toggle="tab" data-bs-target="#policies" type="button">
                    <i class="bi bi-file-earmark-code"></i> Policies
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="middleware-tab" data-bs-toggle="tab" data-bs-target="#middleware" type="button">
                    <i class="bi bi-shield"></i> Middleware
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="blade-tab" data-bs-toggle="tab" data-bs-target="#blade" type="button">
                    <i class="bi bi-braces"></i> Blade Directives
                </button>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content" id="implementationTabContent">

            {{-- Gates Tab --}}
            <div class="tab-pane fade show active" id="gates" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5><i class="bi bi-gate text-primary"></i> Gates - Simple Authorization</h5>
                        <p class="text-muted">
                            Gates adalah closure sederhana untuk check authorization.
                            Cocok untuk aksi yang tidak terkait model tertentu.
                        </p>

                        <div class="alert alert-info">
                            <strong>Kapan pakai Gates?</strong>
                            <ul class="mb-0">
                                <li>Akses ke admin panel</li>
                                <li>Fitur yang hanya untuk role tertentu</li>
                                <li>Authorization yang tidak melibatkan model</li>
                            </ul>
                        </div>

                        <h6>1. Define Gates di AppServiceProvider</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// app/Providers/AppServiceProvider.php</span>

use Illuminate\Support\Facades\Gate;
use App\Models\User;

public function boot(): void
{
    <span class="text-success">// Gate: access-admin</span>
    Gate::define('access-admin', function (User $user): bool {
        return $user->isAdmin();
    });

    <span class="text-success">// Gate: view-reports (admin & staff)</span>
    Gate::define('view-reports', function (User $user): bool {
        return $user->hasAnyRole(['admin', 'staff']);
    });

    <span class="text-success">// Gate: manage-users (admin only)</span>
    Gate::define('manage-users', function (User $user): bool {
        return $user->isAdmin();
    });
}</code></pre>

                        <h6 class="mt-4">2. Check Gates di Controller</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// app/Http/Controllers/AdminController.php</span>

public function dashboard()
{
    <span class="text-success">// Cara 1: Menggunakan Gate facade</span>
    if (Gate::denies('access-admin')) {
        abort(403);
    }

    <span class="text-success">// Cara 2: Menggunakan authorize() method</span>
    $this->authorize('access-admin');

    <span class="text-success">// Cara 3: Menggunakan Gate::allows()</span>
    if (Gate::allows('access-admin')) {
        // Do something
    }

    return view('admin.dashboard');
}</code></pre>
                    </div>
                </div>
            </div>

            {{-- Policies Tab --}}
            <div class="tab-pane fade" id="policies" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5><i class="bi bi-file-earmark-code text-success"></i> Policies - Model-based Authorization</h5>
                        <p class="text-muted">
                            Policies adalah class yang mengorganisir authorization logic untuk model tertentu.
                        </p>

                        <div class="alert alert-success">
                            <strong>Kapan pakai Policies?</strong>
                            <ul class="mb-0">
                                <li>CRUD operations pada model (view, create, update, delete)</li>
                                <li>Logic authorization yang kompleks</li>
                                <li>Perlu reusable authorization untuk satu model</li>
                            </ul>
                        </div>

                        <h6>1. Generate Policy</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code>php artisan make:policy TicketPolicy --model=Ticket</code></pre>

                        <h6 class="mt-4">2. Implementasi Policy Methods</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// app/Policies/TicketPolicy.php</span>

class TicketPolicy
{
    <span class="text-success">/**
     * Siapa yang boleh melihat list tickets?
     */</span>
    public function viewAny(User $user): bool
    {
        <span class="text-success">// Semua user yang login boleh lihat (filtered by role)</span>
        return true;
    }

    <span class="text-success">/**
     * Siapa yang boleh melihat ticket tertentu?
     */</span>
    public function view(User $user, Ticket $ticket): bool
    {
        <span class="text-success">// Admin & Staff lihat semua</span>
        if ($user->hasAnyRole(['admin', 'staff'])) {
            return true;
        }

        <span class="text-success">// User hanya lihat milik sendiri</span>
        return $ticket->user_id === $user->id;
    }

    <span class="text-success">/**
     * Siapa yang boleh update ticket?
     */</span>
    public function update(User $user, Ticket $ticket): bool
    {
        <span class="text-success">// Admin bisa update semua</span>
        if ($user->isAdmin()) {
            return true;
        }

        <span class="text-success">// Staff hanya yang assigned ke mereka</span>
        if ($user->isStaff()) {
            return $ticket->assigned_to === $user->id;
        }

        <span class="text-success">// User hanya milik sendiri & status open</span>
        return $ticket->user_id === $user->id
            && $ticket->status === 'open';
    }

    <span class="text-success">/**
     * Siapa yang boleh delete ticket?
     */</span>
    public function delete(User $user, Ticket $ticket): bool
    {
        <span class="text-success">// Hanya admin yang bisa delete</span>
        return $user->isAdmin();
    }
}</code></pre>

                        <h6 class="mt-4">3. Register Policy</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// app/Providers/AppServiceProvider.php</span>

use App\Models\Ticket;
use App\Policies\TicketPolicy;

public function boot(): void
{
    Gate::policy(Ticket::class, TicketPolicy::class);
}</code></pre>

                        <h6 class="mt-4">4. Gunakan di Controller</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// app/Http/Controllers/TicketController.php</span>

public function edit(Ticket $ticket)
{
    <span class="text-success">// Check authorization menggunakan policy</span>
    $this->authorize('update', $ticket);

    return view('tickets.edit', compact('ticket'));
}

public function destroy(Ticket $ticket)
{
    $this->authorize('delete', $ticket);

    $ticket->delete();
    return redirect()->route('tickets.index');
}</code></pre>
                    </div>
                </div>
            </div>

            {{-- Middleware Tab --}}
            <div class="tab-pane fade" id="middleware" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5><i class="bi bi-shield text-warning"></i> Middleware - Route Protection</h5>
                        <p class="text-muted">
                            Middleware untuk memproteksi route berdasarkan role sebelum request mencapai controller.
                        </p>

                        <div class="alert alert-warning">
                            <strong>Kapan pakai Middleware?</strong>
                            <ul class="mb-0">
                                <li>Proteksi group routes berdasarkan role</li>
                                <li>Check authorization sebelum masuk controller</li>
                                <li>Reusable protection untuk multiple routes</li>
                            </ul>
                        </div>

                        <h6>1. Buat Middleware</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code>php artisan make:middleware RoleMiddleware</code></pre>

                        <h6 class="mt-4">2. Implementasi Middleware</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// app/Http/Middleware/RoleMiddleware.php</span>

class RoleMiddleware
{
    <span class="text-success">/**
     * Handle an incoming request.
     *
     * PENTING: Laravel passes comma-separated params as SEPARATE arguments!
     * Gunakan variadic parameter (...$roles) bukan string tunggal
     */</span>
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        <span class="text-success">// Check if user is authenticated</span>
        if (! $request->user()) {
            return redirect()->route('login');
        }

        <span class="text-success">// Check if user has any of the allowed roles</span>
        if (! $request->user()->hasAnyRole($roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}</code></pre>

                        <h6 class="mt-4">3. Register Middleware</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// bootstrap/app.php</span>

->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})</code></pre>

                        <h6 class="mt-4">4. Gunakan di Routes</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-info">// routes/web.php</span>

<span class="text-success">// Single role</span>
Route::middleware('role:admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);
});

<span class="text-success">// Multiple roles</span>
Route::get('/reports', [AdminController::class, 'reports'])
    ->middleware(['auth', 'role:admin,staff']);

<span class="text-success">// Group dengan prefix</span>
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
    });</code></pre>
                    </div>
                </div>
            </div>

            {{-- Blade Directives Tab --}}
            <div class="tab-pane fade" id="blade" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5><i class="bi bi-braces text-info"></i> Blade Directives - View Authorization</h5>
                        <p class="text-muted">
                            Gunakan directive @@can, @@cannot, @@canany untuk conditional rendering di Blade.
                        </p>

                        <h6>@@can / @@cannot - Check Single Ability</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-success">@{{-- Check Gate --}}</span>
@@can('access-admin')
    &lt;a href="/admin"&gt;Admin Panel&lt;/a&gt;
@@endcan

<span class="text-success">@{{-- Check Policy dengan model --}}</span>
@@can('update', $​ticket)
    &lt;a href="@{{ route('tickets.edit', $​ticket) }}"&gt;Edit&lt;/a&gt;
@@endcan

<span class="text-success">@{{-- Check Policy tanpa instance (create) --}}</span>
@@can('create', App\Models\Ticket::class)
    &lt;a href="@{{ route('tickets.create') }}"&gt;Buat Tiket&lt;/a&gt;
@@endcan

<span class="text-success">@{{-- Negative check --}}</span>
@@cannot('delete', $​ticket)
    &lt;span class="text-muted"&gt;Anda tidak bisa menghapus tiket ini&lt;/span&gt;
@@endcannot</code></pre>

                        <h6 class="mt-4">@@canany - Check Multiple Abilities</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-success">@{{-- Show jika punya salah satu ability --}}</span>
@@canany(['update', 'delete'], $​ticket)
    &lt;div class="btn-group"&gt;
        @@can('update', $​ticket)
            &lt;a href="..." class="btn btn-primary"&gt;Edit&lt;/a&gt;
        @@endcan
        @@can('delete', $​ticket)
            &lt;button class="btn btn-danger"&gt;Hapus&lt;/button&gt;
        @@endcan
    &lt;/div&gt;
@@endcanany</code></pre>

                        <h6 class="mt-4">@@auth / @@guest - Check Authentication</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code>@@auth
    <span class="text-success">@{{-- User sudah login --}}</span>
    &lt;span&gt;Welcome, @{{ auth()-&gt;user()-&gt;name }}&lt;/span&gt;
@@endauth

@@guest
    <span class="text-success">@{{-- User belum login --}}</span>
    &lt;a href="@{{ route('login') }}"&gt;Login&lt;/a&gt;
@@endguest</code></pre>

                        <h6 class="mt-4">Custom Check dengan Role</h6>
                        <pre class="bg-dark text-light p-3 rounded"><code><span class="text-success">@{{-- Check role langsung (tanpa Gate/Policy) --}}</span>
@@if(auth()-&gt;user()-&gt;isAdmin())
    &lt;span class="badge bg-danger"&gt;Admin&lt;/span&gt;
@@elseif(auth()-&gt;user()-&gt;isStaff())
    &lt;span class="badge bg-primary"&gt;Staff&lt;/span&gt;
@@else
    &lt;span class="badge bg-secondary"&gt;User&lt;/span&gt;
@@endif

<span class="text-success">@{{-- Check multiple roles --}}</span>
@@if(auth()-&gt;user()-&gt;hasAnyRole(['admin', 'staff']))
    &lt;a href="@{{ route('admin.reports') }}"&gt;Reports&lt;/a&gt;
@@endif</code></pre>
                    </div>
                </div>
            </div>

        </div>

        {{-- Test It Out --}}
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-play-circle"></i> Coba Sekarang!
            </div>
            <div class="card-body text-center">
                <p>Login dengan role berbeda dan lihat bagaimana authorization bekerja:</p>
                <a href="{{ route('authorization-lab.login') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Login dengan Test Account
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
