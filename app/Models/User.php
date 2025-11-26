<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'last_login_at',
        'account_status',
        'login_count',
        'suspended_at',
        'suspended_reason',
        'must_change_password',
        'password_changed_at',
        'temporary_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class, 'user_id', 'user_id');
    }

    /**
     * Check if the user account is active.
     */
    public function isActive(): bool
    {
        return $this->account_status === 'active';
    }

    /**
     * Check if the user account is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->account_status === 'suspended';
    }

    /**
     * Suspend the user account.
     */
    public function suspend(string $reason = null): void
    {
        $this->update([
            'account_status' => 'suspended',
            'suspended_at' => now(),
            'suspended_reason' => $reason,
        ]);
    }

    /**
     * Activate the user account.
     */
    public function activate(): void
    {
        $this->update([
            'account_status' => 'active',
            'suspended_at' => null,
            'suspended_reason' => null,
        ]);
    }

    /**
     * Record a login activity.
     */
    public function recordLogin(): void
    {
        $this->increment('login_count');
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get account status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->account_status) {
            'active' => 'bg-label-success',
            'inactive' => 'bg-label-secondary',
            'suspended' => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Check if user is an employee (has employee profile).
     */
    public function isEmployee(): bool
    {
        return $this->employeeProfile()->exists();
    }

    /**
     * Get user type for display.
     */
    public function getUserTypeAttribute(): string
    {
        return $this->isEmployee() ? 'Employee' : 'System User';
    }

    /**
     * Get user type badge.
     */
    public function getUserTypeBadgeAttribute(): string
    {
        return $this->isEmployee() ? '🧑‍💻' : '👤';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        $avatar = $this->avatar;
        if (empty($avatar)) {
            return null;
        }

        try {
            if (Storage::disk('public')->exists($avatar)) {
                return asset('storage/' . ltrim($avatar, '/'));
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $assetPath = public_path('assets/img/avatars/' . $avatar);
        if (file_exists($assetPath)) {
            return asset('assets/img/avatars/' . $avatar);
        }

        return null;
    }
}
