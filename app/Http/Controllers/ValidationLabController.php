<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ValidationLabController
 * 
 * Controller untuk demo Lab Input Validation.
 * Mendemonstrasikan perbedaan antara:
 * - Form TANPA server-side validation (vulnerable)
 * - Form DENGAN server-side validation (secure)
 * 
 * Materi Minggu 3 - Hari 2: Input Validation
 */
class ValidationLabController extends Controller
{
    /**
     * Index - Menu lab validasi
     */
    public function index(): View
    {
        return view('validation-lab.index');
    }

    // ================================================================
    // VULNERABLE VERSION - JANGAN GUNAKAN DI PRODUCTION!
    // ================================================================

    /**
     * Show vulnerable form (tanpa server-side validation)
     */
    public function vulnerableForm(): View
    {
        return view('validation-lab.vulnerable', [
            'submissions' => session('vulnerable_submissions', []),
        ]);
    }

    /**
     * Process vulnerable form submission
     * 
     * ❌ VULNERABLE: Tidak ada server-side validation!
     * Data langsung diterima tanpa pengecekan apapun.
     * 
     * RISIKO:
     * - SQL Injection (jika langsung ke query)
     * - XSS (jika langsung ditampilkan)
     * - Data invalid masuk ke sistem
     * - Business logic bypass
     */
    public function vulnerableSubmit(Request $request): RedirectResponse
    {
        // ❌ VULNERABLE: Tidak ada validasi sama sekali!
        // Data langsung diambil dari request
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'submitted_at' => now()->format('H:i:s'),
        ];

        // Simpan ke session untuk demo (dalam real app, ini ke database)
        $submissions = session('vulnerable_submissions', []);
        $submissions[] = $data;
        session(['vulnerable_submissions' => $submissions]);

        return redirect()
            ->route('validation-lab.vulnerable')
            ->with('success', 'Data diterima TANPA validasi! (Berbahaya!)');
    }

    /**
     * Clear vulnerable submissions
     */
    public function vulnerableClear(): RedirectResponse
    {
        session()->forget('vulnerable_submissions');
        return redirect()->route('validation-lab.vulnerable');
    }

    // ================================================================
    // SECURE VERSION - BEST PRACTICE
    // ================================================================

    /**
     * Show secure form (dengan server-side validation)
     */
    public function secureForm(): View
    {
        return view('validation-lab.secure', [
            'submissions' => session('secure_submissions', []),
        ]);
    }

    /**
     * Process secure form submission
     * 
     * ✅ SECURE: Semua input divalidasi di server-side!
     * 
     * FLOW:
     * 1. validate() dipanggil
     * 2. Jika GAGAL: auto redirect back dengan errors
     * 3. Jika SUKSES: lanjut ke code berikutnya
     * 4. $validated berisi data yang sudah bersih
     */
    public function secureSubmit(Request $request): RedirectResponse
    {
        // ✅ SECURE: Server-side validation
        $validated = $request->validate([
            // Name: wajib, string, 2-100 karakter
            'name' => 'required|string|min:2|max:100',
            
            // Email: wajib, format email valid
            'email' => 'required|email:rfc,dns',
            
            // Age: wajib, integer, 17-100 tahun
            'age' => 'required|integer|min:17|max:100',
            
            // Priority: wajib, harus salah satu dari whitelist
            'priority' => 'required|in:low,medium,high',
            
            // Message: wajib, string, 10-1000 karakter
            'message' => 'required|string|min:10|max:1000',
        ], [
            // Custom error messages dalam Bahasa Indonesia
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal :min karakter.',
            'name.max' => 'Nama maksimal :max karakter.',
            
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            
            'age.required' => 'Umur wajib diisi.',
            'age.integer' => 'Umur harus berupa angka.',
            'age.min' => 'Umur minimal :min tahun.',
            'age.max' => 'Umur maksimal :max tahun.',
            
            'priority.required' => 'Prioritas wajib dipilih.',
            'priority.in' => 'Prioritas tidak valid.',
            
            'message.required' => 'Pesan wajib diisi.',
            'message.min' => 'Pesan minimal :min karakter.',
            'message.max' => 'Pesan maksimal :max karakter.',
        ]);

        // ✅ Sanitasi tambahan (defense in depth)
        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['message'] = strip_tags(trim($validated['message']));
        $validated['submitted_at'] = now()->format('H:i:s');

        // Simpan ke session untuk demo
        $submissions = session('secure_submissions', []);
        $submissions[] = $validated;
        session(['secure_submissions' => $submissions]);

        return redirect()
            ->route('validation-lab.secure')
            ->with('success', 'Data berhasil divalidasi dan disimpan dengan aman!');
    }

    /**
     * Clear secure submissions
     */
    public function secureClear(): RedirectResponse
    {
        session()->forget('secure_submissions');
        return redirect()->route('validation-lab.secure');
    }

    // ================================================================
    // BYPASS DEMO - Menunjukkan bahwa client-side bisa di-bypass
    // ================================================================

    /**
     * Demo API endpoint tanpa CSRF (untuk testing dengan curl/Postman)
     * 
     * ⚠️ HANYA UNTUK DEMO - Jangan gunakan di production!
     */
    public function apiVulnerable(Request $request)
    {
        // Menunjukkan bahwa tanpa server-side validation,
        // attacker bisa mengirim data apapun via API
        return response()->json([
            'status' => 'received',
            'message' => 'Data diterima TANPA validasi!',
            'data' => $request->all(),
            'warning' => 'Ini berbahaya! Data tidak divalidasi sama sekali.',
        ]);
    }
}
