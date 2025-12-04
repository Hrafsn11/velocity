<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\KanbanBoardController;
use App\Http\Controllers\KanbanTaskController;
use App\Http\Controllers\ProjectDetailController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

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

    // Kanban Board Management
    Route::prefix('workspaces/{workspace}/kanban')->name('kanban.')->group(function () {
        // Boards
        Route::get('/boards', [KanbanBoardController::class, 'index'])->name('boards.index');
        Route::post('/boards', [KanbanBoardController::class, 'store'])->name('boards.store');
        Route::put('/boards/{board}', [KanbanBoardController::class, 'update'])->name('boards.update');
        Route::delete('/boards/{board}', [KanbanBoardController::class, 'destroy'])->name('boards.destroy');
        Route::post('/boards/reorder', [KanbanBoardController::class, 'reorder'])->name('boards.reorder');

        // Tasks
        Route::get('/tasks/{task}', [KanbanTaskController::class, 'show'])->name('tasks.show');
        Route::post('/tasks', [KanbanTaskController::class, 'store'])->name('tasks.store');
        Route::put('/tasks/{task}', [KanbanTaskController::class, 'update'])->name('tasks.update');
        Route::post('/tasks/{task}/move', [KanbanTaskController::class, 'move'])->name('tasks.move');
        Route::delete('/tasks/{task}', [KanbanTaskController::class, 'destroy'])->name('tasks.destroy');
        Route::post('/tasks/{task}/comment', [KanbanTaskController::class, 'comment'])->name('tasks.comment');
        Route::post('/tasks/{task}/attach', [KanbanTaskController::class, 'attach'])->name('tasks.attach');
        Route::delete('/attachments/{attachment}', [KanbanTaskController::class, 'deleteAttachment'])->name('attachments.destroy');
        Route::get('/tasks/{task}/activities', [KanbanTaskController::class, 'activities'])->name('tasks.activities');
    });


    // Risk Management Routes
    Route::prefix('risk')->name('risk.')->group(function () {
        // Test endpoint for debugging
        Route::get('/test', function () {
            return response()->json(['message' => 'Risk routes work!', 'timestamp' => now()]);
        });
        
        // Debug: Check tasks and issues
        Route::get('/debug', function () {
            $tasks = \App\Models\KanbanTask::select('task_id', 'title', 'workspace_id')
                ->with(['issues' => function($q) {
                    $q->select('issue_id', 'code', 'title', 'linked_task_id', 'status');
                }])
                ->limit(5)
                ->get();
            
            $issues = \App\Models\Issue::select('issue_id', 'code', 'title', 'linked_task_id', 'status')
                ->get();
            
            return response()->json([
                'tasks_count' => \App\Models\KanbanTask::count(),
                'issues_count' => \App\Models\Issue::count(),
                'issues_with_task' => \App\Models\Issue::whereNotNull('linked_task_id')->count(),
                'tasks' => $tasks,
                'issues' => $issues
            ]);
        });
        
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\RiskController::class, 'dashboard'])->name('dashboard');
        
        // Risks
        Route::get('/', [App\Http\Controllers\RiskController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\RiskController::class, 'store'])->name('store');
        Route::put('/{risk}', [App\Http\Controllers\RiskController::class, 'update'])->name('update');
        Route::delete('/{risk}', [App\Http\Controllers\RiskController::class, 'destroy'])->name('destroy');
        Route::post('/{risk}/mark-mitigated', [App\Http\Controllers\RiskController::class, 'markMitigated'])->name('mark-mitigated');
        Route::post('/{risk}/convert-to-issue', [App\Http\Controllers\RiskController::class, 'convertToIssue'])->name('convert-to-issue');
        
        // Issues
        Route::get('/issues', [App\Http\Controllers\IssueController::class, 'index'])->name('issues');
        
        // Test issue endpoint without model binding
        Route::get('/issues/test/{id}', function ($id) {
            $issue = \App\Models\Issue::where('issue_id', $id)->first();
            if (!$issue) {
                return response()->json(['error' => 'Issue not found', 'id' => $id], 404);
            }
            return response()->json(['success' => true, 'data' => $issue->load(['workspace', 'assignee.user', 'creator'])]);
        });
        
        Route::get('/issues/{issue}', [App\Http\Controllers\IssueController::class, 'show'])->name('issues.show');
        Route::post('/issues', [App\Http\Controllers\IssueController::class, 'store'])->name('issues.store');
        Route::put('/issues/{issue}', [App\Http\Controllers\IssueController::class, 'update'])->name('issues.update');
        Route::delete('/issues/{issue}', [App\Http\Controllers\IssueController::class, 'destroy'])->name('issues.destroy');
        Route::post('/issues/{issue}/status', [App\Http\Controllers\IssueController::class, 'updateStatus'])->name('issues.status');
        Route::post('/issues/{issue}/comment', [App\Http\Controllers\IssueController::class, 'addComment'])->name('issues.comment');
        Route::post('/issues/{issue}/resolve', [App\Http\Controllers\IssueController::class, 'resolve'])->name('issues.resolve');
        
        // Change Requests
        Route::get('/change-requests', [App\Http\Controllers\ChangeRequestController::class, 'index'])->name('change-requests');
        Route::post('/change-requests', [App\Http\Controllers\ChangeRequestController::class, 'store'])->name('change-requests.store');
        Route::post('/change-requests/{changeRequest}/approve', [App\Http\Controllers\ChangeRequestController::class, 'approve'])->name('change-requests.approve');
        Route::post('/change-requests/{changeRequest}/reject', [App\Http\Controllers\ChangeRequestController::class, 'reject'])->name('change-requests.reject');
        Route::post('/change-requests/{changeRequest}/implement', [App\Http\Controllers\ChangeRequestController::class, 'implement'])->name('change-requests.implement');
        Route::delete('/change-requests/{changeRequest}', [App\Http\Controllers\ChangeRequestController::class, 'destroy'])->name('change-requests.destroy');
    });
    
});

// Breeze auth routes
require __DIR__.'/auth.php';
