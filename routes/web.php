<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CsrfLabController;
use App\Http\Controllers\DemoBladeController;
use App\Http\Controllers\Lab\SecureController;
use App\Http\Controllers\Lab\VulnerableController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecurityTestController;
use App\Http\Controllers\SqliLabController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ValidationLabController;
use App\Http\Controllers\VulnerableAuth\VulnerableLoginController;
use App\Http\Controllers\VulnerableAuth\VulnerableRegisterController;
use App\Http\Controllers\XSSLabController;
use App\Http\Controllers\FileUpload\SecureUploadController;
use App\Http\Controllers\FileUpload\VulnerableUploadController;
use Illuminate\Support\Facades\Route;

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

/*
|--------------------------------------------------------------------------
| Web Routes - Contoh untuk Hari 3 MVC Laravel
|--------------------------------------------------------------------------
|
| Tambahkan route di bawah ini ke file routes/web.php di proyek Laravel Anda
|
*/

// ============================================
// BASIC ROUTES (Contoh)
// ============================================

Route::get('/', function () {
    return view('home');
});

// Route sederhana dengan Closure
Route::get('/hello', function () {
    return 'Hello World! Selamat datang di Bootcamp Secure Coding!';
});

// Route yang mengembalikan JSON
Route::get('/api/status', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Server berjalan dengan baik',
        'time' => now()->toDateTimeString(),
    ]);
});

// ============================================
// RESOURCE ROUTES - TICKETS
// ============================================

// MINGGU 4 HARI 2: Tickets sekarang dilindungi dengan 'auth' middleware
// Authorization detail ditangani oleh TicketPolicy
Route::middleware('auth')->group(function () {
    // Route::resource() otomatis membuat 7 routes:
    // GET    /tickets           → TicketController@index    (tickets.index)
    // GET    /tickets/create    → TicketController@create   (tickets.create)
    // POST   /tickets           → TicketController@store    (tickets.store)
    // GET    /tickets/{ticket}  → TicketController@show     (tickets.show)
    // GET    /tickets/{ticket}/edit → TicketController@edit (tickets.edit)
    // PUT    /tickets/{ticket}  → TicketController@update   (tickets.update)
    // DELETE /tickets/{ticket}  → TicketController@destroy  (tickets.destroy)
    Route::resource('tickets', TicketController::class);

    // Route tambahan untuk update status (Admin/Staff)
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
        ->name('tickets.update-status');

    // Route untuk assign ticket ke staff (Admin only)
    Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->name('tickets.assign');
});

// ============================================
// ADMIN ROUTES - Protected by Role Middleware
// MINGGU 4 HARI 2: Authorization Implementation
// ============================================
use App\Http\Controllers\AdminController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard - Overview statistics
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management - List all users
    Route::get('/users', [AdminController::class, 'users'])->name('users');

    // All Tickets - Admin view with filters
    Route::get('/tickets', [AdminController::class, 'allTickets'])->name('tickets');

    // Assign ticket to staff
    Route::post('/tickets/{ticket}/assign', [AdminController::class, 'assignTicket'])
        ->name('tickets.assign');
});

// Reports - Accessible by Admin & Staff
Route::get('/reports', [AdminController::class, 'reports'])
    ->middleware(['auth', 'role:staff,admin'])
    ->name('admin.reports');

// ============================================
// ALTERNATIVE: ROUTES MANUAL
// ============================================
// Jika ingin mendefinisikan secara manual:
//
// Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
// Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
// Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
// Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
// Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
// Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
// Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

/*
|--------------------------------------------------------------------------
| Routes untuk Hari 4 - Blade Templating & XSS Prevention
|--------------------------------------------------------------------------
|
| Tambahkan routes ini ke file routes/web.php di proyek Laravel Anda
|
*/

// =========================================
// DEMO BLADE TEMPLATING
// =========================================

Route::prefix('demo-blade')->name('demo-blade.')->group(function () {
    Route::get('/', [DemoBladeController::class, 'index'])->name('index');
    Route::get('/directives', [DemoBladeController::class, 'directives'])->name('directives');
    Route::get('/components', [DemoBladeController::class, 'components'])->name('components');
    Route::get('/includes', [DemoBladeController::class, 'includes'])->name('includes');
    Route::get('/stacks', [DemoBladeController::class, 'stacks'])->name('stacks');
});

