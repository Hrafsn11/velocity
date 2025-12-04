<?php

namespace App\Models;

use App\Enums\IssueStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Issue Model
 * 
 * Represents an issue that needs to be resolved, can be created from a materialized risk.
 * 
 * @property string $issue_id Primary key (ULID)
 * @property string $workspace_id Foreign key to workspace
 * @property string|null $risk_id Foreign key to risk (if issue is from risk)
 * @property string|null $linked_task_id Foreign key to linked kanban task
 * @property string $code Human-readable code (e.g., IS001)
 * @property string $title Issue title
 * @property string $description Issue description
 * @property int $priority Priority level (1-5, 5 is highest)
 * @property int $severity Severity level (1-5, 5 is highest)
 * @property string $status Current status (open, in_progress, resolved, closed, reopened)
 * @property string|null $assignee_id Assigned employee ID
 * @property \Carbon\Carbon|null $deadline Deadline for resolution
 * @property \Carbon\Carbon|null $resolved_at Timestamp when resolved
 * @property string|null $resolved_by User ID who resolved the issue
 * @property string $created_by User ID who created the issue
 * @property-read Workspace $workspace
 * @property-read Risk|null $risk
 * @property-read EmployeeProfile|null $assignee
 * @property-read User $creator
 * @property-read User|null $resolver
 * @property-read KanbanTask|null $linkedTask
 * @property-read \Illuminate\Database\Eloquent\Collection|ChangeRequest[] $changeRequests
 * @property-read \Illuminate\Database\Eloquent\Collection|IssueComment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|IssueAttachment[] $attachments
 */
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
    
    /**
     * Get priority badge CSS class
     */
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

    /**
     * Get status badge CSS class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'open' => 'bg-label-warning',
            'in_progress' => 'bg-label-info',
            'resolved' => 'bg-label-success',
            'closed' => 'bg-label-secondary',
            'reopened' => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'open' => 'ti-circle-dot',
            'in_progress' => 'ti-progress',
            'resolved' => 'ti-check-circle',
            'closed' => 'ti-circle-x',
            'reopened' => 'ti-refresh-alert',
            default => 'ti-circle',
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            5 => 'Critical',
            4 => 'High',
            3 => 'Medium',
            2 => 'Low',
            1 => 'Very Low',
            default => 'Unknown',
        };
    }

    // Query Scopes
    
    /**
     * Scope to filter issues by workspace
     */
    public function scopeForWorkspace($query, string $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope to get only open issues
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope to get issues in progress
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get resolved issues
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope to get active issues (open, in progress, reopened)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'reopened']);
    }

    /**
     * Scope to get issues from risks
     */
    public function scopeFromRisk($query)
    {
        return $query->whereNotNull('risk_id');
    }

    /**
     * Scope to get overdue issues
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope to get high priority issues (priority >= 4)
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 4);
    }

    /**
     * Scope to get assigned issues
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assignee_id');
    }

    /**
     * Scope to get unassigned issues
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assignee_id');
    }

    /**
     * Scope to order by priority (highest first)
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    // Helper Methods
    
    /**
     * Check if issue was created from a risk
     */
    public function isFromRisk(): bool
    {
        return !is_null($this->risk_id);
    }

    /**
     * Check if change requests can be created for this issue
     */
    public function canCreateChangeRequest(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }

    /**
     * Check if issue is overdue
     */
    public function isOverdue(): bool
    {
        return $this->deadline 
            && $this->deadline->isPast() 
            && !in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Check if issue has a linked task
     */
    public function hasLinkedTask(): bool
    {
        return !is_null($this->linked_task_id);
    }

    /**
     * Check if issue is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved' && !is_null($this->resolved_at);
    }

    /**
     * Check if issue is closed
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Check if work can be started on this issue
     * Only open and reopened issues can be started
     */
    public function canStartWorking(): bool
    {
        return in_array($this->status, ['open', 'reopened']);
    }

    /**
     * Check if issue is high priority (4 or 5)
     */
    public function isHighPriority(): bool
    {
        return $this->priority >= 4;
    }

    /**
     * Check if issue is assigned
     */
    public function isAssigned(): bool
    {
        return !is_null($this->assignee_id);
    }

    /**
     * Get the route key for the model (for route model binding)
     */
    public function getRouteKeyName(): string
    {
        return 'issue_id';
    }
}
