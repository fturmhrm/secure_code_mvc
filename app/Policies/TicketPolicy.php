<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

/**
 * TicketPolicy - Authorization logic untuk Ticket model
 *
 * INTEGRASI MATERI:
 * - Minggu 4 Hari 2: Policy untuk model-based authorization
 * - Menggunakan helper methods dari Model (isAdmin, isStaff, belongsToUser, isEditable)
 *
 * OWASP A01:2025 - Broken Access Control
 * Policy memastikan user hanya bisa mengakses data yang seharusnya.
 *
 * ROLE PERMISSIONS:
 * - Admin: Full access ke semua tickets (via before() hook)
 * - Staff: View all, edit/changeStatus assigned tickets
 * - User: CRUD own tickets only (dengan pembatasan)
 *
 * CARA PENGGUNAAN DI CONTROLLER:
 * 1. authorizeResource() - otomatis map action ke policy method
 * 2. $this->authorize('update', $ticket) - manual check
 * 3. Gate::allows('update', $ticket) - via Gate facade
 *
 * CARA PENGGUNAAN DI BLADE:
 * 1. @can('update', $ticket) ... @endcan
 * 2. @cannot('delete', $ticket) ... @endcannot
 */
class TicketPolicy
{
    /**
     * Perform pre-authorization checks.
     * Admin dapat melakukan semua aksi.
     *
     * Return null untuk melanjutkan ke method spesifik,
     * Return true untuk allow, false untuk deny.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Admin bypass - bisa melakukan semua aksi
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Lanjut ke method spesifik
    }

    /**
     * Determine whether the user can view any tickets.
     *
     * Admin & Staff: Bisa lihat semua tickets
     * User: Hanya bisa lihat list (filtered di controller)
     */
    public function viewAny(User $user): bool
    {
        // Semua authenticated user bisa akses halaman index
        // Filtering dilakukan di controller berdasarkan role
        return true;
    }

    /**
     * Determine whether the user can view the ticket.
     *
     * Admin: Bisa lihat semua (handled by before())
     * Staff: Bisa lihat semua tickets
     * User: Hanya bisa lihat ticket sendiri
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Staff bisa lihat semua tickets
        if ($user->isStaff()) {
            return true;
        }

        // User biasa hanya bisa lihat ticket sendiri
        return $ticket->belongsToUser($user);
    }

    /**
     * Determine whether the user can create tickets.
     *
     * Semua authenticated user bisa membuat ticket baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the ticket.
     *
     * Admin: Bisa edit semua (handled by before())
     * Staff: Bisa edit jika di-assign ke mereka
     * User: Bisa edit ticket sendiri (jika belum closed)
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Staff bisa edit jika di-assign ke mereka
        if ($user->isStaff()) {
            return $ticket->isAssignedTo($user);
        }

        // User biasa: hanya bisa edit ticket sendiri DAN belum closed
        return $ticket->belongsToUser($user) && $ticket->isEditable();
    }

    /**
     * Determine whether the user can delete the ticket.
     *
     * Admin: Bisa hapus semua (handled by before())
     * Staff: TIDAK bisa hapus
     * User: TIDAK bisa hapus
     *
     * SECURITY: Hanya admin yang boleh menghapus ticket
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Hanya admin yang bisa delete (sudah di-handle before())
        // Staff dan User tidak bisa delete
        return false;
    }

    /**
     * Determine whether the user can restore the ticket (soft delete).
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        // Hanya admin
        return false;
    }

    /**
     * Determine whether the user can permanently delete the ticket.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        // Hanya admin
        return false;
    }

    /**
     * Determine whether the user can assign staff to ticket.
     *
     * Hanya Admin yang bisa assign staff ke ticket.
     */
    public function assign(User $user, Ticket $ticket): bool
    {
        // Hanya admin (sudah di-handle before())
        return false;
    }

    /**
     * Determine whether the user can change ticket status.
     *
     * Admin: Bisa ubah semua status
     * Staff: Bisa ubah status ticket yang di-assign
     * User: TIDAK bisa ubah status
     */
    public function changeStatus(User $user, Ticket $ticket): bool
    {
        // Staff bisa ubah status jika di-assign
        if ($user->isStaff()) {
            return $ticket->isAssignedTo($user);
        }

        return false;
    }
}
