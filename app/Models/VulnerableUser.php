<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model VulnerableUser - VULNERABLE IMPLEMENTATION
 * 
 * ⚠️ PERINGATAN: Model ini SENGAJA TIDAK AMAN untuk pembelajaran!
 * 
 * Vulnerability yang ada:
 * 1. Password disimpan PLAINTEXT (tidak di-hash)
 * 2. Password tidak di-hidden dari serialization
 * 3. Tidak ada casting yang aman
 */
class VulnerableUser extends Model
{
    /**
     * Table name
     */
    protected $table = 'vulnerable_users';

    /**
     * ❌ VULNERABLE: Semua field bisa mass-assigned
     * termasuk field sensitif
     */
    protected $fillable = [
        'name',
        'email',
        'password', // Password akan tersimpan PLAINTEXT!
    ];

    /**
     * ❌ VULNERABLE: Password TIDAK di-hidden
     * Akan muncul saat model di-convert ke JSON
     */
    protected $hidden = [
        // Password seharusnya di-hidden, tapi sengaja tidak!
    ];

    /**
     * ❌ VULNERABLE: Tidak ada 'hashed' casting
     * Password tidak di-hash otomatis
     */
    protected $casts = [
        // Tidak ada password hashing!
    ];

    /**
     * ❌ VULNERABLE: Method untuk verify password plaintext
     * 
     * Di production, harusnya menggunakan Hash::check()
     */
    public function verifyPassword($inputPassword)
    {
        // VULNERABLE: Langsung compare string!
        return $this->password === $inputPassword;
    }

    /**
     * Method untuk menunjukkan password tersimpan plaintext
     * (untuk demo di comparison page)
     */
    public function getStoredPassword()
    {
        return $this->password; // Plaintext!
    }
}
