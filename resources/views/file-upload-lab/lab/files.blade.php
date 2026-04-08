@extends('layouts.app')

@section('title', 'Uploaded Files')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-folder2-open text-warning"></i> Vulnerable Uploads Directory</h2>
            <p class="text-muted mb-0">Files in <code>/public/uploads/vulnerable/</code></p>
        </div>
        <div>
            <a href="{{ route('file-upload-lab.vulnerable.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Lab
            </a>
        </div>
    </div>

    {{-- Warning --}}
    <div class="alert alert-danger mb-4">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Warning:</strong> Files di direktori ini bisa dieksekusi!
        Jangan click file PHP kecuali Anda tahu apa yang Anda lakukan.
    </div>

    {{-- Clear Button --}}
    <div class="mb-4">
        <form action="{{ route('file-upload-lab.vulnerable.clear') }}" method="POST" class="d-inline"
              onsubmit="return confirm('Clear semua file?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Clear All Files
            </button>
        </form>
    </div>

    {{-- Files Table --}}
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-files"></i> Uploaded Files ({{ count($files) }})</h5>
        </div>
        <div class="card-body">
            @if(count($files) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                @php
                                    $isPHP = str_ends_with(strtolower($file['name']), '.php') ||
                                             str_ends_with(strtolower($file['name']), '.phtml') ||
                                             str_ends_with(strtolower($file['name']), '.php5');
                                @endphp
                                <tr class="{{ $isPHP ? 'table-danger' : '' }}">
                                    <td>
                                        @if($isPHP)
                                            <i class="bi bi-exclamation-triangle text-danger"></i>
                                        @else
                                            <i class="bi bi-file-earmark"></i>
                                        @endif
                                        <code>{{ $file['name'] }}</code>
                                    </td>
                                    <td>{{ number_format($file['size']) }} bytes</td>
                                    <td>{{ date('Y-m-d H:i:s', $file['modified']) }}</td>
                                    <td>
                                        <a href="{{ $file['url'] }}" target="_blank"
                                           class="btn btn-sm {{ $isPHP ? 'btn-danger' : 'btn-primary' }}">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        @if($isPHP)
                                            <a href="{{ $file['url'] }}?cmd=whoami" target="_blank"
                                               class="btn btn-sm btn-warning">
                                                <i class="bi bi-terminal"></i> Execute
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-folder2 display-1"></i>
                    <p class="mt-3">No files uploaded yet.</p>
                    <a href="{{ route('file-upload-lab.vulnerable.level1') }}" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload First File
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
