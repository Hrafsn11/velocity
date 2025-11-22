<?php

namespace App\Services;

use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class EmployeeService
{
    /**
     * Get all employees with their profiles and user data.
     */
    public function getAllEmployees()
    {
        return User::with('employeeProfile')
            ->whereHas('employeeProfile')
            ->latest()
            ->get()
            ->map(function ($user) {
                $profile = $user->employeeProfile;

                return [
                    // employee_id comes from the related employee profile (ULID)
                    'employee_id' => $profile->employee_id ?? null,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar && file_exists(storage_path('app/public/' . $user->avatar)) ? $user->avatar : null,
                    'role' => $profile->role ?? null,
                    'specialization' => $profile->specialization ?? null,
                    'level' => $profile->level ?? null,
                    'skills' => $profile->skills ?? [],
                    'status' => $profile->status ?? null,
                    'phone' => $profile->phone ?? null,
                    'role_icon' => $profile?->role_icon,
                    'level_color' => $profile?->level_color,
                    'status_badge' => $profile?->status_badge,
                    'projects_count' => 0, // Will be implemented when project module is added
                ];
            });
    }

    /**
     * Get employee statistics.
     */
    public function getEmployeeStats(): array
    {
        $total = EmployeeProfile::count();
        $available = EmployeeProfile::where('status', 'available')->count();
        $unavailable = EmployeeProfile::where('status', 'unavailable')->count();

        return [
            'total' => $total,
            'available' => $available,
            'offline' => $unavailable,
            'busy' => 0, // Will be calculated based on workload when project module is added
        ];
    }

    /**
     * Create a new employee with user account.
     */
    public function createEmployee(array $data): User
    {
        try {
            DB::beginTransaction();

            // Check if email already exists
            if (User::where('email', $data['email'])->exists()) {
                throw new \Exception('Email address already exists in the system.');
            }

            // Store plain password for email
            $plainPassword = $data['password'];

            // Create user account
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($plainPassword),
                'avatar' => null,
                'email_verified_at' => now(),
            ]);

            // Parse skills from comma-separated string to array
            $skills = array_map('trim', explode(',', $data['skills']));

            // Create employee profile
            $user->employeeProfile()->create([
                'role' => $data['role'],
                'specialization' => $data['specialization'],
                'level' => $data['level'],
                'skills' => $skills,
                'status' => $data['status'],
                'phone' => $data['phone'] ?? null,
            ]);

            // Assign Employee role
            $user->assignRole('Employee');

            // Send welcome email with credentials
            $user->notify(new \App\Notifications\WelcomeUserNotification($plainPassword, true));

            DB::commit();

            return $user->fresh(['employeeProfile']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing employee.
     */
    public function updateEmployee(User $user, array $data): User
    {
        try {
            DB::beginTransaction();

            // Update user data (NO PASSWORD UPDATE - managed separately)
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            $user->update($userData);

            // Parse skills from comma-separated string to array
            $skills = array_map('trim', explode(',', $data['skills']));

            // Update employee profile
            $user->employeeProfile->update([
                'role' => $data['role'],
                'specialization' => $data['specialization'],
                'level' => $data['level'],
                'skills' => $skills,
                'status' => $data['status'],
                'phone' => $data['phone'] ?? null,
            ]);

            DB::commit();

            return $user->fresh(['employeeProfile']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete an employee and their user account.
     */
    public function deleteEmployee(User $user): bool
    {
        try {
            DB::beginTransaction();

            // Delete employee profile (will cascade due to FK constraint)
            // Then delete user account
            $user->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Find employee by user ID.
     */
    /**
     * Find employee by employee_id (ULID) and return the related User with profile.
     */
    public function findEmployee(string $employee_id): ?User
    {
        $profile = EmployeeProfile::with('user')->where('employee_id', $employee_id)->first();

        if (!$profile || !$profile->user) {
            return null;
        }

        return $profile->user->load('employeeProfile');
    }
}
