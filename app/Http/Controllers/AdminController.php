<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
 * AdminController - Controller untuk halaman admin
 *
 * SECURITY:
 * - Semua method dilindungi oleh Gate 'access-admin'
 * - Hanya user dengan role 'admin' yang bisa akses
 */
class AdminController extends Controller
{
    use AuthorizesRequests;

    /**
     * Admin Dashboard
     * Menampilkan overview statistik sistem
     */
    public function dashboard()
    {
        // Authorize menggunakan Gate
        $this->authorize('access-admin');

        // Statistik untuk dashboard
        $stats = [
            'total_users' => User::count(),
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            'closed_tickets' => Ticket::where('status', 'closed')->count(),
            'unassigned_tickets' => Ticket::whereNull('assigned_to')->count(),
        ];

        // Recent tickets
        $recentTickets = Ticket::with(['user', 'assignee'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTickets'));
    }

    /**
     * Manage Users
     * Menampilkan daftar semua users
     */
    public function users()
    {
        $this->authorize('manage-users');

        $users = User::withCount('tickets')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users', compact('users'));
    }

    /**
     * All Tickets (Admin View)
     * Menampilkan semua tickets dengan filter
     */
    public function allTickets(Request $request)
    {
        $this->authorize('access-admin');

        $query = Ticket::with(['user', 'assignee']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter unassigned
        if ($request->boolean('unassigned')) {
            $query->whereNull('assigned_to');
        }

        $tickets = $query->latest()->paginate(10);

        // Staff list untuk assign dropdown
        $staffList = User::where('role', 'staff')->get();

        return view('admin.tickets', compact('tickets', 'staffList'));
    }

    /**
     * Assign ticket ke staff
     */
    public function assignTicket(Request $request, Ticket $ticket)
    {
        $this->authorize('assign-tickets');

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Pastikan yang di-assign adalah staff
        $staff = User::findOrFail($request->assigned_to);
        if (! $staff->isStaff() && ! $staff->isAdmin()) {
            return back()->with('error', 'Hanya staff atau admin yang bisa di-assign.');
        }

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress', // Auto update status
        ]);

        return back()->with('success', 'Ticket berhasil di-assign ke '.$staff->name);
    }

    /**
     * Reports - Statistics & Analytics
     * Accessible by admin and staff
     *
     * Note: Authorization checked by 'role:admin,staff' middleware in routes/web.php
     * No need for additional Gate check here since middleware already verified role
     */
    public function reports()
    {
        // Middleware 'role:admin,staff' already handles authorization
        // Gate check removed to avoid double-check overhead

        // Ticket statistics
        $ticketStats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        // Priority breakdown
        $priorityStats = [
            'high' => Ticket::where('priority', 'high')->count(),
            'medium' => Ticket::where('priority', 'medium')->count(),
            'low' => Ticket::where('priority', 'low')->count(),
        ];

        // User statistics (admin only)
        $userStats = null;
        if (auth()->user()->isAdmin()) {
            $userStats = [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'staff' => User::where('role', 'staff')->count(),
                'users' => User::where('role', 'user')->count(),
            ];
        }

        // Staff performance (tickets resolved)
        $staffPerformance = User::whereIn('role', ['admin', 'staff'])
            ->withCount(['assignedTickets as resolved_count' => function ($query) {
                $query->whereIn('status', ['resolved', 'closed']);
            }])
            ->withCount('assignedTickets as total_assigned')
            ->get();

        return view('admin.reports', compact(
            'ticketStats',
            'priorityStats',
            'userStats',
            'staffPerformance'
        ));
    }
}
