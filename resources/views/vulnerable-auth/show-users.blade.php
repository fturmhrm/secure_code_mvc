@extends('layouts.app')

@section('title', 'Show Users - Vulnerable Database')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        {{-- Warning Banner --}}
        <div class="alert alert-danger">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-octagon-fill fs-1 me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">⚠️ DATABASE EXPOSED!</h5>
                    <p class="mb-0">
                        Halaman ini menunjukkan betapa bahayanya menyimpan password <strong>PLAINTEXT</strong>.
                        Jika database bocor, semua password langsung terekspos!
                    </p>
                </div>
            </div>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-database-exclamation"></i>
                    Tabel: vulnerable_users (Password Plaintext!)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                    Password (PLAINTEXT!)
                                </th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <code class="text-danger fs-5">{{ $user->password }}</code>
                                    <span class="badge bg-danger ms-2">EXPOSED!</span>
                                </td>
                                <td>{{ $user->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mb-0">Belum ada user terdaftar.</p>
                                    <a href="{{ route('vulnerable.register') }}" class="btn btn-sm btn-danger mt-2">
                                        Register untuk Demo
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Comparison with Secure --}}
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check"></i>
                    Bandingkan: Tabel users (Password Hashed)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Password (HASHED - AMAN)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $secureUsers = \App\Models\User::take(5)->get();
                            @endphp
                            @forelse($secureUsers as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <code class="small text-muted">{{ Str::limit($user->password, 50) }}...</code>
                                    <span class="badge bg-success ms-2">HASHED (bcrypt)</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    Belum ada user di tabel secure.
                                    <a href="{{ route('register') }}">Register di Secure</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Key Takeaways --}}
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb"></i> Key Takeaways
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-danger">❌ Vulnerable (Plaintext)</h6>
                        <ul class="small">
                            <li>Password langsung terbaca</li>
                            <li>Jika DB bocor = semua akun compromised</li>
                            <li>Attacker bisa login ke akun apapun</li>
                            <li>Bisa digunakan untuk credential stuffing</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">✓ Secure (Hashed)</h6>
                        <ul class="small">
                            <li>Password tidak bisa di-reverse</li>
                            <li>Jika DB bocor = password tetap aman</li>
                            <li>Attacker tidak bisa login langsung</li>
                            <li>Setiap hash unik (ada salt)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('auth-lab.comparison') }}" class="btn btn-primary">
                <i class="bi bi-arrows-angle-expand"></i> Lihat Full Comparison
            </a>
        </div>

    </div>
</div>
@endsection
