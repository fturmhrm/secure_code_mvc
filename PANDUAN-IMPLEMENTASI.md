# Panduan Implementasi - Minggu 3 Hari 2: Input Validation

## Daftar Isi

1. [Overview](#overview)
2. [Step 1: Form Request Classes](#step-1-form-request-classes)
3. [Step 2: Update Controller](#step-2-update-controller)
4. [Step 3: Update Views](#step-3-update-views)
5. [Step 4: Validation Lab](#step-4-validation-lab)
6. [Step 5: Routes](#step-5-routes)
7. [Testing](#testing)
8. [Checklist Keamanan](#checklist-keamanan)

---

## Overview

Hari ini kita akan mengimplementasikan **Input Validation** yang secure menggunakan Laravel Form Request. Fokus utama:

1. **Server-side validation** - WAJIB untuk keamanan
2. **Custom error messages** - Bahasa Indonesia yang user-friendly
3. **Form Request classes** - Code organization yang baik
4. **Error handling di views** - UX yang baik

### ⚠️ Prinsip Dasar

```
╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║              NEVER TRUST USER INPUT!                          ║
║                                                               ║
║   Semua data dari user harus dianggap BERBAHAYA               ║
║   sampai divalidasi dan disanitasi.                           ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

---

## Step 1: Form Request Classes

### Generate Form Request

```bash
# Di terminal, jalankan:
php artisan make:request StoreTicketRequest
php artisan make:request UpdateTicketRequest
```

### Copy file ke project:

1. `app/Http/Requests/StoreTicketRequest.php`
2. `app/Http/Requests/UpdateTicketRequest.php`

### Penjelasan StoreTicketRequest:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    // Authorization - siapa yang boleh akses
    public function authorize(): bool
    {
        return true; // Semua user boleh buat tiket
    }

    // Sanitasi sebelum validasi
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim($this->title),
            'description' => trim($this->description),
        ]);
    }

    // Validation rules
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'priority' => ['required', 'in:low,medium,high'], // WHITELIST!
        ];
    }

    // Custom error messages
    public function messages(): array
    {
        return [
            'title.required' => 'Judul tiket wajib diisi.',
            'title.min' => 'Judul minimal :min karakter.',
            // ... dst
        ];
    }
}
```

---

## Step 2: Update Controller

### Sebelum (Tanpa Form Request):

```php
public function store(Request $request)
{
    // Validasi di controller - KOTOR!
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        // ... banyak rules
    ], [
        'title.required' => 'Judul wajib diisi.',
        // ... banyak messages
    ]);
    
    $ticket = Ticket::create($validated);
    return redirect()->route('tickets.show', $ticket);
}
```

### Sesudah (Dengan Form Request):

```php
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;

public function store(StoreTicketRequest $request)
{
    // Validasi OTOMATIS terjadi sebelum method dipanggil!
    // Jika gagal, auto redirect back dengan errors
    
    $ticket = Ticket::create($request->validated());
    return redirect()
        ->route('tickets.show', $ticket)
        ->with('success', 'Tiket berhasil dibuat!');
}

public function update(UpdateTicketRequest $request, Ticket $ticket)
{
    $ticket->update($request->validated());
    return redirect()
        ->route('tickets.show', $ticket)
        ->with('success', 'Tiket berhasil diperbarui!');
}
```

---

## Step 3: Update Views

### Error Display Pattern:

```blade
{{-- 1. Global Error Display --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h6>Oops! Ada kesalahan:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 2. Per-field Error Display --}}
<div class="mb-3">
    <label for="title">Judul</label>
    <input type="text" 
           name="title" 
           class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title') }}">
    
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

### Penjelasan:

| Directive | Fungsi |
|-----------|--------|
| `@error('field')` | Check apakah field punya error |
| `$message` | Pesan error untuk field tersebut |
| `$errors->any()` | Check apakah ada error sama sekali |
| `$errors->all()` | Ambil semua error messages |
| `old('field')` | Ambil input sebelumnya (untuk preserve input) |

---

## Step 4: Validation Lab

Lab ini mendemonstrasikan perbedaan antara form DENGAN dan TANPA server-side validation.

### Copy files:

1. `app/Http/Controllers/ValidationLabController.php`
2. `resources/views/validation-lab/index.blade.php`
3. `resources/views/validation-lab/vulnerable.blade.php`
4. `resources/views/validation-lab/secure.blade.php`

### Akses:

- `/validation-lab` - Menu lab
- `/validation-lab/vulnerable` - Form TANPA server validation
- `/validation-lab/secure` - Form DENGAN server validation

### Demo Bypass Client-Side:

1. Buka form vulnerable
2. Buka DevTools (F12)
3. Inspect input element
4. Hapus atribut `required`, `min`, `max`, `pattern`
5. Ubah `type="email"` ke `type="text"`
6. Submit form dengan data invalid
7. **Hasil:** Data invalid masuk ke sistem!

---

## Step 5: Routes

Tambahkan ke `routes/web.php`:

```php
use App\Http\Controllers\ValidationLabController;

// Validation Lab
Route::prefix('validation-lab')->name('validation-lab.')->group(function () {
    Route::get('/', [ValidationLabController::class, 'index'])->name('index');
    
    // Vulnerable
    Route::get('/vulnerable', [ValidationLabController::class, 'vulnerableForm'])->name('vulnerable');
    Route::post('/vulnerable', [ValidationLabController::class, 'vulnerableSubmit'])->name('vulnerable.submit');
    Route::post('/vulnerable/clear', [ValidationLabController::class, 'vulnerableClear'])->name('vulnerable.clear');
    
    // Secure
    Route::get('/secure', [ValidationLabController::class, 'secureForm'])->name('secure');
    Route::post('/secure', [ValidationLabController::class, 'secureSubmit'])->name('secure.submit');
    Route::post('/secure/clear', [ValidationLabController::class, 'secureClear'])->name('secure.clear');
});

// Tickets dengan Form Request
Route::resource('tickets', TicketController::class);
```

---

## Testing

### Test Case 1: Submit Form Kosong

**Input:** Kosongkan semua field, submit
**Expected:** Error messages muncul untuk semua required fields

### Test Case 2: Input Terlalu Pendek

**Input:** Title = "Bug" (kurang dari 5 karakter)
**Expected:** Error "Judul minimal 5 karakter."

### Test Case 3: Priority Invalid

**Input:** Via DevTools, ubah value select ke "urgent"
**Expected:** Error "Prioritas tidak valid."

### Test Case 4: Bypass Client-Side

**Input:** Hapus atribut HTML5 validation, submit data invalid
**Expected:** 
- Vulnerable form: Data masuk
- Secure form: Error muncul

### Test Case 5: Valid Input

**Input:** Semua field valid
**Expected:** Redirect ke show page dengan success message

---

## Checklist Keamanan

### ✅ Yang HARUS dilakukan:

- [ ] Semua form memiliki server-side validation
- [ ] String fields memiliki `max` length
- [ ] Numeric fields memiliki `min`/`max` range
- [ ] Enum/select menggunakan `in:value1,value2` (WHITELIST)
- [ ] Email menggunakan rule `email`
- [ ] Foreign keys menggunakan `exists:table,column`
- [ ] Error messages tidak expose sensitive info
- [ ] Form Request digunakan untuk code organization
- [ ] `$request->validated()` digunakan, bukan `$request->all()`

### ❌ Yang JANGAN dilakukan:

- [ ] Hanya mengandalkan client-side validation
- [ ] Skip validasi untuk "internal" endpoint
- [ ] Trust hidden fields tanpa validasi
- [ ] Gunakan blacklist approach
- [ ] Tampilkan technical error ke user
- [ ] Hardcode validation di banyak tempat

---

## Validation Rules Reference

### Basic Rules

| Rule | Fungsi |
|------|--------|
| `required` | Wajib diisi |
| `nullable` | Boleh null |
| `string` | Harus string |
| `integer` | Harus integer |
| `numeric` | Harus angka |
| `boolean` | Harus true/false |
| `array` | Harus array |

### Size Rules

| Rule | Fungsi |
|------|--------|
| `min:n` | Minimal n |
| `max:n` | Maksimal n |
| `between:a,b` | Antara a dan b |
| `size:n` | Harus tepat n |

### Format Rules

| Rule | Fungsi |
|------|--------|
| `email` | Format email valid |
| `url` | Format URL valid |
| `date` | Format tanggal valid |
| `regex:pattern` | Sesuai regex |

### Comparison Rules

| Rule | Fungsi |
|------|--------|
| `in:a,b,c` | Harus salah satu nilai (WHITELIST) |
| `not_in:a,b,c` | Tidak boleh salah satu |
| `confirmed` | Harus ada field `_confirmation` |
| `same:field` | Harus sama dengan field lain |

### Database Rules

| Rule | Fungsi |
|------|--------|
| `unique:table,column` | Tidak boleh duplikat |
| `exists:table,column` | Harus ada di database |

---

## Tugas

1. Implementasi `StoreTicketRequest` dan `UpdateTicketRequest`
2. Update controller menggunakan Form Request
3. Update views dengan error handling
4. Test dengan berbagai input (valid dan invalid)
5. Screenshot hasil testing

**Deadline:** Sebelum pulang hari ini
