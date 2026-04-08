@extends('layouts.app')

@section('title', 'Dashboard - Vulnerable')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        
        <div class="card vulnerable-border">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-speedometer2"></i> Dashboard (Vulnerable)
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h5 class="alert-heading">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Selamat datang, {{ $user->name ?? 'Unknown' }}!
                    </h5>
                    <p>Anda login dengan <strong>Vulnerable Authentication</strong>.</p>
                    <p class="mb-0 small">
                        <i class="bi bi-bug"></i> Sesi ini TIDAK AMAN!
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-person"></i> User Info
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $user->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $user->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Password:</strong></td>
                                        <td>
                                            <code class="text-danger">{{ $user->password ?? 'N/A' }}</code>
                                            <br>
                                            <span class="badge bg-danger">PLAINTEXT!</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Session ID:</strong></td>
                                        <td>
                                            <code class="small">{{ Str::limit(session()->getId(), 20) }}...</code>
                                            <span class="badge bg-danger">NOT Regenerated</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-bug"></i> Security Status
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Rate Limiting
                                        <span class="badge bg-danger">✗ Disabled</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Password Hashing
                                        <span class="badge bg-danger">✗ Plaintext!</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Session Regeneration
                                        <span class="badge bg-danger">✗ Not Done</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        CSRF Protection
                                        <span class="badge bg-success">✓ Active</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-4">
                    <h6><i class="bi bi-lightbulb"></i> Apa yang salah?</h6>
                    <ol class="mb-0 small">
                        <li>Password terlihat jelas (tidak di-hash)</li>
                        <li>Session ID tidak di-regenerate setelah login</li>
                        <li>Tidak ada rate limiting untuk login</li>
                        <li>User data langsung disimpan di session</li>
                    </ol>
                </div>

                <div class="mt-4">
                    <a href="{{ route('vulnerable.logout') }}" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout (Vulnerable)
                    </a>
                    <a href="{{ route('auth-lab.comparison') }}" class="btn btn-outline-primary ms-2">
                        <i class="bi bi-arrows-angle-expand"></i> Lihat Comparison
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
