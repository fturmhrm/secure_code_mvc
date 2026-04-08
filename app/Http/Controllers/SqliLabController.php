<?php

namespace App\Http\Controllers;

use App\Models\SqliLabProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * SqliLabController
 *
 * Controller untuk demo Lab SQL Injection Prevention.
 * Mendemonstrasikan:
 * - Bagaimana SQL Injection bekerja
 * - Berbagai jenis serangan SQLi
 * - Cara mencegah SQLi dengan Eloquent dan Parameter Binding
 *
 * ⚠️ WARNING: Kode vulnerable di sini HANYA untuk pembelajaran!
 * JANGAN gunakan pattern vulnerable di production!
 *
 * Materi Minggu 3 - Hari 4: SQL Injection Prevention
 */
class SqliLabController extends Controller
{
    /**
     * Index - Menu lab SQL Injection
     */
    public function index(): View
    {
        return view('sqli-lab.index');
    }

    /**
     * Penjelasan cara kerja SQL Injection
     */
    public function howItWorks(): View
    {
        return view('sqli-lab.how-it-works');
    }

    /**
     * SQLi Cheatsheet
     */
    public function cheatsheet(): View
    {
        return view('sqli-lab.cheatsheet');
    }

    // ================================================================
    // VULNERABLE SEARCH DEMO
    // ⚠️ JANGAN GUNAKAN PATTERN INI DI PRODUCTION!
    // ================================================================

    /**
     * Halaman search vulnerable
     */
    public function vulnerableSearch(Request $request): View
    {
        $search = $request->input('search', '');
        $products = [];
        $query = '';
        $error = null;

        if ($search) {
            // ❌ VULNERABLE: String concatenation langsung!
            // Input user langsung dimasukkan ke query tanpa sanitasi
            $query = "SELECT * FROM sqli_lab_products WHERE name LIKE '%{$search}%' OR description LIKE '%{$search}%'";

            try {
                $products = DB::select($query);
            } catch (\Exception $e) {
                // Di production, JANGAN tampilkan error SQL!
                // Tapi untuk demo, kita tampilkan agar peserta belajar
                $error = $e->getMessage();
            }
        }

        return view('sqli-lab.vulnerable-search', [
            'search' => $search,
            'products' => $products,
            'query' => $query,
            'error' => $error,
        ]);
    }

    // ================================================================
    // VULNERABLE LOGIN DEMO
    // ⚠️ JANGAN GUNAKAN PATTERN INI DI PRODUCTION!
    // ================================================================

    /**
     * Halaman login vulnerable
     */
    public function vulnerableLogin(): View
    {
        return view('sqli-lab.vulnerable-login', [
            'success' => false,
            'user' => null,
            'error' => null,
            'query' => null,
            'inputUsername' => '',
            'inputPassword' => '',
        ]);
    }