// =========================================
// XSS LAB - VULNERABLE & SECURE
// =========================================

Route::prefix('xss-lab')->name('xss-lab.')->group(function () {
    Route::get('/', [XSSLabController::class, 'index'])->name('index');

    // Reset comments untuk demo ulang
    Route::post('/reset-comments', [XSSLabController::class, 'resetComments'])->name('reset-comments');

    // Reflected XSS
    Route::get('/reflected/vulnerable', [XSSLabController::class, 'reflectedVulnerable'])
        ->name('reflected.vulnerable');
    Route::get('/reflected/secure', [XSSLabController::class, 'reflectedSecure'])
        ->name('reflected.secure');

    // Stored XSS
    Route::get('/stored/vulnerable', [XSSLabController::class, 'storedVulnerable'])
        ->name('stored.vulnerable');
    Route::post('/stored/vulnerable', [XSSLabController::class, 'storedVulnerableStore'])
        ->name('stored.vulnerable.store');

    Route::get('/stored/secure', [XSSLabController::class, 'storedSecure'])
        ->name('stored.secure');
    Route::post('/stored/secure', [XSSLabController::class, 'storedSecureStore'])
        ->name('stored.secure.store');

    // DOM-Based XSS
    Route::get('/dom/vulnerable', [XSSLabController::class, 'domVulnerable'])
        ->name('dom.vulnerable');
    Route::get('/dom/secure', [XSSLabController::class, 'domSecure'])
        ->name('dom.secure');
});

// =========================================
// COMMENTS ROUTES
// =========================================
// Nested routes untuk comments di bawah tickets

// Store comment (POST)
Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])
    ->name('comments.store');
// Delete comment (DELETE)
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
    ->name('comments.destroy');
// Update comment (optional) - (PUT/PATCH)
Route::put('/comments/{comment}', [CommentController::class, 'update'])
    ->name('comments.update');

// =========================================
// SECURITY TESTING ROUTES
// =========================================
// Dashboard untuk testing keamanan aplikasi
// PENTING: Jangan aktifkan di production!

Route::prefix('security-testing')->name('security-testing.')->group(function () {
    // Dashboard index
    Route::get('/', [SecurityTestController::class, 'index'])->name('index');

    // XSS Testing
    Route::get('/xss', [SecurityTestController::class, 'xssTest'])->name('xss');

    // CSRF Testing
    Route::get('/csrf', [SecurityTestController::class, 'csrfTest'])->name('csrf');
    Route::post('/csrf', [SecurityTestController::class, 'csrfTestPost'])->name('csrf.post');

    // Security Headers Testing
    Route::get('/headers', [SecurityTestController::class, 'headersTest'])->name('headers');

    // Audit Checklist
    Route::get('/audit', [SecurityTestController::class, 'auditChecklist'])->name('audit');
});

// ================================================================
// VALIDATION LAB ROUTES
// ================================================================
Route::prefix('validation-lab')->name('validation-lab.')->group(function () {
    // Index - Menu Lab
    Route::get('/', [ValidationLabController::class, 'index'])
        ->name('index');

    // ----- VULNERABLE FORM -----
    // Form tanpa server-side validation
    Route::get('/vulnerable', [ValidationLabController::class, 'vulnerableForm'])
        ->name('vulnerable');
    Route::post('/vulnerable', [ValidationLabController::class, 'vulnerableSubmit'])
        ->name('vulnerable.submit');
    Route::post('/vulnerable/clear', [ValidationLabController::class, 'vulnerableClear'])
        ->name('vulnerable.clear');

    // ----- SECURE FORM -----
    // Form dengan server-side validation
    Route::get('/secure', [ValidationLabController::class, 'secureForm'])
        ->name('secure');
    Route::post('/secure', [ValidationLabController::class, 'secureSubmit'])
        ->name('secure.submit');
    Route::post('/secure/clear', [ValidationLabController::class, 'secureClear'])
        ->name('secure.clear');
});

// ================================================================
// TICKET CRUD ROUTES
// ================================================================
// Menggunakan Resource Controller dengan Form Request validation
// Store: StoreTicketRequest
// Update: UpdateTicketRequest
// Route::resource('tickets', TicketController::class);

