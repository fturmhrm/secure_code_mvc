<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model SqliLabUser
 *
 * PERINGATAN: Model ini SENGAJA menyimpan password plaintext
 * untuk keperluan demo SQL Injection login bypass.
 *
 * Di production, SELALU:
 * 1. Extend Illuminate\Foundation\Auth\User
 * 2. Gunakan Hash::make() untuk password
 * 3. Gunakan Auth::attempt() untuk login
 */
class SqliLabUser extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'sqli_lab_users';

    /**
     * Kolom yang bisa di-fill
     */
    protected $fillable = [
        'username',
        'password', // Plaintext untuk demo - JANGAN lakukan ini di production!
        'email',
        'role',
    ];

    /**
     * Kolom yang disembunyikan dari array/JSON
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Scope untuk cari berdasarkan role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk admin
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Check apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * SECURE: Verifikasi password dengan Eloquent
     *
     * CATATAN: Di production, gunakan Hash::check()!
     * Ini hanya untuk demo plaintext password SQLi.
     */
    public static function authenticate($username, $password)
    {
        // SECURE: Menggunakan Eloquent (parameterized)
        return static::where('username', $username)
            ->where('password', $password)
            ->first();
    }
}
