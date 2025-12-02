<?php

use App\Http\Controllers\ProjectDetailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\EmployeeController;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected routes (memerlukan login)
Route::middleware(['auth', 'verified', 'check.account.status'])->group(function () {

    // Dashboard - semua user bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('workspaces', WorkspaceController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');

    Route::get('/sprint-board', [SprintController::class, 'board'])->name('sprint-board');

    // Workspace detail (dynamic) — show workspace by id (workspace_id)
    Route::get('/workspaces/{workspace}', [ProjectDetailController::class, 'show'])->name('workspaces.show');

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
        Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend')->middleware('permission:edit users');
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate')->middleware('permission:edit users');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password')->middleware('permission:edit users');
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

    // Employee Management - butuh permission
    Route::middleware(['permission:view employees'])->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::post('/', [EmployeeController::class, 'store'])->middleware('permission:create employees')->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->middleware('permission:edit employees')->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->middleware('permission:edit employees')->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->middleware('permission:delete employees')->name('destroy');
    });

    // Risk Management Routes (for testing - hardcoded data)
    Route::prefix('risk')->name('risk.')->group(function () {
        Route::get('/dashboard', function () {
            return view('risk.dashboard');
        })->name('dashboard');
        
        Route::get('/', function () {
            return view('risk.index');
        })->name('index');
        
        Route::get('/issues', function () {
            return view('risk.issues');
        })->name('issues');
        
        Route::get('/change-requests', function () {
            return view('risk.change-requests');
        })->name('change-requests');
    });
});

// Breeze auth routes
require __DIR__.'/auth.php';
