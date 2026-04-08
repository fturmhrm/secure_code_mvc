<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CsrfLabController
 *
 * Controller untuk demo Lab CSRF Protection.
 * Mendemonstrasikan:
 * - Bagaimana CSRF attack bekerja
 * - Bagaimana CSRF token melindungi aplikasi
 * - Implementasi CSRF untuk form dan AJAX
 *
 * Materi Minggu 3 - Hari 3: CSRF Protection
 */
class CsrfLabController extends Controller
{
    /**
     * Index - Menu lab CSRF
     */
    public function index(): View
    {
        return view('csrf-lab.index');
    }

    /**
     * Penjelasan cara kerja CSRF
     */
    public function howItWorks(): View
    {
        return view('csrf-lab.how-it-works');
    }

    /**
     * Demo serangan CSRF
     */
    public function attackDemo(): View
    {
        return view('csrf-lab.attack-demo', [
            'transfers' => session('csrf_transfers', []),
            'balance' => session('csrf_balance', 10000000), // Rp 10 juta
        ]);
    }

    /**
     * Demo CSRF protection
     */
    public function protectionDemo(): View
    {
        return view('csrf-lab.protection-demo', [
            'actions' => session('csrf_protected_actions', []),
        ]);
    }

    /**
     * Demo CSRF untuk AJAX
     */
    public function ajaxDemo(): View
    {
        return view('csrf-lab.ajax-demo', [
            'ajaxResults' => session('csrf_ajax_results', []),
        ]);
    }

    // ================================================================
    // VULNERABLE ENDPOINTS (untuk demo serangan)
    // ⚠️ JANGAN GUNAKAN PATTERN INI DI PRODUCTION!
    // ================================================================

    /**
     * Transfer uang - VULNERABLE (tanpa CSRF check)
     *
     * ❌ VULNERABLE: Endpoint ini tidak memvalidasi CSRF token
     * Attacker bisa membuat form di website lain yang submit ke sini
     *
     * CATATAN: Di Laravel, ini akan tetap gagal karena middleware
     * VerifyCsrfToken aktif. Untuk demo, kita exclude route ini.
     */
    public function vulnerableTransfer(Request $request): RedirectResponse
    {
        // ❌ TIDAK ADA VALIDASI CSRF TOKEN!
        // Request langsung diproses

        $amount = (int) $request->input('amount', 0);
        $to = $request->input('to', 'Unknown');
        $source = $request->input('source', 'form');

        // Ambil balance
        $balance = session('csrf_balance', 10000000);

        // Proses transfer (tanpa validasi keamanan!)
        if ($amount > 0 && $amount <= $balance) {
            $balance -= $amount;
            session(['csrf_balance' => $balance]);

            // Log transfer
            $transfers = session('csrf_transfers', []);
            $transfers[] = [
                'to' => $to,
                'amount' => $amount,
                'balance_after' => $balance,
                'source' => $source,
                'time' => now()->format('H:i:s'),
                'status' => 'success',
                'warning' => 'Transfer TANPA validasi CSRF!',
            ];
            session(['csrf_transfers' => $transfers]);

            return redirect()
                ->route('csrf-lab.attack-demo')
                ->with('danger', "⚠️ Transfer Rp " . number_format($amount) . " ke {$to} BERHASIL tanpa validasi CSRF!");
        }

        return redirect()
            ->route('csrf-lab.attack-demo')
            ->with('error', 'Transfer gagal: saldo tidak cukup atau amount invalid.');
    }

