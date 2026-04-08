{{--
    File Upload Lab - Overview Page
    Materi teori tentang Logging dan File Upload Basics
--}}

@extends('layouts.app')

@section('title', 'Overview: ' . ($section === 'logging' ? 'Secure Logging' : 'File Upload Basics'))

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                @if($section === 'logging')
                    <i class="bi bi-journal-text text-info"></i> Secure Logging & Monitoring
                @else
                    <i class="bi bi-cloud-arrow-up text-warning"></i> File Upload Security Basics
                @endif
            </h2>
            <p class="text-muted mb-0">Minggu 5 - Hari 3: Materi Teori</p>
        </div>
        <a href="{{ route('file-upload-lab.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Lab
        </a>
    </div>

    {{-- Section Navigation --}}
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $section === 'logging' ? 'active' : '' }}"
               href="{{ route('file-upload-lab.overview', ['section' => 'logging']) }}">
                <i class="bi bi-journal-text"></i> Logging
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $section === 'upload-basics' ? 'active' : '' }}"
               href="{{ route('file-upload-lab.overview', ['section' => 'upload-basics']) }}">
                <i class="bi bi-cloud-arrow-up"></i> File Upload Basics
            </a>
        </li>
    </ul>

    @if($section === 'logging')
        @include('file-upload-lab.partials.overview-logging')
    @else
        @include('file-upload-lab.partials.overview-upload')
    @endif

</div>
@endsection
