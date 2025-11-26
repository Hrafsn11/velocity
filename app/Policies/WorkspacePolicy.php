<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use App\Models\EmployeeProfile;

class WorkspacePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Workspace $workspace): bool
    {
        if (!$user->hasRole('Employee')) {
            return true;
        }

        $profile = EmployeeProfile::where('user_id', $user->user_id)->first();
        if (!$profile) {
            return false;
        }

        if ($workspace->manager_id === $profile->employee_id) {
            return true;
        }

        return $workspace->members()->where('employee_profiles.employee_id', $profile->employee_id)->exists();
    }

    public function create(User $user): bool
    {
        return !$user->hasRole('Employee');
    }

    public function update(User $user, Workspace $workspace): bool
    {
        return !$user->hasRole('Employee');
    }

    public function delete(User $user, Workspace $workspace): bool
    {
        return !$user->hasRole('Employee');
    }
}
