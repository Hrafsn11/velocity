<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'issue_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'workspace_id',
        'risk_id',
        'linked_task_id',
        'code',
        'title',
        'description',
        'priority',
        'severity',
        'status',
        'assignee_id',
        'deadline',
        'resolved_at',
        'resolved_by',
        'created_by',
    ];

    protected $casts = [
        'priority' => 'integer',
        'severity' => 'integer',
        'deadline' => 'date',
        'resolved_at' => 'datetime',
    ];

    // Relationships
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class, 'risk_id', 'risk_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'assignee_id', 'employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'issue_id', 'issue_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(RiskActivity::class, 'subject')->latest();
    }

    public function linkedTask(): BelongsTo
    {
        return $this->belongsTo(KanbanTask::class, 'linked_task_id', 'task_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(IssueComment::class, 'issue_id', 'issue_id')->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IssueAttachment::class, 'issue_id', 'issue_id');
    }

    // Accessors
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            5 => 'bg-danger',
            4 => 'bg-warning',
            3 => 'bg-info',
            2 => 'bg-success',
            1 => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'open' => 'bg-label-danger',
            'in_progress' => 'bg-label-warning',
            'resolved' => 'bg-label-success',
            'closed' => 'bg-label-secondary',
            'reopened' => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }

    // Methods
    public function isFromRisk(): bool
    {
        return !is_null($this->risk_id);
    }

    public function canCreateChangeRequest(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && !in_array($this->status, ['resolved', 'closed']);
    }

    public function hasLinkedTask(): bool
    {
        return !is_null($this->linked_task_id);
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved' && !is_null($this->resolved_at);
    }

    public function canStartWorking(): bool
    {
        // Only open and reopened issues can be started
        return in_array($this->status, ['open', 'reopened']);
    }

    /**
     * Get the route key for the model (for route model binding)
     */
    public function getRouteKeyName(): string
    {
        return 'issue_id';
    }
}
