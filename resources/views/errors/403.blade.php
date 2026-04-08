@extends('layouts.app')

@section('title', '403 - Forbidden')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <div class="py-5">
            <i class="bi bi-shield-exclamation display-1 text-danger"></i>
            <h1 class="display-4 mt-4">403</h1>
            <h2>Akses Ditolak</h2>
            <p class="lead text-muted">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
            </p>
            
            <hr class="my-4">
            
            <div class="alert alert-danger">
                <i class="bi bi-info-circle"></i>
                <strong>OWASP A01:2021 - Broken Access Control</strong><br>
                Sistem mendeteksi Anda mencoba mengakses resource yang tidak diizinkan.
            </div>

            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-house"></i> Kembali ke Dashboard
                </a>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                </a>
            </div>

            {{-- Debug info for development --}}
            @if(config('app.debug'))
                <div class="card mt-5 text-start">
                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-bug"></i> Debug Info (Development Only)
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>User:</th>
                                <td>{{ auth()->user()->name ?? 'Guest' }}</td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td>{{ auth()->user()->role ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Attempted URL:</th>
                                <td><code>{{ request()->url() }}</code></td>
                            </tr>
                            <tr>
                                <th>Method:</th>
                                <td><code>{{ request()->method() }}</code></td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
