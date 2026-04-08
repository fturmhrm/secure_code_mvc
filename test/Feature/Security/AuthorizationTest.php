<?php

namespace Tests\Feature\Security;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AuthorizationTest - Security Testing untuk Authorization (IDOR & RBAC)
 *
 * OWASP A01:2025 - Broken Access Control (Ranking #1!)
 *
 * Test ini membuktikan bahwa mekanisme authorization
 * di aplikasi secure-ticketing bekerja dengan benar:
 * - IDOR prevention (user tidak bisa akses data orang lain)
 * - Role-based access control (user ≠ staff ≠ admin)
 * - TicketPolicy enforcement
 *
 * Minggu 6 Hari 1 - Security Testing & PHPUnit
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // ===============================================
    // IDOR TESTS - Insecure Direct Object Reference
    // ===============================================

    /**
     * ✅ Test: User TIDAK BISA lihat ticket orang lain (IDOR View)
     *
     * Skenario: User B mencoba akses ticket User A
     * melalui URL /tickets/{id}
     */
    public function test_user_cannot_view_other_users_ticket(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // User A buat ticket
        $ticket = Ticket::factory()->create([
            'user_id' => $userA->id,
        ]);

        // User B coba akses ticket User A
        $response = $this->actingAs($userB)
            ->get("/tickets/{$ticket->id}");

        // HARUS ditolak!
        $response->assertStatus(403);
    }

    /**
     * ✅ Test: User TIDAK BISA edit ticket orang lain (IDOR Edit)
     */
    public function test_user_cannot_edit_other_users_ticket(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $ticket = Ticket::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)
            ->put("/tickets/{$ticket->id}", [
                'title' => 'Hacked Title by User B',
                'description' => 'This is a hacked description that should not be saved',
                'priority' => 'high',
            ]);

        $response->assertStatus(403);

        // VERIFIKASI: Data TIDAK berubah di database!
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => $ticket->title, // Masih title asli
        ]);
    }

    /**
     * ✅ Test: User TIDAK BISA delete ticket orang lain (IDOR Delete)
     *
     * Note: Berdasarkan TicketPolicy, hanya admin yang bisa delete.
     * User biasa tidak bisa delete ticket siapapun (termasuk miliknya sendiri).
     */
    public function test_user_cannot_delete_other_users_ticket(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $ticket = Ticket::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)
            ->delete("/tickets/{$ticket->id}");

        $response->assertStatus(403);

        // VERIFIKASI: Ticket masih ada di database!
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    /**
     * ✅ Test: User biasa TIDAK BISA delete ticket sendiri
     *
     * Berdasarkan TicketPolicy->delete(), hanya admin yang bisa hapus.
     */
    public function test_user_cannot_delete_own_ticket(): void
    {
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete("/tickets/{$ticket->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    // ===============================================
    // RBAC TESTS - Role-Based Access Control
    // ===============================================

    /**
     * ✅ Test: User biasa TIDAK BISA akses admin dashboard
     *
     * Membuktikan vertical privilege escalation tidak mungkin.
     * Admin route dilindungi middleware 'role:admin'.
     */
    public function test_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(); // role = 'user' (default)

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    /**
     * ✅ Test: Staff TIDAK BISA akses admin dashboard
     *
     * Staff memiliki role lebih tinggi dari user biasa,
     * tapi tetap tidak boleh akses admin dashboard.
     */
    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)->get('/admin');

        $response->assertStatus(403);
    }

    /**
     * ✅ Test: Admin BISA akses admin dashboard
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    /**
     * ✅ Test: Admin BISA lihat ticket siapapun
     *
     * TicketPolicy before() memberikan Admin full access.
     */
    public function test_admin_can_view_any_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)
            ->get("/tickets/{$ticket->id}");

        $response->assertStatus(200);
    }

    /**
     * ✅ Test: Admin BISA delete ticket siapapun
     *
     * TicketPolicy before() memberikan Admin full access untuk delete.
     */
    public function test_admin_can_delete_any_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)
            ->delete("/tickets/{$ticket->id}");

        // Admin berhasil delete → redirect ke index
        $response->assertRedirect(route('tickets.index'));

        // Ticket sudah terhapus dari database
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    /**
     * ✅ Test: User BISA lihat ticket miliknya sendiri
     *
     * Verifikasi bahwa authorization tidak terlalu ketat
     * (tidak memblok akses yang sah).
     */
    public function test_user_can_view_own_ticket(): void
    {
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get("/tickets/{$ticket->id}");

        $response->assertStatus(200);
    }

    /**
     * ✅ Test: Staff BISA lihat ticket siapa saja
     *
     * Berdasarkan TicketPolicy->view(), staff bisa lihat semua tickets.
     */
    public function test_staff_can_view_any_ticket(): void
    {
        $staff = User::factory()->staff()->create();
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($staff)
            ->get("/tickets/{$ticket->id}");

        $response->assertStatus(200);
    }
}
