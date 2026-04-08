<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Comment
 * 
 * Digunakan untuk demo Stored XSS
 * Menunjukkan bagaimana data dari user bisa berbahaya
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'author_name',
        'content',
    ];

    /**
     * Relasi: Comment belongs to Ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_name', 'name');
    }
}
