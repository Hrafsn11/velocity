<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
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
        Route::put('/', 'update')->name('update');
        Route::post('/avatar', 'updateAvatar')->name('avatar');
        Route::put('/password', 'updatePassword')->name('password');
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

    // Configuration - butuh permission
    Route::middleware(['permission:manage settings'])->prefix('config')->name('admin.config.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ConfigController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\ConfigController::class, 'store'])->name('store');
        Route::get('/reset', [App\Http\Controllers\Admin\ConfigController::class, 'reset'])->name('reset');
    });
});

// Breeze auth routes
require __DIR__.'/auth.php';
