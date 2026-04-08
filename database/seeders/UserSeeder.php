<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Test users untuk authorization lab:
     * - 1 Admin: Full access
     * - 1 Staff: Can view all, edit assigned
     * - 2 Users: Can only manage own tickets
     */
    public function run(): void
    {
        // ========================================
        // ADMIN
        // ========================================
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@wikrama.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // ========================================
        // STAFF
        // ========================================
        $staff = User::create([
            'name' => 'Staff Helpdesk',
            'email' => 'staff@wikrama.sch.id',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        // ========================================
        // USERS (Students)
        // ========================================
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

        // ========================================
        // SAMPLE TICKETS
        // ========================================

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
    }
}
