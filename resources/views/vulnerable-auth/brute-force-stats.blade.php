@extends('layouts.app')

@section('title', 'Brute Force Stats')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up"></i>
                    Statistik Brute Force Attack
                </h5>
            </div>
            <div class="card-body">
                
                {{-- Stats Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center">
                                <h2>{{ $stats['total'] ?? 0 }}</h2>
                                <small>Total Attempts</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h2>{{ $stats['failed'] ?? 0 }}</h2>
                                <small>Failed Attempts</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h2>{{ $stats['success'] ?? 0 }}</h2>
                                <small>Successful</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h2>{{ $stats['last_5_min'] ?? 0 }}</h2>
                                <small>Last 5 Minutes</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Key Point --}}
                <div class="alert alert-danger">
                    <strong><i class="bi bi-exclamation-triangle"></i> Perhatikan!</strong>
                    <p class="mb-0">
                        Tanpa rate limiting, attacker bisa melakukan <strong>unlimited attempts</strong>!
                        Ini memungkinkan brute force attack yang efektif.
                    </p>
                </div>

                {{-- Filter Form --}}
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Filter by email..."
                               value="{{ $email ?? '' }}">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </form>

                {{-- Attempts Table --}}
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Time</th>
                                <th>Email</th>
                                <th>IP Address</th>
                                <th>Type</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attempts as $attempt)
                            <tr>
                                <td>{{ $attempt->created_at->format('H:i:s') }}</td>
                                <td>{{ $attempt->email }}</td>
                                <td>{{ $attempt->ip_address }}</td>
                                <td>
                                    @if($attempt->type === 'vulnerable')
                                        <span class="badge bg-danger">Vulnerable</span>
                                    @else
                                        <span class="badge bg-success">Secure</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->successful)
                                        <span class="badge bg-success">Success</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada login attempts.
                                    <br>
                                    <a href="{{ route('vulnerable.login') }}">
                                        Coba login untuk generate data
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
                    <i class="bi bi-shield-check"></i> Bagaimana Secure Login Menangani Ini?
                </h5>
            </div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded"><code>// LoginRequest.php - Rate Limiting
public function ensureIsNotRateLimited(): void
{
    <span class="text-success">// Batasi 5 attempts per menit</span>
    if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
        return;
    }

    $seconds = RateLimiter::availableIn($this->throttleKey());

    <span class="text-warning">// Throw error dengan waktu tunggu</span>
    throw ValidationException::withMessages([
        'email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
    ]);
}</code></pre>

                <div class="alert alert-success mt-3 mb-0">
                    <strong>Hasil:</strong> Attacker hanya bisa mencoba 5 password per menit.
                    Brute force menjadi <strong>tidak praktis</strong>!
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