// ================================================================
// API DEMO (untuk demo bypass dengan curl/Postman)
// ================================================================
Route::prefix('api')->group(function () {
    // Vulnerable endpoint - tanpa CSRF dan validation
    Route::post('/vulnerable-submit', [ValidationLabController::class, 'apiVulnerable'])
        ->withoutMiddleware(['web']);
});

// ================================================================
// CSRF LAB ROUTES
// ================================================================
Route::prefix('csrf-lab')->name('csrf-lab.')->group(function () {
    // Index - Menu Lab
    Route::get('/', [CsrfLabController::class, 'index'])
        ->name('index');

    // How It Works - Penjelasan CSRF
    Route::get('/how-it-works', [CsrfLabController::class, 'howItWorks'])
        ->name('how-it-works');

    // Attack Demo - Simulasi serangan
    Route::get('/attack-demo', [CsrfLabController::class, 'attackDemo'])
        ->name('attack-demo');

    // Protection Demo - Demo protection
    Route::get('/protection-demo', [CsrfLabController::class, 'protectionDemo'])
        ->name('protection-demo');

    // AJAX Demo - CSRF untuk AJAX
    Route::get('/ajax-demo', [CsrfLabController::class, 'ajaxDemo'])
        ->name('ajax-demo');

    // ----- ACTION ROUTES -----

    // Secure transfer (dengan CSRF protection normal)
    Route::post('/secure-transfer', [CsrfLabController::class, 'secureTransfer'])
        ->name('secure-transfer');

    // Protected action
    Route::post('/protected-action', [CsrfLabController::class, 'protectedAction'])
        ->name('protected-action');

    // AJAX action
    Route::post('/ajax-action', [CsrfLabController::class, 'ajaxAction'])
        ->name('ajax-action');

    // Reset demo data
    Route::post('/reset', [CsrfLabController::class, 'resetDemo'])
        ->name('reset');
});

// ================================================================
// VULNERABLE ROUTE (untuk demo - di-exclude dari CSRF middleware)
// ⚠️ JANGAN GUNAKAN PATTERN INI DI PRODUCTION!
// ================================================================
// Route ini perlu di-exclude dari VerifyCsrfToken middleware
// untuk demonstrasi serangan CSRF
Route::post('/csrf-lab/vulnerable-transfer', [CsrfLabController::class, 'vulnerableTransfer'])
    ->name('csrf-lab.vulnerable-transfer')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Route untuk demo PROTECTED transfer (DENGAN CSRF - akan return 419 jika tanpa token)
Route::post('/csrf-lab/protected-transfer', [CsrfLabController::class, 'protectedTransfer'])
    ->name('csrf-lab.protected-transfer');

// ============================================
// SQL INJECTION LAB ROUTES
// ============================================
Route::prefix('sqli-lab')->name('sqli-lab.')->group(function () {

    // Menu utama
    Route::get('/', [SqliLabController::class, 'index'])->name('index');

    // Halaman edukasi
    Route::get('/how-it-works', [SqliLabController::class, 'howItWorks'])->name('how-it-works');
    Route::get('/cheatsheet', [SqliLabController::class, 'cheatsheet'])->name('cheatsheet');

    // ============================================
    // VULNERABLE ENDPOINTS (UNTUK DEMO)
    // ============================================
    // PERINGATAN: Endpoint ini SENGAJA VULNERABLE!
    // Hanya untuk pembelajaran - JANGAN gunakan di production!

    // Vulnerable Search - String concatenation
    Route::get('/vulnerable-search', [SqliLabController::class, 'vulnerableSearch'])
        ->name('vulnerable-search');

    // Vulnerable Login - Authentication bypass
    Route::get('/vulnerable-login', [SqliLabController::class, 'vulnerableLogin'])
        ->name('vulnerable-login');
    Route::post('/vulnerable-login', [SqliLabController::class, 'vulnerableLoginSubmit'])
        ->name('vulnerable-login-submit');

    // Blind SQL Injection Demo
    Route::get('/blind-sqli', [SqliLabController::class, 'blindSqli'])
        ->name('blind-sqli');
    Route::post('/blind-sqli/boolean', [SqliLabController::class, 'blindSqliBooleanCheck'])
        ->name('blind-sqli-boolean');
    Route::post('/blind-sqli/time', [SqliLabController::class, 'blindSqliTimeCheck'])
        ->name('blind-sqli-time');

    // ============================================
    // SECURE ENDPOINTS (BEST PRACTICE)
    // ============================================

    // Secure Search - 4 metode aman
    Route::get('/secure-search', [SqliLabController::class, 'secureSearch'])
        ->name('secure-search');

    // ============================================
    // UTILITY ROUTES
    // ============================================

    // Seed demo data
    Route::get('/seed-data', [SqliLabController::class, 'seedData'])
        ->name('seed');

    // Reset data
    Route::get('/reset-data', [SqliLabController::class, 'resetData'])
        ->name('reset');
});

