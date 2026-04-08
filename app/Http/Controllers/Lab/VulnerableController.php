<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

/**
 * VulnerableController - CONTOH KODE VULNERABLE
 *
 * ⚠️ JANGAN GUNAKAN POLA INI DI PRODUCTION!
 *
 * Controller ini sengaja dibuat TIDAK AMAN untuk demonstrasi
 * IDOR (Insecure Direct Object Reference) vulnerability.
 *
 * OWASP A01:2025 - Broken Access Control
 *
 * MASALAH:
 * - Tidak ada authorization check
 * - User bisa akses data siapa saja dengan mengganti ID di URL
 */
class VulnerableController extends Controller
{
    /**
     * Tampilkan tickets milik user yang login
     *
     * 📝 CATATAN: Index hanya menampilkan ticket MILIK SENDIRI
     * Vulnerability IDOR ada di show() - user bisa ganti ID di URL
     * untuk mengakses ticket orang lain
     */
    public function index()
    {
        // Tampilkan hanya ticket milik user yang login
        // IDOR vulnerability ada di halaman show (URL manipulation)
        $tickets = auth()->user()->tickets()->with('user')->latest()->get();

        return view('bac-lab.vulnerable.tickets.index', compact('tickets'));
    }

    /**
     * Tampilkan detail ticket (VULNERABLE - IDOR!)
     *
     * ❌ MASALAH:
     * - Tidak check apakah ticket milik user yang login
     * - User bisa ganti ID di URL untuk akses data orang lain
     */
    public function show($id)
    {
        // ❌ VULNERABLE: Langsung find tanpa authorization
        // User bisa akses /lab/vulnerable/tickets/1, /2, /3, dst
        $ticket = Ticket::with('user')->findOrFail($id);

        // ❌ Tidak ada check: $ticket->user_id === auth()->id()

        return view('bac-lab.vulnerable.tickets.show', compact('ticket'));
    }

    /**
     * Form edit ticket (VULNERABLE - IDOR!)
     *
     * ❌ MASALAH: Sama seperti show, tidak ada authorization
     */
    public function edit($id)
    {
        // ❌ VULNERABLE: Tidak ada authorization check
        $ticket = Ticket::findOrFail($id);

        return view('bac-lab.vulnerable.tickets.edit', compact('ticket'));
    }

    /**
     * Update ticket (VULNERABLE - IDOR!)
     *
     * ❌ MASALAH: User bisa update ticket siapa saja!
     */
    public function update(Request $request, $id)
    {
        // ❌ VULNERABLE: Validasi ada, tapi TIDAK ADA authorization
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
        ]);

        // ❌ VULNERABLE: Langsung update tanpa check ownership
        $ticket = Ticket::findOrFail($id);
        $ticket->update($validated);

        return redirect()
            ->route('bac-lab.vulnerable.tickets.show', $ticket)
            ->with('success', 'Ticket berhasil diupdate! (VULNERABLE)');
    }

    /**
     * Hapus ticket (VULNERABLE - IDOR!)
     *
     * ❌ MASALAH: User bisa hapus ticket siapa saja!
     */
    public function destroy($id)
    {
        // ❌ VULNERABLE: Langsung delete tanpa authorization
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()
            ->route('bac-lab.vulnerable.tickets.index')
            ->with('success', 'Ticket berhasil dihapus! (VULNERABLE)');
    }
}
