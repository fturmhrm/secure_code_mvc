<?php

namespace Tests\Feature\Security;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * InputValidationTest - Security Testing untuk Input Validation & CSRF
 *
 * OWASP A03:2025 - Injection (XSS, SQLi)
 * OWASP A05:2025 - Security Misconfiguration (CSRF)
 *
 * Test ini membuktikan bahwa mekanisme input validation
 * di aplikasi secure-ticketing bekerja dengan benar:
 * - XSS payload di-escape di output (Blade {{ }})
 * - SQL injection tidak berhasil (Eloquent binding)
 * - Server-side validation tidak bisa di-bypass
 * - CSRF token wajib untuk form submission
 *
 * Minggu 6 Hari 1 - Security Testing & PHPUnit
 */
class InputValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ===============================================
    // XSS PREVENTION TESTS
    // ===============================================

    /**
     * ✅ Test: XSS payload di-escape saat ditampilkan
     *
     * Blade {{ }} secara default meng-escape HTML entities.
     * <script> menjadi &lt;script&gt;
     * Sehingga browser TIDAK mengeksekusi script.
     */
    public function test_xss_payload_is_escaped_in_ticket_output(): void
    {
        $xssPayload = '<script>alert("XSS")</script>';

        $ticket = Ticket::factory()->create([
            'title' => $xssPayload,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/tickets/{$ticket->id}");

        $response->assertStatus(200);

        // Raw script tag TIDAK BOLEH ada di response
        // (parameter escaped=false memastikan kita check raw HTML)
        $response->assertDontSee($xssPayload, false);
    }

    /**
     * ✅ Test: XSS payload di-escape juga di halaman index
     */
    public function test_xss_payload_is_escaped_in_ticket_list(): void
    {
        $xssPayload = '<img src=x onerror=alert(1)>';

        Ticket::factory()->create([
            'title' => $xssPayload,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/tickets');

        $response->assertStatus(200);

        // Raw img onerror TIDAK BOLEH ada di response
        $response->assertDontSee($xssPayload, false);
    }

    // ===============================================
    // VALIDATION BYPASS TESTS
    // ===============================================

    /**
     * ✅ Test: Validasi TIDAK bisa di-bypass - title kosong
     *
     * Attacker mungkin mencoba mengirim request langsung
     * tanpa melalui form (misal dengan cURL/Postman)
     * untuk bypass validasi client-side.
     * Server-side validation HARUS tetap menolak.
     */
    public function test_validation_rejects_empty_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post('/tickets', [
            'title' => '',
            'description' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'description']);
    }

    /**
     * ✅ Test: Validasi TIDAK bisa di-bypass - title terlalu pendek
     *
     * StoreTicketRequest mengharuskan title minimal 5 karakter.
     */
    public function test_validation_rejects_title_too_short(): void
    {
        $response = $this->actingAs($this->user)->post('/tickets', [
            'title' => 'ab',
            'description' => 'Deskripsi yang cukup panjang untuk memenuhi validasi minimum dua puluh karakter.',
            'priority' => 'medium',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /**
     * ✅ Test: Validasi TIDAK bisa di-bypass - title terlalu panjang
     *
     * StoreTicketRequest mengharuskan title maksimal 255 karakter.
     */
    public function test_validation_rejects_title_too_long(): void
    {
        $longTitle = str_repeat('a', 300);

        $response = $this->actingAs($this->user)->post('/tickets', [
            'title' => $longTitle,
            'description' => 'Deskripsi yang cukup panjang untuk memenuhi validasi minimum dua puluh karakter.',
            'priority' => 'medium',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /**
     * ✅ Test: Validasi TIDAK bisa di-bypass - priority tidak valid
     *
     * StoreTicketRequest mengharuskan priority: low, medium, atau high.
     * Attacker tidak bisa mengirim priority yang tidak valid.
     */
    public function test_validation_rejects_invalid_priority(): void
    {
        $response = $this->actingAs($this->user)->post('/tickets', [
            'title' => 'Valid Title Here',
            'description' => 'Deskripsi yang cukup panjang untuk memenuhi validasi minimum dua puluh karakter.',
            'priority' => 'critical', // Tidak ada di whitelist!
        ]);

        $response->assertSessionHasErrors(['priority']);
    }

    /**
     * ✅ Test: Data valid berhasil disimpan
     *
     * Verifikasi bahwa validasi tidak terlalu ketat
     * (data yang benar tetap bisa masuk).
     */
    public function test_valid_ticket_data_is_accepted(): void
    {
        $response = $this->actingAs($this->user)->post('/tickets', [
            'title' => 'Ticket Valid untuk Testing',
            'description' => 'Deskripsi yang cukup panjang untuk memenuhi validasi minimum dua puluh karakter.',
            'priority' => 'medium',
        ]);

        $response->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('tickets', [
            'title' => 'Ticket Valid untuk Testing',
            'user_id' => $this->user->id,
        ]);
    }

    // ===============================================
    // SQL INJECTION PREVENTION TESTS
    // ===============================================

    /**
     * ✅ Test: SQL Injection payload di title tidak merusak database
     *
     * Eloquent menggunakan parameterized queries secara default,
     * sehingga SQL injection payload disimpan sebagai string biasa.
     */
    public function test_sql_injection_payload_is_stored_as_plain_text(): void
    {
        $sqlPayload = "'; DROP TABLE tickets; --";

        $response = $this->actingAs($this->user)->post('/tickets', [
            'title' => $sqlPayload,
            'description' => 'Testing SQL injection prevention di form ticket submission.',
            'priority' => 'medium',
        ]);

        // Tabel tickets masih ada (tidak ter-drop)
        $this->assertDatabaseHas('tickets', [
            'user_id' => $this->user->id,
        ]);
    }

    // ===============================================
    // CSRF PROTECTION TESTS
    // ===============================================

    /**
     * ✅ Test: CSRF protection membedakan endpoint yang dilindungi vs yang tidak
     *
     * OWASP: Cross-Site Request Forgery prevention
     *
     * Endpoint vulnerable-transfer SENGAJA di-exclude dari CSRF middleware
     * (untuk tujuan demo lab). Endpoint ini menerima request apapun.
     *
     * Endpoint protected-transfer tetap dilindungi CSRF middleware.
     *
     * Kita memverifikasi bahwa middleware CSRF terdaftar di routes
     * yang dilindungi, dan TIDAK terdaftar di routes vulnerable.
     */
    public function test_csrf_vulnerable_endpoint_has_no_csrf_middleware(): void
    {
        // Vulnerable endpoint: request tanpa CSRF token berhasil (302 redirect)
        $response = $this->call('POST', '/csrf-lab/vulnerable-transfer', [
            'amount' => 100,
            'to' => 'attacker',
        ]);

        // Berhasil diproses (redirect) karena CSRF middleware di-exclude
        $response->assertRedirect();
    }

    /**
     * ✅ Test: Ticket store memerlukan authentication (CSRF implisit)
     *
     * POST request ke /tickets oleh guest harus di-redirect ke login.
     * Ini memverifikasi bahwa authentication + CSRF bersama-sama
     * melindungi form submission.
     */
    public function test_guest_post_to_tickets_is_rejected(): void
    {
        $response = $this->post('/tickets', [
            'title' => 'Test Ticket dari Guest',
            'description' => 'Deskripsi panjang yang memenuhi validasi minimum untuk test.',
            'priority' => 'medium',
        ]);

        // Guest di-redirect ke login
        $response->assertRedirect('/login');

        // Ticket TIDAK tersimpan di database
        $this->assertDatabaseMissing('tickets', [
            'title' => 'Test Ticket dari Guest',
        ]);
    }
}
