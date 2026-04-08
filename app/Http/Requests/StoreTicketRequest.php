<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreTicketRequest
 *
 * Form Request untuk validasi pembuatan tiket baru.
 *
 * KEUNTUNGAN FORM REQUEST:
 * 1. Controller lebih bersih
 * 2. Validation rules bisa di-reuse
 * 3. Mudah di-test secara terpisah
 * 4. Single Responsibility Principle
 *
 * Materi Minggu 3 - Hari 2: Input Validation
 */
class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Return true = semua user bisa akses
     * Return false = throw 403 Forbidden
     *
     * ⚠️ TEMPORARY: Return true karena auth belum diimplementasi (Minggu 4)
     * TODO: Tambahkan authorization check di Minggu 4
     */
    public function authorize(): bool
    {
        // User harus authenticated untuk membuat ticket
        return $this->user() !== null;
    }

    /**
     * Prepare data sebelum validasi.
     *
     * Berguna untuk:
     * - Sanitasi input (trim whitespace)
     * - Normalisasi data (lowercase email)
     * - Set default values
     *
     * Method ini dipanggil SEBELUM rules() dijalankan.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Trim whitespace dari title dan description
            'title' => $this->title ? trim($this->title) : null,
            'description' => $this->description ? trim($this->description) : null,

            // Set default priority jika tidak ada
            'priority' => $this->priority ?? 'medium',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * PENJELASAN RULES:
     *
     * required      → Field wajib diisi
     * string        → Harus berupa string
     * min:n         → Minimal n karakter
     * max:n         → Maksimal n karakter
     * in:a,b,c      → Harus salah satu dari nilai yang ditentukan
     * nullable      → Boleh kosong/null
     */
    public function rules(): array
    {
        return [
            // Title: wajib, string, 5-255 karakter
            'title' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],

            // Description: wajib, string, minimal 20 karakter (biar jelas)
            'description' => [
                'required',
                'string',
                'min:20',
            ],

            // Priority: wajib, harus salah satu dari low/medium/high
            // Menggunakan 'in' rule = WHITELIST approach (lebih aman!)
            'priority' => [
                'required',
                'in:low,medium,high',
            ],

            // Category: opsional, jika diisi harus string max 100
            'category' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom error messages untuk validation errors.
     *
     * Format: 'field.rule' => 'pesan error'
     *
     * Ini penting untuk UX yang baik - pesan dalam Bahasa Indonesia
     * yang mudah dipahami user.
     */
    public function messages(): array
    {
        return [
            // Title messages
            'title.required' => 'Judul tiket wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.min' => 'Judul minimal :min karakter.',
            'title.max' => 'Judul maksimal :max karakter.',

            // Description messages
            'description.required' => 'Deskripsi tiket wajib diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.min' => 'Deskripsi minimal :min karakter agar permasalahan jelas.',

            // Priority messages
            'priority.required' => 'Silakan pilih prioritas tiket.',
            'priority.in' => 'Prioritas yang dipilih tidak valid. Pilih: Low, Medium, atau High.',

            // Category messages
            'category.string' => 'Kategori harus berupa teks.',
            'category.max' => 'Kategori maksimal :max karakter.',
        ];
    }

    /**
     * Get custom attribute names untuk error messages.
     *
     * Laravel akan mengganti :attribute placeholder dengan nama ini.
     * Hasilnya: "The judul tiket field is required."
     * Bukan: "The title field is required."
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul tiket',
            'description' => 'deskripsi',
            'priority' => 'prioritas',
            'category' => 'kategori',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * Method ini dipanggil SETELAH validasi berhasil.
     * Bisa digunakan untuk sanitasi tambahan.
     */
    protected function passedValidation(): void
    {
        // Contoh: strip HTML tags dari description untuk keamanan tambahan
        // (Defense in depth - meskipun Blade sudah auto-escape)
        $this->merge([
            'description' => strip_tags($this->description),
        ]);
    }
}
