<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SecurityTestController;

/*
|--------------------------------------------------------------------------
| Routes untuk Hari 5 - Lab Lengkap XSS Prevention
|--------------------------------------------------------------------------
|
| Tambahkan routes ini ke file routes/web.php di proyek Laravel Anda
| Routes ini menambahkan fitur:
| 1. Comments pada Tickets
| 2. Security Testing Dashboard
|
*/

// =========================================
// COMMENTS ROUTES
// =========================================
// Nested routes untuk comments di bawah tickets

// Route::middleware('auth')->group(function () {
//     // Store comment (POST)
//     Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])
//         ->name('comments.store');
    
//     // Delete comment (DELETE)
//     Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
//         ->name('comments.destroy');
    
//     // Update comment (optional) - (PUT/PATCH)
//     Route::put('/comments/{comment}', [CommentController::class, 'update'])
//         ->name('comments.update');
// });

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

/*
|--------------------------------------------------------------------------
| CARA PENGGUNAAN
|--------------------------------------------------------------------------
|
| 1. Copy isi file ini ke routes/web.php Anda
| 2. Atau, include file ini di routes/web.php:
|
|    // Di routes/web.php
|    require __DIR__.'/hari-5.php';
|
| 3. Pastikan controllers sudah di-copy ke folder yang sesuai
| 4. Jalankan: php artisan route:list untuk verifikasi
|
*/

/*
|--------------------------------------------------------------------------
| ROUTES LENGKAP MINGGU 2 (Untuk Referensi)
|--------------------------------------------------------------------------
|
| Kombinasi routes dari Hari 3, 4, dan 5:
|
| // Hari 3 - Tickets CRUD
| Route::resource('tickets', TicketController::class)->middleware('auth');
|
| // Hari 4 - Demo Blade
| Route::prefix('demo-blade')->name('demo-blade.')->group(function () {
|     Route::get('/', [DemoBladeController::class, 'index'])->name('index');
|     Route::get('/directives', [DemoBladeController::class, 'directives'])->name('directives');
|     Route::get('/components', [DemoBladeController::class, 'components'])->name('components');
|     Route::get('/includes', [DemoBladeController::class, 'includes'])->name('includes');
|     Route::get('/stacks', [DemoBladeController::class, 'stacks'])->name('stacks');
| });
|
| // Hari 4 - XSS Lab
| Route::prefix('xss-lab')->name('xss-lab.')->group(function () {
|     Route::get('/', [XSSLabController::class, 'index'])->name('index');
|     Route::post('/reset-comments', [XSSLabController::class, 'resetComments'])->name('reset-comments');
|     
|     // Reflected XSS
|     Route::get('/reflected/vulnerable', [XSSLabController::class, 'reflectedVulnerable'])->name('reflected.vulnerable');
|     Route::get('/reflected/secure', [XSSLabController::class, 'reflectedSecure'])->name('reflected.secure');
|     
|     // Stored XSS
|     Route::get('/stored/vulnerable', [XSSLabController::class, 'storedVulnerable'])->name('stored.vulnerable');
|     Route::post('/stored/vulnerable', [XSSLabController::class, 'storedVulnerableStore'])->name('stored.vulnerable.store');
|     Route::get('/stored/secure', [XSSLabController::class, 'storedSecure'])->name('stored.secure');
|     Route::post('/stored/secure', [XSSLabController::class, 'storedSecureStore'])->name('stored.secure.store');
|     
|     // DOM-Based XSS
|     Route::get('/dom/vulnerable', [XSSLabController::class, 'domVulnerable'])->name('dom.vulnerable');
|     Route::get('/dom/secure', [XSSLabController::class, 'domSecure'])->name('dom.secure');
| });
|
| // Hari 5 - Comments & Security Testing (di atas)
|
*/
