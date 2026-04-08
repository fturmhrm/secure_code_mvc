<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | SECURE OPTIONS:
    | - 'bcrypt': Default, well-tested, recommended
    | - 'argon': Argon2i - memory-hard, resistant to GPU attacks
    | - 'argon2id': Argon2id - most secure, Laravel 10+ default
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | SECURITY: Cost factor (rounds)
    | - Higher = more secure but slower
    | - Default: 12 (4096 iterations)
    | - Recommended: 12-14 untuk production
    | - Minimum: 10
    |
    | Rule of thumb: Hashing should take ~100ms-500ms
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Argon2id adalah algoritma hashing modern yang lebih resistant 
    | terhadap GPU dan ASIC attacks.
    |
    | - memory: RAM yang digunakan (KB)
    | - threads: Parallelism factor
    | - time: Iterations
    |
    */

    'argon' => [
        'memory' => 65536,  // 64 MB
        'threads' => 1,
        'time' => 4,
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rehash on Login
    |--------------------------------------------------------------------------
    |
    | SECURITY: Otomatis re-hash password jika algorithm berubah
    | atau cost factor ditingkatkan.
    |
    | Contoh use case:
    | 1. Upgrade bcrypt rounds dari 10 ke 12
    | 2. Migrasi dari bcrypt ke argon2id
    |
    */

    'rehash_on_login' => true,

];

/*
|--------------------------------------------------------------------------
| SECURITY NOTES - Password Hashing
|--------------------------------------------------------------------------
|
| ❌ NEVER DO THIS (Vulnerable):
| 
| $password = $request->password;  // Plaintext!
| $user->password = md5($password);  // Weak hash!
| $user->password = sha256($password);  // No salt!
|
| ✅ ALWAYS DO THIS (Secure):
|
| use Illuminate\Support\Facades\Hash;
|
| // Hashing
| $user->password = Hash::make($request->password);
|
| // Verifying
| if (Hash::check($inputPassword, $user->password)) {
|     // Password correct
| }
|
| // Check if needs rehash
| if (Hash::needsRehash($user->password)) {
|     $user->password = Hash::make($inputPassword);
| }
|
| Laravel 10+ dengan 'password' => 'hashed' cast:
| $user->password = $request->password;  // Auto-hashed!
|
*/