/*
|--------------------------------------------------------------------------
| Web Routes - Authentication Lab (Minggu 4 Hari 1)
|--------------------------------------------------------------------------
|
| Lab ini berisi routes untuk implementasi authentication:
| - Secure: Menggunakan Laravel Breeze (routes/auth.php)
| - Vulnerable: Login/Register dengan kerentanan untuk demonstrasi
|
*/

// ============================================================================
// Auth Lab Pages (Minggu 4 Hari 1 - Authentication)
// ============================================================================
Route::prefix('auth-lab')->name('auth-lab.')->group(function () {
    Route::get('/', function () {
        return view('auth-lab.index');
    })->name('index');

    Route::get('/comparison', function () {
        return view('auth-lab.comparison');
    })->name('comparison');
});

// ============================================================================
// Authorization Lab Pages (Minggu 4 Hari 2 - Authorization)
// ============================================================================
Route::prefix('authorization-lab')->name('authorization-lab.')->group(function () {
    Route::get('/', function () {
        return view('authorization-lab.index');
    })->name('index');

    Route::get('/login', function () {
        return view('authorization-lab.login');
    })->name('login');

    Route::get('/implementation', function () {
        return view('authorization-lab.implementation');
    })->name('implementation');
});

// ============================================================================
// Secure Dashboard (Requires Auth)
// ============================================================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('auth.dashboard');
    })->name('dashboard');
});

// ============================================================================
// VULNERABLE Authentication Routes (Demo Only)
// ============================================================================
Route::prefix('vulnerable')->name('vulnerable.')->group(function () {

    // Login - No middleware protection
    Route::get('/login', [VulnerableLoginController::class, 'create'])
        ->name('login');
    Route::post('/login', [VulnerableLoginController::class, 'store'])
        ->name('login.submit');

    // Register - No middleware protection
    Route::get('/register', [VulnerableRegisterController::class, 'create'])
        ->name('register');
    Route::post('/register', [VulnerableRegisterController::class, 'store'])
        ->name('register.submit');

    // Dashboard - Uses session instead of auth middleware
    Route::get('/dashboard', function () {
        if (! session()->has('vulnerable_user')) {
            return redirect()->route('vulnerable.login');
        }

        // Pass user from session to view
        return view('vulnerable-auth.dashboard', [
            'user' => session('vulnerable_user'),
        ]);
    })->name('dashboard');

    // Logout
    Route::post('/logout', [VulnerableLoginController::class, 'destroy'])
        ->name('logout');

    // DEMO: Show all users (Vulnerable - information disclosure)
    Route::get('/show-users', [VulnerableRegisterController::class, 'showUsers'])
        ->name('show-users');

    // DEMO: Brute force statistics
    Route::get('/brute-force-stats', [VulnerableLoginController::class, 'bruteForceStats'])
        ->name('brute-force-stats');
});

// ============================================================================
// BAC/IDOR Lab Routes (Minggu 4 Hari 4 - Broken Access Control)
// ============================================================================

// Public routes (tidak perlu login untuk baca materi)
Route::prefix('bac-lab')->name('bac-lab.')->group(function () {

    // Lab Index - Overview & Pilihan Secure/Vulnerable (public)
    Route::get('/', function () {
        return view('bac-lab.index');
    })->name('home');

    // Comparison Page (public)
    Route::get('/comparison', function () {
        return view('bac-lab.comparison');
    })->name('comparison');

    // Login Pages untuk masing-masing versi (public)
    Route::get('/vulnerable/login', function () {
        return view('bac-lab.vulnerable.login');
    })->name('vulnerable.login');

    Route::get('/secure/login', function () {
        return view('bac-lab.secure.login');
    })->name('secure.login');
});

