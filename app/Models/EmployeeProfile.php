<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeProfile extends Model
{
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
        return $this->belongsTo(User::class);
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
}
