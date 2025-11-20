# 🚀 QUICK START GUIDE - Admin Starter Kit

## ✅ Status Instalasi Saat Ini

Berikut adalah status dari setup yang sudah dilakukan:

### Yang Sudah Selesai:
- ✅ Laravel 12 terinstall
- ✅ Laravel Breeze terinstall  
- ✅ Spatie Permission terinstall
- ✅ Database `admin_starter` dibuat
- ✅ Migrations dijalankan (users, roles, permissions, app_settings)
- ✅ Seeders dijalankan (3 users, 3 roles, permissions, app settings)
- ✅ Vuexy template assets dicopy ke public folder
- ✅ Controllers dibuat (Dashboard, User, Role, Permission, Profile, Setting)
- ✅ Models dibuat (User dengan HasRoles, AppSetting)

### Yang Perlu Dilengkapi:
- ⏳ Routes configuration
- ⏳ Views (layouts, dashboard, users, roles, dll)
- ⏳ Controller logic implementation

## 🎯 Langkah Selanjutnya

### 1. Copy Routes Configuration

Ganti isi file `routes/web.php` dengan kode berikut:

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected routes (memerlukan login)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard - semua user bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::prefix('profile')->name('profile.')->controller(AdminProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::post('/avatar', 'updateAvatar')->name('avatar.update');
        Route::patch('/password', 'updatePassword')->name('password.update');
    });

    // User Management - butuh permission
    Route::middleware(['permission:view users'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Role Management - butuh permission
    Route::middleware(['permission:view roles'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Permission Management - butuh permission
    Route::middleware(['permission:view permissions'])->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Settings - butuh permission
    Route::middleware(['permission:view settings'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');
    });
});

// Breeze auth routes
require __DIR__.'/auth.php';
```

### 2. Test Login

Jalankan server development:

```bash
cd c:\Laragon\www\kitnow\admin-starter
php artisan serve
```

Akses aplikasi di browser: `http://localhost:8000`

**Default Login Credentials:**
- Email: `admin@admin.com`
- Password: `password`

### 3. User Accounts Yang Tersedia

| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| **Super Admin** | admin@admin.com | password | Full Access |
| **Admin** | admin@example.com | password | Limited Access |
| **User** | user@example.com | password | Dashboard Only |

## 📁 File Structure Yang Sudah Dibuat

```
admin-starter/
├── app/
│   ├── Http/Controllers/Admin/
│   │   ├── DashboardController.php ✅
│   │   ├── UserController.php ✅
│   │   ├── RoleController.php ✅
│   │   ├── PermissionController.php ✅
│   │   ├── ProfileController.php ✅
│   │   └── SettingController.php ✅
│   └── Models/
│       ├── User.php ✅ (with HasRoles trait)
│       └── AppSetting.php ✅
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php ✅ (with avatar column)
│   │   ├── create_permission_tables.php ✅ (Spatie)
│   │   └── create_app_settings_table.php ✅
│   └── seeders/
│       ├── DatabaseSeeder.php ✅
│       ├── RolePermissionSeeder.php ✅
│       └── AppSettingSeeder.php ✅
├── public/
│   ├── assets/ ✅ (Vuexy CSS, JS, Images)
│   └── libs/ ✅ (Vuexy Libraries)
└── routes/
    └── web.php ⏳ (needs manual update)
```

## 🔧 Konfigurasi Database

File `.env` sudah dikonfigurasi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_starter
DB_USERNAME=root
DB_PASSWORD=
```

## 📊 Database Structure

### Tables:
1. **users** - Data user dengan avatar
2. **roles** - Master roles (Super Admin, Admin, User)
3. **permissions** - Master permissions
4. **model_has_roles** - Mapping user-role
5. **model_has_permissions** - Mapping user-permission
6. **role_has_permissions** - Mapping role-permission
7. **app_settings** - Konfigurasi aplikasi (logo, nama, warna, dll)

### Pre-loaded Permissions:
- Dashboard: `view dashboard`
- Users: `view users`, `create users`, `edit users`, `delete users`
- Roles: `view roles`, `create roles`, `edit roles`, `delete roles`
- Permissions: `view permissions`, `create permissions`, `edit permissions`, `delete permissions`
- Settings: `view settings`, `edit settings`

### Pre-loaded Settings:
- `app_name` = "Admin Starter"
- `app_logo` = "/assets/img/logo.png"
- `app_favicon` = "/assets/img/favicon.ico"
- `primary_color` = "#696cff"
- `secondary_color` = "#8592a3"
- `theme_mode` = "light"
- `timezone` = "Asia/Jakarta"
- `date_format` = "Y-m-d"
- `time_format` = "H:i:s"

## 🎨 Customization

### Mengubah Settings via Code:

```php
use App\Models\AppSetting;

// Get setting
$appName = AppSetting::get('app_name');

// Set setting
AppSetting::set('app_name', 'My Custom App');

// Get all settings
$settings = AppSetting::getAllSettings();

// Get settings by group
$themeSettings = AppSetting::getByGroup('theme');
```

### Check User Permissions:

```php
// Di Controller atau Blade
if (auth()->user()->hasRole('Super Admin')) {
    // Do something
}

if (auth()->user()->can('edit users')) {
    // Do something
}

// Di Blade
@role('Super Admin')
    <p>Hanya Super Admin yang bisa lihat ini</p>
@endrole

@can('edit users')
    <button>Edit User</button>
@endcan
```

## 🚀 Next Development Steps

Untuk melanjutkan development, Anda perlu membuat:

### 1. Views Structure:
```
resources/views/
├── layouts/
│   ├── admin.blade.php (Main Layout)
│   └── partials/
│       ├── sidebar.blade.php
│       ├── navbar.blade.php
│       └── footer.blade.php
├── admin/
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── roles/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── permissions/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── profile/
│   │   └── edit.blade.php
│   └── settings/
│       └── index.blade.php
```

### 2. Controller Implementation

Implement logic di setiap controller untuk:
- DashboardController: Tampilkan statistik (total users, roles, dll)
- UserController: CRUD users + assign roles
- RoleController: CRUD roles + assign permissions
- PermissionController: CRUD permissions
- ProfileController: Update profile, avatar, password
- SettingController: Update app settings

### 3. JavaScript & AJAX

Tambahkan file JS untuk:
- DataTables untuk list data
- Form validation
- Image upload preview
- Color picker
- SweetAlert untuk confirmations

## 💡 Development Tips

1. **Permission Middleware**: Routes sudah diproteksi dengan middleware `permission:`
2. **Role Check**: Gunakan `hasRole()` untuk check role user
3. **Setting Helper**: Model `AppSetting` punya method `get()` dan `set()`
4. **Avatar Upload**: Column `avatar` sudah ada di users table
5. **Vuexy Assets**: Semua CSS/JS Vuexy sudah tersedia di `public/assets` dan `public/libs`

## 🐛 Troubleshooting

### Permission Denied Error:
```bash
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

### Assets Tidak Load:
```bash
npm install
npm run build
```

### Reset Database:
```bash
php artisan migrate:fresh --seed
```

## 📞 Support

Jika ada yang perlu bantuan implementasi lebih lanjut:
1. Dashboard widgets dengan charts
2. CRUD forms dengan validation
3. Role & Permission assignment forms
4. File upload untuk avatar dan logo
5. Settings page dengan color picker

---

**Project ini sudah 60% selesai!** Yang tersisa tinggal views dan controller logic. 🎉
