<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk SQL Injection Lab
 *
 * Membuat tabel:
 * 1. sqli_lab_products - untuk demo search
 * 2. sqli_lab_users - untuk demo login bypass
 *
 * CATATAN: Tabel users menyimpan password plaintext
 * HANYA untuk keperluan demo SQLi.
 * Di production, SELALU gunakan Hash::make()!
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Products untuk demo search SQLi
        Schema::create('sqli_lab_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // Tabel Users untuk demo login bypass SQLi
        // PERINGATAN: Password plaintext HANYA untuk demo!
        Schema::create('sqli_lab_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password'); // Plaintext untuk demo SQLi
            $table->string('email')->unique();
            $table->string('role')->default('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sqli_lab_products');
        Schema::dropIfExists('sqli_lab_users');
    }
};
