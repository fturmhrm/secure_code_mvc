@extends('layouts.app')

@section('title', 'Level 3: Blacklist Bypass')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <span class="badge bg-info me-2">Level 3</span>
                Blacklist Extension Validation
            </h2>
            <p class="text-muted mb-0">Blacklist yang tidak lengkap dan case-sensitive</p>
        </div>
        <div>
            <a href="{{ route('file-upload-lab.vulnerable.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Lab
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Upload Form --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Blacklist Protected Upload</h5>
                </div>
                <div class="card-body">
                    {{-- Show blacklist --}}
                    <div class="alert alert-info py-2 mb-3">
                        <strong>Blocked Extensions:</strong>
                        @foreach($blacklist ?? ['php', 'exe', 'sh', 'bat'] as $ext)
                            <code>.{{ $ext }}</code>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>

                    <form action="{{ route('file-upload-lab.vulnerable.level3') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select File to Upload</label>
                            <input type="file" name="file" class="form-control" required>
                            <div class="form-text">
                                <i class="bi bi-shield-exclamation text-warning"></i>
                                Files with blocked extensions will be rejected
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-upload"></i> Upload File
                        </button>
                    </form>

                    @if(isset($error))
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-x-circle"></i> {{ $error }}
                        </div>
                    @endif

                    @if(isset($message))
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle"></i> {{ $message }}
                            @if(isset($fileUrl))
                                <br>
                                <strong>URL:</strong>
                                <a href="{{ $fileUrl }}" target="_blank">{{ $fileUrl }}</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vulnerable Code --}}
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-code-slash"></i> Vulnerable Code</h6>
                </div>
                <div class="card-body bg-dark">
<pre class="text-light mb-0 small"><code><span class="text-secondary">// VULNERABLE: Incomplete blacklist, case-sensitive!</span>
$blacklist = [<span class="text-success">'php'</span>, <span class="text-success">'exe'</span>, <span class="text-success">'sh'</span>, <span class="text-success">'bat'</span>];

$extension = pathinfo($filename, PATHINFO_EXTENSION);

<span class="text-secondary">// Only exact match, case-sensitive!</span>
<span class="text-primary">if</span> (in_array($extension, $blacklist)) {
    <span class="text-primary">throw new</span> Exception(<span class="text-success">'Blocked!'</span>);
}

<span class="text-secondary">// Problems:</span>
<span class="text-secondary">// 1. "PHP" != "php" (case bypass)</span>
<span class="text-secondary">// 2. "php5" not in list (alt extension)</span>
<span class="text-secondary">// 3. "shell.php.jpg" passes (double ext)</span></code></pre>
                </div>
            </div>
        </div>

        {{-- Bypass Instructions --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-unlock"></i> Bypass Techniques</h5>
                </div>
                <div class="card-body">
                    <h6><span class="badge bg-success">✓ Tested</span> Case Variation Bypass</h6>
                    <p class="small">Blacklist case-sensitive, gunakan variasi case:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>shell.PHP</code> ← "PHP" != "php" <span class="text-success">✓</span><br>
                        <code>shell.Php</code><br>
                        <code>shell.pHp</code><br>
                        <code>shell.pHP</code>
                    </div>

                    <h6><span class="badge bg-warning text-dark">Server-Dependent</span> Alternative Extension Bypass</h6>
                    <p class="small">PHP punya banyak extension yang tidak di-blacklist:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>shell.php5</code> ← Alternative PHP ext<br>
                        <code>shell.php7</code><br>
                        <code>shell.phtml</code><br>
                        <code>shell.phar</code><br>
                        <code>shell.phps</code><br>
                        <code>shell.php3</code>
                    </div>
                    <div class="alert alert-info py-1 small mb-3">
                        <i class="bi bi-info-circle"></i>
                        <em>Memerlukan server config (Apache/Nginx) yang handle extension tersebut.
                        PHP built-in server hanya handle <code>.php</code>.</em>
                    </div>

                    <h6><span class="badge bg-warning text-dark">Server-Dependent</span> Double Extension Bypass</h6>
                    <p class="small">Apache mungkin eksekusi berdasarkan extension pertama:</p>
                    <div class="bg-dark text-light p-2 rounded small mb-3">
                        <code>shell.php.jpg</code> ← Passes blacklist check<br>
                        <code>shell.php.png</code><br>
                        <code>shell.php.txt</code>
                    </div>

                    <div class="alert alert-warning py-2 mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Server Configuration Required:</strong><br>
                        <small>
                        • Alternative extensions & double extension hanya bekerja jika server dikonfigurasi dengan
                        <code>AddHandler</code> atau <code>AddType</code> di Apache.<br>
                        • <strong>Case variation adalah bypass paling reliable</strong> karena hanya bergantung pada kode aplikasi.
                        </small>
                    </div>

                    @if(isset($fileUrl))
                        <div class="alert alert-danger mt-3 mb-0">
                            <h6><i class="bi bi-terminal"></i> Test:</h6>
                            <a href="{{ $fileUrl }}?cmd=whoami" target="_blank" class="text-danger small">
                                {{ $fileUrl }}?cmd=whoami
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Lesson Learned --}}
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Lesson Learned</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-danger"><i class="bi bi-x-circle"></i> Blacklist Problems:</h6>
                    <ul class="small">
                        <li><strong>Never complete</strong> - Selalu ada yang terlewat</li>
                        <li><strong>Case sensitivity</strong> - PHP, pHp, PHp, etc.</li>
                        <li><strong>Alternative extensions</strong> - php3, php5, phtml, phar</li>
                        <li><strong>Double extensions</strong> - shell.php.jpg</li>
                        <li><strong>Null byte</strong> - shell.php%00.jpg (old PHP)</li>
                        <li><strong>Maintenance burden</strong> - Harus selalu update</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success"><i class="bi bi-check-circle"></i> Use Whitelist Instead!</h6>
                    <div class="bg-dark text-light p-2 rounded small">
<pre class="mb-0"><code><span class="text-secondary">// SECURE: Whitelist only allowed types</span>
$whitelist = [<span class="text-success">'jpg'</span>, <span class="text-success">'jpeg'</span>, <span class="text-success">'png'</span>, <span class="text-success">'gif'</span>];

<span class="text-secondary">// Case-insensitive check</span>
$ext = strtolower($file->getClientOriginalExtension());

<span class="text-primary">if</span> (!in_array($ext, $whitelist)) {
    <span class="text-primary">throw new</span> Exception(<span class="text-success">'Invalid type!'</span>);
}</code></pre>
                    </div>
                    <p class="small mt-2 mb-0">
                        <strong>Whitelist = Default Deny.</strong> Hanya izinkan yang eksplisit diizinkan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
