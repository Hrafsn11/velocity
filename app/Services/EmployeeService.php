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
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? '/assets/img/avatars/1.png',
                    'role' => $user->employeeProfile->role,
                    'specialization' => $user->employeeProfile->specialization,
                    'level' => $user->employeeProfile->level,
                    'skills' => $user->employeeProfile->skills,
                    'status' => $user->employeeProfile->status,
                    'phone' => $user->employeeProfile->phone,
                    'role_icon' => $user->employeeProfile->role_icon,
                    'level_color' => $user->employeeProfile->level_color,
                    'status_badge' => $user->employeeProfile->status_badge,
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

            // Create user account
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'avatar' => '/assets/img/avatars/1.png',
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

            // Assign default Employee role
            $user->assignRole('Employee');

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

            // Update user data
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            // Update password if provided
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

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
    public function findEmployee(int $userId): ?User
    {
        return User::with('employeeProfile')->find($userId);
    }
}
