<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeRequest extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'change_request_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'workspace_id',
        'issue_id',
        'code',
        'title',
        'description',
        'type',
        'timeline_extension_days',
        'current_end_date',
        'proposed_end_date',
        'members_to_add',
        'members_to_remove',
        'justification',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'requested_by',
    ];

    protected $casts = [
        'timeline_extension_days' => 'integer',
        'current_end_date' => 'date',
        'proposed_end_date' => 'date',
        'members_to_add' => 'array',
        'members_to_remove' => 'array',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'issue_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(RiskActivity::class, 'subject')->latest();
    }

    // Accessors
    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'timeline' => 'bg-label-info',
            'resource' => 'bg-label-success',
            default => 'bg-label-secondary',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-label-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'implemented' => 'bg-label-success',
            default => 'bg-label-secondary',
        };
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeImplemented(): bool
    {
        return $this->status === 'approved';
    }

    public function isTimelineType(): bool
    {
        return $this->type === 'timeline';
    }

    public function isResourceType(): bool
    {
        return $this->type === 'resource';
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'change_request_id';
    }
}