// Protected routes (perlu login untuk demo)
Route::middleware('auth')->prefix('bac-lab')->name('bac-lab.')->group(function () {

    // ========================================
    // VULNERABLE VERSION (IDOR Demo)
    // ========================================
    // ⚠️ Route ini SENGAJA dibuat vulnerable untuk demonstrasi
    // JANGAN gunakan pattern ini di production!

    Route::prefix('vulnerable')->name('vulnerable.')->group(function () {

        Route::get('/tickets', [VulnerableController::class, 'index'])
            ->name('tickets.index');

        Route::get('/tickets/{id}', [VulnerableController::class, 'show'])
            ->name('tickets.show');

        Route::get('/tickets/{id}/edit', [VulnerableController::class, 'edit'])
            ->name('tickets.edit');

        Route::put('/tickets/{id}', [VulnerableController::class, 'update'])
            ->name('tickets.update');

        Route::delete('/tickets/{id}', [VulnerableController::class, 'destroy'])
            ->name('tickets.destroy');
    });

    // ========================================
    // SECURE VERSION (dengan Policy)
    // ========================================
    // ✅ Route ini menggunakan Policy untuk authorization
    // GUNAKAN pattern ini di production!

    Route::prefix('secure')->name('secure.')->group(function () {

        // Resource route dengan route model binding
        // Policy akan otomatis di-check via authorizeResource()
        Route::resource('tickets', SecureController::class)
            ->parameters(['tickets' => 'ticket']);
    });
});

Route::prefix('file-upload-lab')->name('file-upload-lab.')->group(function () {
    // Lab Index
    Route::get('/', function () {
        return view('file-upload-lab.index');
    })->name('index');

    // Overview/Materi (Logging & Upload Basics)
    Route::get('/overview/{section?}', function ($section = 'logging') {
        $validSections = ['logging', 'upload-basics'];
        if (! in_array($section, $validSections)) {
            $section = 'logging';
        }

        return view('file-upload-lab.overview', compact('section'));
    })->name('overview');

    // =============================================
    // VULNERABLE UPLOAD LAB (Educational Purposes)
    // =============================================
    Route::prefix('vulnerable')->name('vulnerable.')->group(function () {
        // Lab index
        Route::get('/', [VulnerableUploadController::class, 'index'])->name('index');

        // Level 1: No validation
        Route::match(['get', 'post'], '/level1', [VulnerableUploadController::class, 'level1'])
            ->name('level1');

        // Level 2: Client-side only
        Route::match(['get', 'post'], '/level2', [VulnerableUploadController::class, 'level2'])
            ->name('level2');

        // Level 3: Blacklist bypass
        Route::match(['get', 'post'], '/level3', [VulnerableUploadController::class, 'level3'])
            ->name('level3');

        // Level 4: MIME type bypass
        Route::match(['get', 'post'], '/level4', [VulnerableUploadController::class, 'level4'])
            ->name('level4');

        // Level 5: Magic bytes bypass
        Route::match(['get', 'post'], '/level5', [VulnerableUploadController::class, 'level5'])
            ->name('level5');

        // View uploaded files
        Route::get('/files', [VulnerableUploadController::class, 'listFiles'])->name('files');

        // Clear all uploads
        Route::delete('/clear', [VulnerableUploadController::class, 'clearUploads'])->name('clear');
    });

    // =============================================
    // SECURE UPLOAD IMPLEMENTATION
    // =============================================
    Route::prefix('secure')->name('secure.')->group(function () {
        // Secure upload demo
        Route::get('/', [SecureUploadController::class, 'index'])->name('index');

        // Upload file
        Route::post('/upload', [SecureUploadController::class, 'upload'])->name('upload');

        // Serve file (via controller)
        Route::get('/file/{filename}', [SecureUploadController::class, 'serve'])->name('serve');

        // Download file
        Route::get('/download/{filename}', [SecureUploadController::class, 'download'])->name('download');

        // Delete file
        Route::delete('/file/{filename}', [SecureUploadController::class, 'delete'])->name('delete');

        // Clear all
        Route::delete('/clear', [SecureUploadController::class, 'clearAll'])->name('clear');
    });
});

// ============================================================================
// Error Handling Demo (Minggu 5 Hari 2 - Error Handling & Information Disclosure)
// ============================================================================

Route::get('/error-handling-demo', function () {
    return view('error-handling-demo.index');
})->name('error-handling-demo');

// ============================================================================
// Secure Auth Routes (Laravel Breeze)
// ============================================================================
require __DIR__.'/auth.php';
