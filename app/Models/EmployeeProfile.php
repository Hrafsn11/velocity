<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeProfile extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'employee_profiles';

    protected $primaryKey = 'employee_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'role',
        'specialization',
        'level',
        'skills',
        'status',
        'phone',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    /**
     * Get the user that owns the employee profile.
     */
    public function user(): BelongsTo
    {
        // Specify the foreign key on this table and the owner's key on users table.
        // This ensures Eloquent sets `user_id` (not `user_user_id`) when creating via relation.
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the workspaces this employee is a member of.
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_members', 'employee_id', 'workspace_id')
            ->withTimestamps();
    }

    /**
     * Get the role label with icon.
     */
    public function getRoleIconAttribute(): string
    {
        return match ($this->role) {
            'programmer' => 'ti-code',
            'designer' => 'ti-palette',
            'qa' => 'ti-bug',
            'analyst' => 'ti-chart-dots',
            'manager' => 'ti-briefcase',
            default => 'ti-user',
        };
    }

    /**
     * Get the level badge color.
     */
    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            'intern' => 'text-info',
            'junior' => 'text-success',
            'middle' => 'text-primary',
            'senior' => 'text-warning',
            'lead' => 'text-danger',
            default => 'text-secondary',
        };
    }

    /**
     * Get the level badge background class for UI badges.
     */
    public function getLevelBadgeAttribute(): string
    {
        return match ($this->level) {
            'intern' => 'bg-label-info',
            'junior' => 'bg-label-success',
            'middle' => 'bg-label-primary',
            'senior' => 'bg-label-warning',
            'lead' => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get the role badge background class for UI badges.
     */
    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'programmer' => 'bg-label-success',
            'designer' => 'bg-label-primary',
            'qa' => 'bg-label-warning',
            'analyst' => 'bg-label-info',
            'manager' => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available' => 'bg-label-success',
            'unavailable' => 'bg-label-secondary',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get workload level based on number of workspaces.
     * 0-1: Low, 2-3: Medium, 4-5: High, 6+: Overload
     */
    public function getWorkloadLevelAttribute(): string
    {
        $count = $this->workspaces()->count();
        
        return match (true) {
            $count >= 6 => 'Overload',
            $count >= 4 => 'High',
            $count >= 2 => 'Medium',
            default => 'Low',
        };
    }

    /**
     * Get workload badge class based on level.
     */
    public function getWorkloadBadgeAttribute(): string
    {
        $level = $this->workload_level;
        
        return match ($level) {
            'Overload' => 'bg-label-danger',
            'High' => 'bg-label-warning',
            'Medium' => 'bg-label-info',
            'Low' => 'bg-label-success',
            default => 'bg-label-secondary',
        };
    }
}
