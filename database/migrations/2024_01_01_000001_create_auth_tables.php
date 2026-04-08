<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Authentication Lab
 *
 * Membuat tabel tambahan untuk demo Auth Lab:
 * - vulnerable_users: untuk demo vulnerable auth (password plaintext)
 * - login_attempts: untuk tracking login attempts
 *
 * Note: Tabel users, password_reset_tokens, sessions sudah dibuat
 * oleh migration default Laravel (0001_01_01_000000_create_users_table.php)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // VULNERABLE: Users table dengan password PLAINTEXT
        // HANYA UNTUK DEMO - JANGAN GUNAKAN DI PRODUCTION!
        // ============================================
        Schema::create('vulnerable_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // PLAINTEXT - VULNERABLE!
            $table->timestamps();
        });

        // Login attempts tracking untuk comparison
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address', 45);
            $table->boolean('successful')->default(false);
            $table->string('type'); // 'secure' atau 'vulnerable'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('vulnerable_users');
    }
};
