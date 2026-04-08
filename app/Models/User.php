<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User - SECURE IMPLEMENTATION
 *
 * Model ini menggunakan best practices:
 * - Password otomatis di-hash via mutator
 * - Hidden attributes untuk keamanan
 * - Proper casting untuk tipe data
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * SECURITY: Hanya field yang boleh diisi via mass assignment
     * Role ditambahkan untuk RBAC (Minggu 4 Hari 2)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * SECURITY: Password dan remember_token tidak akan muncul
     * saat model di-convert ke array/JSON
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * SECURITY (Minggu 4 Hari 1):
     * 'hashed' memastikan password selalu di-hash saat di-set (Laravel 10+)
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Auto-hash password! (Minggu 4 Hari 1)
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Tickets yang dibuat oleh user ini
     * Penggunaan: $user->tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Tickets yang di-assign ke user ini (untuk staff)
     * Penggunaan: $user->assignedTickets
     */
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Scope untuk mencari user berdasarkan email
     * SECURE: Menggunakan Eloquent (parameterized query)
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}
