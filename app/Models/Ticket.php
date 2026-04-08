<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Ticket
 *
 * Representasi dari tabel 'tickets' di database
 * Sesuai dengan materi Hari 3 - MVC Laravel
 */
class Ticket extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara mass-assignment
     *
     * PENTING untuk keamanan!
     * Hanya kolom yang didefinisikan di sini yang bisa diisi via create() atau update()
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'assigned_to',  // Ditambah untuk RBAC (Minggu 4 Hari 2)
        'title',
        'description',
        'status',
        'priority',
        'category',
    ];

    /**
     * Casting tipe data otomatis
     *
     * Laravel akan otomatis mengkonversi tipe data saat mengambil/menyimpan
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Nilai default untuk atribut
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'open',
        'priority' => 'medium',
    ];

    /**
     * Relasi: Ticket belongs to User (owner/creator)
     *
     * Setiap tiket dimiliki oleh satu user
     * Penggunaan: $ticket->user->name
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi: Ticket assigned to User (staff)
     * Ditambah untuk RBAC (Minggu 4 Hari 2)
     *
     * Penggunaan: $ticket->assignee->name
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope untuk filter tiket berdasarkan status
     *
     * Penggunaan: Ticket::status('open')->get()
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter tiket berdasarkan prioritas
     *
     * Penggunaan: Ticket::priority('high')->get()
     */
    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: Only open tickets
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Accessor untuk mendapatkan badge class berdasarkan status
     *
     * Penggunaan: $ticket->status_badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-warning',
            'in_progress' => 'bg-info',
            'resolved' => 'bg-success',
            'closed' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Accessor untuk mendapatkan badge class berdasarkan priority
     *
     * Penggunaan: $ticket->priority_badge
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'bg-danger',
            'medium' => 'bg-warning',
            'low' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if ticket is open for editing
     * Ticket yang sudah closed tidak bisa diedit oleh regular user
     */
    public function isEditable(): bool
    {
        return $this->status !== 'closed';
    }

    /**
     * Check if ticket belongs to given user
     */
    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if ticket is assigned to given user
     */
    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
