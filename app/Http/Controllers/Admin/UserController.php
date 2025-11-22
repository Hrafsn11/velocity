<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->withCount('roles')
            ->latest('last_login_at')
            ->get();
        
        $stats = [
            'total' => User::count(),
            'active' => User::where('account_status', 'active')->count(),
            'suspended' => User::where('account_status', 'suspended')->count(),
            'inactive' => User::where('account_status', 'inactive')->count(),
        ];
        
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'stats', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'roles' => 'nullable|array',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);

            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'data' => [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'account_status' => $user->account_status,
                    'status_badge' => $user->status_badge,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at,
                    'login_count' => $user->login_count,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->toArray(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
                'roles' => 'nullable|array',
            ]);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'data' => [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'account_status' => $user->account_status,
                    'status_badge' => $user->status_badge,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at,
                    'login_count' => $user->login_count,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Suspend a user account.
     */
    public function suspend(Request $request, User $user)
    {
        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'Cannot suspend your own account.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user->suspend($validated['reason']);

        return response()->json([
            'success' => true,
            'message' => 'User account suspended successfully.',
        ]);
    }

    /**
     * Activate a user account.
     */
    public function activate(User $user)
    {
        $user->activate();

        return response()->json([
            'success' => true,
            'message' => 'User account activated successfully.',
        ]);
    }

    /**
     * Reset user password by generating a temporary password.
     */
    public function resetPassword(User $user)
    {
        try {
            if ($user->user_id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reset your own password. Use profile settings instead.',
                ], 403);
            }

            // Generate random temporary password
            $temporaryPassword = Str::random(12);

            // Update user with temporary password
            $user->update([
                'password' => Hash::make($temporaryPassword),
                'password_changed_at' => now(),
            ]);

            // Send notification with temporary password
            $user->notify(new ResetPasswordNotification($temporaryPassword));

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. A temporary password has been sent to the user\'s email.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage(),
            ], 500);
        }
    }
}
