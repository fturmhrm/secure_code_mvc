<?php

namespace Database\Seeders;

use App\Models\SqliLabProduct;
use App\Models\SqliLabUser;
use App\Models\Ticket;
use App\Models\User;
use App\Models\VulnerableUser;
use App\Models\XssLabComment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder - Master Seeder untuk Semua Lab
 *
 * Seeder ini mengkonsolidasikan semua data untuk:
 * - Authorization Lab (Minggu 4 Hari 2): 4 users dengan RBAC
 * - BAC/IDOR Lab (Minggu 4 Hari 4): 3 users untuk demo IDOR
 * - SQLi Lab: Products & Users untuk demo SQL Injection
 * - XSS Lab: Comments untuk demo XSS
 *
 * Jalankan: php artisan db:seed
 * Reset:   php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('🌱 SEEDING DATABASE...');
        $this->command->info('========================================');

        // ============================================
        // 1. AUTHORIZATION LAB USERS (Minggu 4 Hari 2)
        // ============================================
        $this->command->info('');
        $this->command->info('📁 [1/5] Authorization Lab Users...');

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@wikrama.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $staff = User::create([
            'name' => 'Staff Helpdesk',
            'email' => 'staff@wikrama.sch.id',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@student.wikrama.sch.id',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $siti = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@student.wikrama.sch.id',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->command->info('   ✓ Created 4 users: admin, staff, budi, siti');

        // ============================================
        // 2. BAC/IDOR LAB USERS (Minggu 4 Hari 4)
        // ============================================
        $this->command->info('');
        $this->command->info('📁 [2/5] BAC/IDOR Lab Users...');

        // Victim user - punya data sensitif yang akan dicoba diakses
        $victim = User::create([
            'name' => 'Korban (Victim)',
            'email' => 'victim@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Attacker user - akan mencoba IDOR
        $attacker = User::create([
            'name' => 'Penyerang (Attacker)',
            'email' => 'attacker@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->command->info('   ✓ Created 2 users: victim, attacker');

        // ============================================
        // 3. TICKETS - Authorization Lab
        // ============================================
        $this->command->info('');
        $this->command->info('📁 [3/5] Authorization Lab Tickets...');

        // Ticket milik Budi
        Ticket::create([
            'user_id' => $budi->id,
            'assigned_to' => $staff->id,
            'title' => 'Tidak bisa login ke e-learning',
            'description' => 'Saya sudah coba reset password tapi tetap tidak bisa masuk ke sistem e-learning.',
            'priority' => 'high',
            'status' => 'in_progress',
        ]);

        Ticket::create([
            'user_id' => $budi->id,
            'assigned_to' => null,
            'title' => 'Request akses lab komputer',
            'description' => 'Mohon dibukakan akses ke lab komputer untuk mengerjakan tugas praktikum.',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        // Ticket milik Siti
        Ticket::create([
            'user_id' => $siti->id,
            'assigned_to' => $staff->id,
            'title' => 'Printer di perpustakaan error',
            'description' => 'Printer di perpustakaan mengeluarkan kertas kosong. Sudah dicoba restart tapi masih sama.',
            'priority' => 'medium',
            'status' => 'in_progress',
        ]);

        Ticket::create([
            'user_id' => $siti->id,
            'assigned_to' => null,
            'title' => 'WiFi lambat di kelas XII RPL',
            'description' => 'Koneksi WiFi sangat lambat di area kelas XII RPL, sulit untuk mengakses materi online.',
            'priority' => 'low',
            'status' => 'open',
        ]);

        // Ticket closed untuk testing
        Ticket::create([
            'user_id' => $budi->id,
            'assigned_to' => $staff->id,
            'title' => 'Password email terlupa',
            'description' => 'Lupa password email sekolah.',
            'priority' => 'high',
            'status' => 'closed',
        ]);

        $this->command->info('   ✓ Created 5 tickets for authorization lab');

        // ============================================
        // 4. TICKETS - BAC/IDOR Lab
        // ============================================
        $this->command->info('');
        $this->command->info('📁 [4/5] BAC/IDOR Lab Tickets...');

        // Ticket milik Victim - Data SENSITIF (target IDOR)
        $victimTicket1 = Ticket::create([
            'user_id' => $victim->id,
            'title' => '[CONFIDENTIAL] Laporan Keuangan Q1',
            'description' => 'Data keuangan rahasia perusahaan. Total pendapatan: Rp 500.000.000. Data ini SEHARUSNYA hanya bisa dilihat oleh pemiliknya!',
            'status' => 'open',
            'priority' => 'high',
        ]);

        $victimTicket2 = Ticket::create([
            'user_id' => $victim->id,
            'title' => '[PRIVATE] Data Pribadi Karyawan',
            'description' => 'NIK: 3201xxxxx, Alamat: Jl. Rahasia No. 123. IDOR vulnerability membuat data ini bocor!',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        // Ticket milik Attacker
        $attackerTicket1 = Ticket::create([
            'user_id' => $attacker->id,
            'title' => 'Tiket Biasa Attacker',
            'description' => 'Ini adalah tiket normal milik attacker. Attacker akan mencoba mengakses tiket victim dengan mengganti ID!',
            'status' => 'open',
            'priority' => 'low',
        ]);

        $attackerTicket2 = Ticket::create([
            'user_id' => $attacker->id,
            'title' => 'Permintaan Support',
            'description' => 'Tiket support biasa dari attacker.',
            'status' => 'open',
            'priority' => 'medium',
        ]);

        // Ticket Admin untuk BAC Lab
        Ticket::create([
            'user_id' => $admin->id,
            'title' => '[ADMIN] System Maintenance',
            'description' => 'Jadwal maintenance server. Admin bisa melihat semua tiket.',
            'status' => 'open',
            'priority' => 'medium',
        ]);

        $this->command->info('   ✓ Created 5 tickets for BAC/IDOR lab');

        // ============================================
        // 5. OTHER LABS DATA
        // ============================================
        $this->command->info('');
        $this->command->info('📁 [5/5] Other Labs Data...');

        // SQLi Lab Products (jika model exists)
        if (class_exists(SqliLabProduct::class)) {
            SqliLabProduct::truncate();
            SqliLabProduct::insert([
                ['name' => 'Laptop ASUS ROG', 'price' => 15000000, 'description' => 'Gaming laptop dengan RTX 3060', 'stock' => 10],
                ['name' => 'iPhone 15 Pro', 'price' => 18000000, 'description' => 'Smartphone Apple terbaru', 'stock' => 25],
                ['name' => 'Mechanical Keyboard', 'price' => 500000, 'description' => 'Keyboard gaming RGB', 'stock' => 50],
                ['name' => 'Gaming Mouse', 'price' => 350000, 'description' => 'Mouse dengan DPI tinggi', 'stock' => 40],
                ['name' => 'Monitor 27 inch', 'price' => 3500000, 'description' => 'Monitor IPS 144Hz', 'stock' => 15],
            ]);
            $this->command->info('   ✓ Created SQLi Lab products');
        }

        // SQLi Lab Users (jika model exists)
        if (class_exists(SqliLabUser::class)) {
            SqliLabUser::truncate();
            SqliLabUser::insert([
                ['username' => 'admin', 'password' => 'supersecret123', 'email' => 'admin@sqlilab.local', 'role' => 'admin'],
                ['username' => 'user1', 'password' => 'password123', 'email' => 'user1@sqlilab.local', 'role' => 'user'],
                ['username' => 'staff', 'password' => 'staffpass', 'email' => 'staff@sqlilab.local', 'role' => 'staff'],
            ]);
            $this->command->info('   ✓ Created SQLi Lab users');
        }

        // Vulnerable Users for Auth Lab (jika model exists)
        if (class_exists(VulnerableUser::class)) {
            VulnerableUser::truncate();
            VulnerableUser::insert([
                ['name' => 'Admin Vulnerable', 'email' => 'admin@vulnerable.test', 'password' => Hash::make('admin123')],
                ['name' => 'User Vulnerable', 'email' => 'user@vulnerable.test', 'password' => Hash::make('user123')],
            ]);
            $this->command->info('   ✓ Created Vulnerable Auth users');
        }

        // XSS Lab Comments (jika model exists dan ada ticket)
        if (class_exists(XssLabComment::class)) {
            // XSS lab comments need a ticket_id, so we skip if tickets are empty
            $firstTicket = Ticket::first();
            if ($firstTicket) {
                XssLabComment::truncate();
                XssLabComment::insert([
                    ['ticket_id' => $firstTicket->id, 'author_name' => 'John', 'content' => 'Great article! Very informative.', 'created_at' => now(), 'updated_at' => now()],
                    ['ticket_id' => $firstTicket->id, 'author_name' => 'Jane', 'content' => 'Thanks for sharing this knowledge.', 'created_at' => now(), 'updated_at' => now()],
                ]);
                $this->command->info('   ✓ Created XSS Lab comments');
            }
        }

        // ============================================
        // OUTPUT SUMMARY
        // ============================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✅ DATABASE SEEDED SUCCESSFULLY!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📋 AUTHORIZATION LAB ACCOUNTS:');
        $this->command->table(
            ['Email', 'Password', 'Role', 'Purpose'],
            [
                ['admin@wikrama.sch.id', 'password', 'admin', 'Full access'],
                ['staff@wikrama.sch.id', 'password', 'staff', 'Handle tickets'],
                ['budi@student.wikrama.sch.id', 'password', 'user', 'Regular user'],
                ['siti@student.wikrama.sch.id', 'password', 'user', 'Regular user'],
            ]
        );
        $this->command->info('');
        $this->command->info('📋 BAC/IDOR LAB ACCOUNTS:');
        $this->command->table(
            ['Email', 'Password', 'Role', 'Purpose'],
            [
                ['victim@test.com', 'password', 'user', '🎯 TARGET - Has sensitive data'],
                ['attacker@test.com', 'password', 'user', '🔓 ATTACKER - Will try IDOR'],
            ]
        );
        $this->command->info('');
        $this->command->info('📋 BAC/IDOR LAB TICKET IDs:');
        $this->command->table(
            ['Ticket ID', 'Owner', 'Title'],
            [
                ['#'.$victimTicket1->id, 'Victim', '[CONFIDENTIAL] Laporan Keuangan Q1'],
                ['#'.$victimTicket2->id, 'Victim', '[PRIVATE] Data Pribadi Karyawan'],
                ['#'.$attackerTicket1->id, 'Attacker', 'Tiket Biasa Attacker'],
                ['#'.$attackerTicket2->id, 'Attacker', 'Permintaan Support'],
            ]
        );
        $this->command->info('');
        $this->command->warn('🔓 IDOR TEST: Login sebagai attacker, akses /bac-lab/vulnerable/tickets/'.$victimTicket1->id);
        $this->command->info('');
    }
}
