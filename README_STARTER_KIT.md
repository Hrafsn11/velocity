# 🚀 ADMIN STARTER KIT - Laravel + Vuexy

Starter kit Laravel lengkap dengan Dashboard, Roles, Permissions, Profile Management, dan App Configuration.

## 📌 Fitur Utama

- ✅ **Authentication** (Login, Register, Forgot Password) - Laravel Breeze
- ✅ **Dashboard** dengan statistik dan charts
- ✅ **User Management** (CRUD users dengan role assignment)
- ✅ **Role Management** (CRUD roles dengan permission assignment)
- ✅ **Permission Management** (CRUD permissions)
- ✅ **Profile Management** (Update profile, password, avatar)
- ✅ **App Configuration** (Ganti logo, nama app, primary color, dll)
- ✅ **Vuexy Bootstrap Admin Template** - Modern & Responsive

## 🛠️ Tech Stack

- **Laravel 12.x**
- **Laravel Breeze** (Blade)
- **Spatie Laravel Permission**
- **Vuexy Bootstrap Template**
- **MySQL Database**
- **Bootstrap 5**
- **jQuery & AJAX**

## 📦 Instalasi & Setup

### 1. Persiapan Database

Database `admin_starter` sudah dibuat otomatis. Pastikan MySQL sudah running di Laragon.

### 2. Update Model User

Edit file `app/Models/User.php` dan tambahkan trait Spatie Permission:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### 3. Buat Model AppSetting

```bash
cd c:\Laragon\www\kitnow\admin-starter
php artisan make:model AppSetting -m
```

Edit migration `database/migrations/xxxx_create_app_settings_table.php`:

```php
public function up(): void
{
    Schema::create('app_settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->string('type')->default('text'); // text, file, color, select
        $table->string('group')->default('general'); // general, theme, email, etc
        $table->timestamps();
    });
}
```

Edit model `app/Models/AppSetting.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
```

### 4. Update DatabaseSeeder

Edit `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AppSettingSeeder::class,
        ]);
    }
}
```

### 5. Jalankan Migration dan Seeder

```bash
php artisan migrate:fresh --seed
```

### 6. Buat Controllers

```bash
php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/UserController --resource
php artisan make:controller Admin/RoleController --resource
php artisan make:controller Admin/PermissionController --resource
php artisan make:controller Admin/ProfileController
php artisan make:controller Admin/SettingController
```

### 7. Setup Routes

Edit `routes/web.php`:

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
    });

    // User Management (with permission middleware)
    Route::middleware(['permission:view users'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Role Management
    Route::middleware(['permission:view roles'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Permission Management
    Route::middleware(['permission:view permissions'])->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Settings
    Route::middleware(['permission:view settings'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');
    });
});

require __DIR__.'/auth.php';
```

### 8. Buat Layout Vuexy

Buat file `resources/views/layouts/admin.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.partials.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.partials.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.partials.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
```

## 🔐 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@admin.com | password |
| Admin | admin@example.com | password |
| User | user@example.com | password |

## 🎨 Customize Settings

Setelah login, buka menu **Settings** untuk:
- Ganti nama aplikasi
- Upload logo custom
- Ubah primary color
- Atur timezone dan format tanggal

## 📁 Struktur Project

```
admin-starter/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Admin/
│   │           ├── DashboardController.php
│   │           ├── UserController.php
│   │           ├── RoleController.php
│   │           ├── PermissionController.php
│   │           ├── ProfileController.php
│   │           └── SettingController.php
│   └── Models/
│       ├── User.php
│       └── AppSetting.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── RolePermissionSeeder.php
│       └── AppSettingSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   └── partials/
│       │       ├── sidebar.blade.php
│       │       ├── navbar.blade.php
│       │       └── footer.blade.php
│       ├── dashboard/
│       ├── users/
│       ├── roles/
│       ├── permissions/
│       ├── profile/
│       └── settings/
└── public/
    ├── assets/
    └── libs/
```

## 🚀 Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## 📝 Next Steps

1. Customize dashboard widgets sesuai kebutuhan
2. Tambah fitur notifikasi
3. Implement activity log
4. Tambah export/import data
5. Setup email configuration

## 💡 Tips

- Gunakan middleware `permission:` untuk proteksi routes
- Gunakan `auth()->user()->hasRole('Super Admin')` untuk check role
- Gunakan `auth()->user()->can('edit users')` untuk check permission
- Gunakan `AppSetting::get('app_name')` untuk ambil settings

## 🐛 Troubleshooting

### Jika ada error permission:
```bash
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

### Jika assets tidak load:
```bash
npm install
npm run build
```

## 📧 Support

Jika ada pertanyaan, silakan dokumentasikan issue atau custom request Anda.

---

**Happy Coding! 🎉**