    /**
     * Proses login vulnerable
     */
    public function vulnerableLoginSubmit(Request $request): View
    {
        $username = $request->input('username', '');
        $password = $request->input('password', '');

        // ❌ VULNERABLE: String concatenation langsung!
        $query = "SELECT * FROM sqli_lab_users WHERE username = '{$username}' AND password = '{$password}'";

        $success = false;
        $users = [];  // Hasil query mentah - bisa 0, 1, atau banyak row
        $error = null;
        $isBypass = false;

        try {
            // Eksekusi query vulnerable
            $users = DB::select($query);

            if (count($users) > 0) {
                $success = true;

                // Detect if this is a bypass (contains SQL injection characters)
                $isBypass = str_contains($username, "'") ||
                           str_contains($username, '--') ||
                           str_contains($username, '#') ||
                           stripos($username, 'UNION') !== false ||
                           stripos($username, ' OR ') !== false;
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        // Return hasil query apa adanya - view akan handle tampilannya
        return view('sqli-lab.vulnerable-login', [
            'success' => $success,
            'users' => $users,  // Array hasil query (bukan single user)
            'user' => count($users) > 0 ? $users[0] : null,  // User pertama untuk backward compat
            'error' => $error,
            'query' => $query,
            'inputUsername' => $username,
            'inputPassword' => $password,
            'isBypass' => $isBypass,
        ]);
    }

    // ================================================================
    // SECURE SEARCH DEMO
    // ✅ Pattern yang benar untuk digunakan di production
    // ================================================================

    /**
     * Halaman search secure
     */
    public function secureSearch(Request $request): View
    {
        $search = $request->input('search', '');
        $method = $request->input('method', 'eloquent');
        $results = [];

        if ($search) {
            switch ($method) {
                case 'eloquent':
                    // ✅ SECURE: Eloquent ORM
                    $results = SqliLabProduct::where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->get()
                        ->toArray();
                    break;

                case 'query_builder':
                    // ✅ SECURE: Query Builder
                    $results = DB::table('sqli_lab_products')
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->get()
                        ->toArray();
                    break;

                case 'param_binding':
                    // ✅ SECURE: Raw query dengan parameter binding
                    $results = DB::select(
                        'SELECT * FROM sqli_lab_products WHERE name LIKE ? OR description LIKE ?',
                        ['%'.$search.'%', '%'.$search.'%']
                    );
                    break;

                case 'named_binding':
                    // ✅ SECURE: Named parameter binding
                    $results = DB::select(
                        'SELECT * FROM sqli_lab_products WHERE name LIKE :search1 OR description LIKE :search2',
                        ['search1' => '%'.$search.'%', 'search2' => '%'.$search.'%']
                    );
                    break;
            }
        }

        return view('sqli-lab.secure-search', [
            'search' => $search,
            'method' => $method,
            'products' => $results,
        ]);
    }

    // ================================================================
    // UTILITY ENDPOINTS
    // ================================================================

    /**
     * Seed sample data untuk demo
     */
    public function seedData()
    {
        // Check if tables exist
        if (! DB::getSchemaBuilder()->hasTable('sqli_lab_products')) {
            return redirect()->route('sqli-lab.index')
                ->with('error', 'Tabel sqli_lab_products belum ada. Jalankan migration terlebih dahulu.');
        }

        // Clear existing data
        DB::table('sqli_lab_products')->truncate();
        DB::table('sqli_lab_users')->truncate();

        // Seed products
        $products = [
            ['name' => 'Laptop ASUS ROG', 'description' => 'Gaming laptop dengan RTX 4080', 'price' => 25000000, 'stock' => 10],
            ['name' => 'iPhone 15 Pro', 'description' => 'Smartphone Apple terbaru dengan chip A17', 'price' => 19000000, 'stock' => 25],
            ['name' => 'Samsung Galaxy S24', 'description' => 'Android flagship dengan AI features', 'price' => 15000000, 'stock' => 30],
            ['name' => 'MacBook Air M3', 'description' => 'Ultrabook tipis dengan chip Apple M3', 'price' => 18000000, 'stock' => 15],
            ['name' => 'Mechanical Keyboard', 'description' => 'Keyboard gaming RGB dengan switch Cherry MX', 'price' => 1500000, 'stock' => 50],
            ['name' => 'Gaming Mouse Logitech', 'description' => 'Mouse wireless dengan sensor HERO 25K', 'price' => 1200000, 'stock' => 40],
            ['name' => 'Monitor 4K 27 inch', 'description' => 'Monitor IPS 4K untuk productivity dan gaming', 'price' => 5000000, 'stock' => 20],
            ['name' => 'SSD NVMe 1TB', 'description' => 'Storage super cepat untuk OS dan aplikasi', 'price' => 1500000, 'stock' => 100],
        ];

        foreach ($products as $product) {
            DB::table('sqli_lab_products')->insert(array_merge($product, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed users (untuk demo login bypass)
        // ⚠️ Password plain text hanya untuk demo! Di production gunakan Hash::make()
        $users = [
            ['username' => 'admin', 'password' => 'supersecret123', 'email' => 'admin@example.com', 'role' => 'admin'],
            ['username' => 'user1', 'password' => 'password123', 'email' => 'user1@example.com', 'role' => 'user'],
            ['username' => 'manager', 'password' => 'manager456', 'email' => 'manager@example.com', 'role' => 'manager'],
        ];

        foreach ($users as $user) {
            DB::table('sqli_lab_users')->insert(array_merge($user, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        return redirect()->route('sqli-lab.index')
            ->with('success', 'Sample data berhasil di-seed! 8 products dan 3 users ditambahkan.');
    }

    /**
     * Reset demo data
     */
    public function resetData()
    {
        if (DB::getSchemaBuilder()->hasTable('sqli_lab_products')) {
            DB::table('sqli_lab_products')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('sqli_lab_users')) {
            DB::table('sqli_lab_users')->truncate();
        }

        return redirect()->route('sqli-lab.index')
            ->with('success', 'Demo data berhasil direset!');
    }

    // ================================================================
    // BLIND SQL INJECTION DEMO
    // ⚠️ JANGAN GUNAKAN PATTERN INI DI PRODUCTION!
    // ================================================================

    /**
     * Halaman Blind SQLi Demo
     */
    public function blindSqli(): View
    {
        return view('sqli-lab.blind-sqli');
    }

    /**
     * Boolean-based Blind SQLi - Check if user exists
     * Hanya return true/false, tidak ada data detail
     */
    public function blindSqliBooleanCheck(Request $request)
    {
        $username = $request->input('username', '');
        $startTime = microtime(true);

        // ❌ VULNERABLE: String concatenation!
        $query = "SELECT COUNT(*) as count FROM sqli_lab_users WHERE username = '{$username}'";

        $exists = false;
        $error = null;

        try {
            $result = DB::select($query);
            $exists = $result[0]->count > 0;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        // Return JSON untuk AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'exists' => $exists,
                'query' => $query,
                'executionTime' => $executionTime,
                'error' => $error,
            ]);
        }

        return view('sqli-lab.blind-sqli', [
            'booleanResult' => [
                'username' => $username,
                'exists' => $exists,
                'query' => $query,
                'executionTime' => $executionTime,
                'error' => $error,
            ],
        ]);
    }

    /**
     * Time-based Blind SQLi - Vulnerable endpoint
     * Query structure memungkinkan pg_sleep dalam single statement
     */
    public function blindSqliTimeCheck(Request $request)
    {
        $id = $request->input('id', '1');
        $startTime = microtime(true);

        // ❌ VULNERABLE: String concatenation dengan ID!
        // pg_sleep() bisa diinjeksi sebagai bagian dari WHERE clause
        // pg_sleep() returns void, dan "void IS NULL" = true di PostgreSQL
        $query = "SELECT name FROM sqli_lab_products WHERE id = {$id}";

        $productName = null;
        $error = null;

        try {
            $result = DB::select($query);
            $productName = $result[0]->name ?? null;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'productName' => $productName,
                'query' => $query,
                'executionTime' => $executionTime,
                'error' => $error,
            ]);
        }

        return view('sqli-lab.blind-sqli', [
            'timeResult' => [
                'id' => $id,
                'productName' => $productName,
                'query' => $query,
                'executionTime' => $executionTime,
                'error' => $error,
            ],
        ]);
    }
}
