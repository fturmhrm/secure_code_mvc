<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model LoginAttempt
 * 
 * Untuk tracking login attempts (secure vs vulnerable)
 * Berguna untuk comparison dan audit
 */
class LoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'successful',
        'type',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeSecure($query)
    {
        return $query->where('type', 'secure');
    }

    public function scopeVulnerable($query)
    {
        return $query->where('type', 'vulnerable');
    }

    /**
     * Scope untuk failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    /**
     * Hitung attempts dalam periode tertentu
     */
    public static function countRecentAttempts($email, $type, $minutes = 1)
    {
        return static::where('email', $email)
            ->where('type', $type)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
}
