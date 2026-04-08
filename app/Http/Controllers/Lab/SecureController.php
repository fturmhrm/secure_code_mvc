<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
 * SecureController - CONTOH KODE AMAN
 *
 * ✅ GUNAKAN POLA INI DI PRODUCTION!
 *
 * Controller ini mendemonstrasikan cara yang BENAR untuk
 * mencegah IDOR dengan Laravel Policy.
 *
 * OWASP A01:2025 - Broken Access Control (PENCEGAHAN)
 *
 * SOLUSI:
 * - Menggunakan Policy untuk authorization
 * - authorizeResource() untuk otomatis check semua CRUD action
 * - Query scoping untuk filter data berdasarkan role
 */
class SecureController extends Controller
{
    use AuthorizesRequests;

    /**
     * Constructor - Register policy untuk semua resource actions
     *
     * ✅ authorizeResource() akan otomatis memanggil:
     * - viewAny() untuk index
     * - view() untuk show
     * - create() untuk create/store
     * - update() untuk edit/update
     * - delete() untuk destroy
     */
    public function __construct()
    {
        // ✅ SECURE: Otomatis authorization untuk semua action
        $this->authorizeResource(Ticket::class, 'ticket');
    }

    /**
     * Tampilkan tickets (SECURE)
     *
     * ✅ SOLUSI: Filter berdasarkan role
     * - Admin: lihat semua
     * - User biasa: hanya milik sendiri
     */
    public function index()
    {
        $user = auth()->user();

        // ✅ SECURE: Query berdasarkan role
        if ($user->isAdmin()) {
            // Admin bisa lihat semua
            $tickets = Ticket::with('user')->latest()->get();
        } else {
            // User biasa hanya lihat milik sendiri
            $tickets = $user->tickets()->with('user')->latest()->get();
        }

        return view('bac-lab.secure.tickets.index', compact('tickets'));
    }

    /**
     * Tampilkan detail ticket (SECURE - Dilindungi Policy)
     *
     * ✅ SOLUSI:
     * - Route model binding otomatis resolve Ticket
     * - Policy view() dipanggil otomatis via authorizeResource
     */
    public function show(Ticket $ticket)
    {
        // ✅ Authorization sudah di-handle oleh authorizeResource
        // Jika user tidak berhak, akan otomatis 403 Forbidden

        $ticket->load('user');

        return view('bac-lab.secure.tickets.show', compact('ticket'));
    }

    /**
     * Form edit ticket (SECURE - Dilindungi Policy)
     */
    public function edit(Ticket $ticket)
    {
        // ✅ Authorization via Policy update() - otomatis

        return view('bac-lab.secure.tickets.edit', compact('ticket'));
    }

    /**
     * Update ticket (SECURE - Dilindungi Policy)
     *
     * ✅ SOLUSI: Policy + Validation
     */
    public function update(Request $request, Ticket $ticket)
    {
        // ✅ Authorization via Policy update() - otomatis

        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        // ✅ Status hanya bisa diubah admin/staff
        if (auth()->user()->hasAnyRole(['admin', 'staff'])) {
            $statusValidation = $request->validate([
                'status' => 'sometimes|in:open,in_progress,resolved,closed',
            ]);
            $validated = array_merge($validated, $statusValidation);
        }

        $ticket->update($validated);

        return redirect()
            ->route('bac-lab.secure.tickets.show', $ticket)
            ->with('success', 'Ticket berhasil diupdate! (SECURE ✅)');
    }

    /**
     * Hapus ticket (SECURE - Dilindungi Policy)
     *
     * ✅ SOLUSI: Hanya admin yang bisa hapus (via Policy)
     */
    public function destroy(Ticket $ticket)
    {
        // ✅ Authorization via Policy delete() - otomatis
        // Hanya admin yang bisa delete (didefinisikan di Policy)

        $ticket->delete();

        return redirect()
            ->route('bac-lab.secure.tickets.index')
            ->with('success', 'Ticket berhasil dihapus! (SECURE ✅)');
    }
}
