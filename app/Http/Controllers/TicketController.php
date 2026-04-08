<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * TicketController
 *
 * Controller untuk mengelola tiket dengan Input Validation.
 *
 * PERBEDAAN DENGAN VALIDASI DI CONTROLLER:
 *
 * ❌ SEBELUM (Validasi inline - kotor):
 * public function store(Request $request) {
 *     $validated = $request->validate([
 *         'title' => 'required|string|max:255',
 *         'description' => 'required|string|min:20',
 *         // ... 10+ rules lainnya
 *     ], [
 *         'title.required' => 'Judul wajib diisi.',
 *         // ... 20+ custom messages
 *     ]);
 *     // Controller jadi panjang dan sulit dibaca
 * }
 *
 * ✅ SESUDAH (Form Request - bersih):
 * public function store(StoreTicketRequest $request) {
 *     Ticket::create($request->validated());
 *     // Controller singkat dan fokus pada logic
 * }
 *
 * Materi Minggu 3 - Hari 2: Input Validation
 */
class TicketController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of tickets.
     *
     * MINGGU 4 HARI 2 - AUTHORIZATION LOGIC:
     * - Admin/Staff: Lihat semua tickets
     * - User: Lihat tickets sendiri saja
     */
    public function index(Request $request): View
    {
        // Authorization: viewAny policy check
        $this->authorize('viewAny', Ticket::class);

        $user = $request->user();

        // Query builder dengan eager loading (Minggu 2)
        $query = Ticket::with(['user', 'assignee']);

        // AUTHORIZATION: Filter berdasarkan role (Minggu 4 Hari 2)
        if ($user->isUser()) {
            // User biasa hanya lihat ticket sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->isStaff()) {
            // Staff bisa lihat semua, tapi highlight assigned
            $query->orderByRaw('CASE WHEN assigned_to = ? THEN 0 ELSE 1 END', [$user->id]);
        }
        // Admin: tidak ada filter (lihat semua)

        // Filter by status (optional)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create(): View
    {
        $this->authorize('create', Ticket::class);

        return view('tickets.create');
    }

    /**
     * Store a newly created ticket in storage.
     *
     * ✅ MENGGUNAKAN FORM REQUEST: StoreTicketRequest
     *
     * FLOW:
     * 1. Request masuk
     * 2. StoreTicketRequest OTOMATIS dipanggil
     * 3. authorize() dicek → jika false, throw 403
     * 4. prepareForValidation() dijalankan → trim, sanitize
     * 5. rules() divalidasi → jika gagal, redirect back dengan errors
     * 6. passedValidation() dijalankan → sanitasi tambahan
     * 7. Jika SEMUA OK, baru masuk ke method ini
     * 8. $request->validated() berisi data yang sudah bersih & valid
     *
     * @param  StoreTicketRequest  $request  ← Ganti Request dengan StoreTicketRequest
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        // ✅ Validasi sudah OTOMATIS terjadi sebelum sampai di sini!
        $validatedData = $request->validated();

        // ✅ FIXED: Set user_id dari authenticated user (Minggu 4)
        // Bukan lagi hardcode user_id = 1 seperti di Minggu 3
        $validatedData['user_id'] = $request->user()->id;

        // Set default status untuk tiket baru
        $validatedData['status'] = 'open';

        // Simpan ke database
        $ticket = Ticket::create($validatedData);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil dibuat!');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        // Load relationships
        $ticket->load(['user', 'assignee']);

        // Staff list untuk admin assign (Minggu 4 Hari 2)
        $staffList = [];
        if (Gate::allows('assign-tickets')) {
            $staffList = User::whereIn('role', ['staff', 'admin'])->get();
        }

        return view('tickets.show', compact('ticket', 'staffList'));
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified ticket in storage.
     *
     * ✅ MENGGUNAKAN FORM REQUEST: UpdateTicketRequest
     *
     * PERBEDAAN DENGAN STORE:
     * - UpdateTicketRequest punya field 'status' tambahan
     * - Authorization bisa dicek (ownership) - nanti di Minggu 4
     *
     * @param  UpdateTicketRequest  $request  ← Ganti Request dengan UpdateTicketRequest
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        // ✅ Validasi sudah OTOMATIS terjadi!
        // UpdateTicketRequest memvalidasi: title, description, status, priority

        // Update tiket dengan data yang sudah valid
        $ticket->update($request->validated());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil diperbarui!');
    }

    /**
     * Remove the specified ticket from storage.
     *
     * Untuk delete, kita tidak perlu Form Request karena:
     * - Tidak ada input yang perlu divalidasi
     * - Authorization bisa dicek di middleware atau policy (Minggu 4)
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()
            ->route('tickets.index')
            ->with('success', 'Tiket berhasil dihapus!');
    }

    /**
     * Update ticket status (Quick action)
     *
     * MINGGU 4 HARI 2: Custom policy method
     * Memerlukan changeStatus policy check
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        // Manual authorization untuk custom policy method
        $this->authorize('changeStatus', $ticket);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update($validated);

        return back()->with('success', 'Status ticket berhasil diupdate!');
    }

    /**
     * Assign ticket to staff member
     *
     * MINGGU 4 HARI 2: Admin only action via Gate
     */
    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        // Check gate for assign permission
        $this->authorize('assign', $ticket);

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update($validated);

        $message = $validated['assigned_to']
            ? 'Ticket berhasil di-assign!'
            : 'Assignment ticket berhasil dihapus!';

        return back()->with('success', $message);
    }
}
