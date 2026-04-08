# 📦 Tentang Spatie Laravel-Permission

## Apa itu Spatie Laravel-Permission?

[Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission) adalah package populer untuk implementasi RBAC yang lebih kompleks di Laravel.

## Mengapa Materi Ini TIDAK Menggunakan Spatie?

### 1. Tujuan Edukatif

Memahami **native Laravel authorization** (Gates & Policies) adalah fondasi penting sebelum menggunakan package. Dengan memahami cara kerja native:

- Siswa mengerti konsep dasar authorization
- Lebih mudah debugging jika ada masalah
- Dapat menyesuaikan solusi sesuai kebutuhan

### 2. Kompleksitas Proyek

Untuk sistem ticketing sederhana dengan 3 role (admin, staff, user):

| Aspek | Native Laravel | Spatie |
|-------|----------------|--------|
| Setup | Minimal | Install package + migrations |
| Learning curve | Rendah | Medium |
| Database tables | 1 (users.role) | 5 tables |
| Cocok untuk | Simple RBAC | Complex permissions |

### 3. Sesuai Kurikulum

Materi PPT dirancang untuk mengajarkan:
- Laravel Gates
- Laravel Policies
- Simple role column approach
- Custom RoleMiddleware

## Kapan Harus Menggunakan Spatie?

✅ **Gunakan Spatie jika:**

1. **Multiple permissions per role** - Role bisa punya banyak permissions yang berbeda
   ```
   Admin → create_user, delete_user, view_reports, manage_settings
   Editor → create_post, edit_post, publish_post
   ```

2. **Dynamic role/permission** - Admin bisa create role baru via UI

3. **Permission inheritance** - Role mewarisi permissions dari role lain

4. **User multiple roles** - Satu user bisa punya lebih dari satu role

5. **Audit trail** - Perlu track perubahan role/permission

❌ **Native Laravel cukup jika:**

1. Role sederhana (admin, staff, user)
2. Permissions fixed (tidak berubah-ubah)
3. Satu user = satu role
4. Tidak perlu manage role via UI

## Perbandingan Implementasi

### Native Laravel (Yang Kita Pakai)

```php
// Migration - Simple
Schema::table('users', function (Blueprint $table) {
    $table->enum('role', ['admin', 'staff', 'user'])->default('user');
});

// Model User
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

// Policy
public function update(User $user, Ticket $ticket): bool
{
    return $user->isAdmin() || $ticket->user_id === $user->id;
}

// Blade
@can('update', $ticket)
    <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
@endcan
```

### Spatie Laravel-Permission

```php
// Migration - 5 tables (roles, permissions, model_has_roles, etc.)

// Assign role
$user->assignRole('admin');

// Check permission
if ($user->can('edit tickets')) { ... }

// Blade
@can('edit tickets')
    <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
@endcan

// Middleware
Route::middleware(['role:admin'])->group(...);
Route::middleware(['permission:edit tickets'])->group(...);
```

## Jika Ingin Mencoba Spatie (Opsional)

### Installation

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Setup Model

```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

### Create Roles & Permissions

```php
// database/seeders/RolePermissionSeeder.php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create permissions
Permission::create(['name' => 'view tickets']);
Permission::create(['name' => 'create tickets']);
Permission::create(['name' => 'edit tickets']);
Permission::create(['name' => 'delete tickets']);
Permission::create(['name' => 'manage users']);

// Create roles
$admin = Role::create(['name' => 'admin']);
$admin->givePermissionTo(Permission::all());

$staff = Role::create(['name' => 'staff']);
$staff->givePermissionTo(['view tickets', 'edit tickets']);

$user = Role::create(['name' => 'user']);
$user->givePermissionTo(['view tickets', 'create tickets']);
```

## Kesimpulan

| Situasi | Rekomendasi |
|---------|-------------|
| Belajar Laravel authorization | Native (Gates & Policies) |
| Proyek kecil-menengah, role sederhana | Native |
| Proyek besar, permission kompleks | Spatie |
| Perlu dynamic role management | Spatie |
| Production app dengan audit requirements | Spatie |

---

**Untuk bootcamp ini, kita fokus pada native Laravel authorization karena:**
1. Lebih mudah dipahami untuk pemula
2. Cukup untuk sistem ticketing
3. Mengajarkan konsep fundamental yang berlaku universal
