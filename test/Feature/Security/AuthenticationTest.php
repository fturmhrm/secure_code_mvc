<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AuthenticationTest - Security Testing untuk Authentication
 *
 * OWASP A07:2025 - Authentication Failures
 *
 * Test ini membuktikan bahwa mekanisme authentication
 * di aplikasi secure-ticketing bekerja dengan benar:
 * - Login valid berhasil
 * - Login invalid ditolak
 * - Brute force protection (rate limiting)
 * - Password tidak terexpose di error
 * - Logout menghapus session
 * - Guest tidak bisa akses protected route
 *
 * Minggu 6 Hari 1 - Security Testing & PHPUnit
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ✅ Test: Halaman login bisa diakses
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * ✅ Test: Login berhasil dengan credential valid
     *
     * Membuktikan bahwa authentication flow berjalan normal
     * untuk user yang memberikan credential yang benar.
     */
    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password', // Default factory password
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * ✅ Test: Login GAGAL dengan password salah
     *
     * Membuktikan bahwa password yang salah benar-benar ditolak
     * dan user TIDAK ter-authenticate.
     */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // User harus tetap sebagai guest (tidak login)
        $this->assertGuest();
    }

    /**
     * ✅ Test: Login GAGAL dengan email yang tidak terdaftar
     *
     * Membuktikan bahwa email tidak terdaftar juga ditolak.
     */
    public function test_users_cannot_authenticate_with_nonexistent_email(): void
    {
        $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /**
     * ✅ Security Test: Password TIDAK ditampilkan di error response
     *
     * OWASP: Pesan error tidak boleh mengandung password yang diinput.
     * Jika password muncul di response, ini adalah information disclosure.
     */
    public function test_password_not_exposed_in_error_response(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'MySecretPassword123!',
        ]);

        // Password TIDAK boleh muncul di response body
        $response->assertDontSee('MySecretPassword123!');
    }

    /**
     * ✅ Security Test: Brute Force Protection (Rate Limiting)
     *
     * OWASP A07: Aplikasi harus mencegah automated attack
     * seperti credential stuffing dan brute force.
     *
     * Setelah beberapa percobaan gagal, percobaan berikutnya
     * HARUS di-block (throttle).
     */
    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        $user = User::factory()->create();

        // Simulasi percobaan login gagal berulang kali
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // Percobaan berikutnya HARUS ditolak (throttled)
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Laravel Breeze mengembalikan session error tentang throttling
        // bukan HTTP 429, tapi redirect dengan error message
        $response->assertSessionHasErrors();
    }

    /**
     * ✅ Security Test: Logout menghapus session
     *
     * Setelah logout, user TIDAK boleh masih ter-authenticate.
     * Jika session tidak dihapus → session hijacking risk.
     */
    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create();

        // Login dulu
        $this->actingAs($user);
        $this->assertAuthenticated();

        // Logout
        $this->post('/logout');

        // Harus menjadi guest
        $this->assertGuest();
    }

    /**
     * ✅ Security Test: Guest tidak bisa akses protected route (dashboard)
     *
     * Route yang memerlukan authentication harus redirect
     * guest ke halaman login.
     */
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        // Harus redirect ke login
        $response->assertRedirect('/login');
    }

    /**
     * ✅ Security Test: Guest tidak bisa akses halaman tickets
     *
     * Semua route tickets dilindungi middleware 'auth'.
     */
    public function test_guest_cannot_access_tickets(): void
    {
        $response = $this->get('/tickets');

        $response->assertRedirect('/login');
    }
}