    /**
     * Protected Transfer - DENGAN CSRF middleware aktif
     * Route ini TIDAK di-exclude dari middleware, jadi akan return 419
     * jika request tidak memiliki CSRF token
     *
     * Digunakan untuk demonstrasi bahwa CSRF protection BEKERJA
     */
    public function protectedTransfer(Request $request): RedirectResponse
    {
        // Jika sampai di sini, berarti CSRF token valid
        // (middleware sudah memvalidasi sebelum masuk controller)

        $amount = (int) $request->input('amount', 0);
        $to = $request->input('to', 'Unknown');

        $balance = session('csrf_balance', 10000000);

        if ($amount > 0 && $amount <= $balance) {
            $balance -= $amount;
            session(['csrf_balance' => $balance]);

            $transfers = session('csrf_transfers', []);
            $transfers[] = [
                'to' => $to,
                'amount' => $amount,
                'balance_after' => $balance,
                'source' => 'protected_endpoint',
                'time' => now()->format('H:i:s'),
                'status' => 'success',
                'info' => 'Transfer via protected endpoint (CSRF valid)',
            ];
            session(['csrf_transfers' => $transfers]);

            return redirect()
                ->route('csrf-lab.attack-demo')
                ->with('success', "Transfer Rp " . number_format($amount) . " berhasil!");
        }

        return redirect()
            ->route('csrf-lab.attack-demo')
            ->with('error', 'Transfer gagal.');
    }

    // ================================================================
    // SECURE ENDPOINTS (dengan CSRF protection)
    // ================================================================

    /**
     * Transfer uang - SECURE (dengan CSRF check)
     *
     * ✅ SECURE: Laravel middleware VerifyCsrfToken akan memvalidasi
     * token sebelum request sampai di controller ini.
     *
     * Jika token tidak valid → 419 Page Expired
     */
    public function secureTransfer(Request $request): RedirectResponse
    {
        // ✅ CSRF token sudah divalidasi oleh middleware!
        // Kita bisa yakin request ini legitimate

        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'to' => 'required|string|max:100',
        ]);

        $amount = $validated['amount'];
        $to = $validated['to'];

        // Ambil balance
        $balance = session('csrf_balance', 10000000);

        if ($amount <= $balance) {
            $balance -= $amount;
            session(['csrf_balance' => $balance]);

            // Log transfer
            $transfers = session('csrf_transfers', []);
            $transfers[] = [
                'to' => $to,
                'amount' => $amount,
                'balance_after' => $balance,
                'source' => 'secure_form',
                'time' => now()->format('H:i:s'),
                'status' => 'success',
                'info' => 'Transfer DENGAN validasi CSRF ✓',
            ];
            session(['csrf_transfers' => $transfers]);

            return redirect()
                ->route('csrf-lab.attack-demo')
                ->with('success', "✅ Transfer Rp " . number_format($amount) . " ke {$to} berhasil dengan CSRF protection!");
        }

        return redirect()
            ->route('csrf-lab.attack-demo')
            ->with('error', 'Transfer gagal: saldo tidak cukup.');
    }

    /**
     * Protected action - untuk demo protection
     */
    public function protectedAction(Request $request): RedirectResponse
    {
        // ✅ CSRF sudah divalidasi oleh middleware

        $validated = $request->validate([
            'action_type' => 'required|in:update_profile,change_settings,delete_data',
            'description' => 'nullable|string|max:255',
        ]);

        $actions = session('csrf_protected_actions', []);
        $actions[] = [
            'type' => $validated['action_type'],
            'description' => $validated['description'] ?? '-',
            'time' => now()->format('H:i:s'),
            'status' => 'success',
            'csrf_valid' => true,
        ];
        session(['csrf_protected_actions' => $actions]);

        return redirect()
            ->route('csrf-lab.protection-demo')
            ->with('success', 'Aksi berhasil dijalankan dengan CSRF protection!');
    }

    /**
     * AJAX endpoint - dengan CSRF protection
     */
    public function ajaxAction(Request $request)
    {
        // ✅ CSRF token divalidasi via header X-CSRF-TOKEN

        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $results = session('csrf_ajax_results', []);
        $results[] = [
            'message' => $validated['message'],
            'time' => now()->format('H:i:s'),
            'method' => 'AJAX with CSRF',
        ];
        session(['csrf_ajax_results' => $results]);

        return response()->json([
            'success' => true,
            'message' => 'Request berhasil! CSRF token valid.',
            'data' => $validated['message'],
            'time' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Reset demo data
     */
    public function resetDemo(): RedirectResponse
    {
        session()->forget([
            'csrf_transfers',
            'csrf_balance',
            'csrf_protected_actions',
            'csrf_ajax_results',
        ]);

        return redirect()
            ->route('csrf-lab.index')
            ->with('success', 'Demo data berhasil direset!');
    }
}
