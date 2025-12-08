<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Change Request Model
 * 
 * Represents a formal request for project changes (timeline extension or resource modification).
 * 
 * @property string $change_request_id Primary key (ULID)
 * @property string $workspace_id Foreign key to workspace
 * @property string $issue_id Foreign key to issue
 * @property string $code Human-readable code (e.g., CR001)
 * @property string $title Change request title
 * @property string $description Description of requested change
 * @property string $type Type of change (timeline, resource)
 * @property int|null $timeline_extension_days Days to extend (for timeline type)
 * @property \Carbon\Carbon|null $current_end_date Current project end date
 * @property \Carbon\Carbon|null $proposed_end_date Proposed new end date
 * @property array|null $members_to_add Employee IDs to add (for resource type)
 * @property array|null $members_to_remove Employee IDs to remove (for resource type)
 * @property string $justification Justification for the change
 * @property string $status Request status (pending, approved, rejected, implemented)
 * @property string|null $approved_by User ID who approved/rejected
 * @property \Carbon\Carbon|null $approved_at Timestamp when approved/rejected
 * @property string|null $rejection_reason Reason for rejection (if rejected)
 * @property string $requested_by User ID who created the request
 * @property-read Workspace $workspace
 * @property-read Issue $issue
 * @property-read User $requester
 * @property-read User|null $approver
 */
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
    
    /**
     * Get type badge CSS class
     */
    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'timeline' => 'bg-label-info',
            'resource' => 'bg-label-success',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get status badge CSS class
     */
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

    /**
     * Get type icon
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'timeline' => 'ti-clock',
            'resource' => 'ti-users',
            default => 'ti-file',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'pending' => 'ti-clock',
            'approved' => 'ti-check-circle',
            'rejected' => 'ti-circle-x',
            'implemented' => 'ti-check',
            default => 'ti-circle',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'timeline' => 'Timeline Extension',
            'resource' => 'Resource Modification',
            default => 'Unknown',
        };
    }

    // Query Scopes
    
    /**
     * Scope to filter by workspace
     */
    public function scopeForWorkspace($query, string $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope to get pending change requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved change requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected change requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get implemented change requests
     */
    public function scopeImplemented($query)
    {
        return $query->where('status', 'implemented');
    }

    /**
     * Scope to get timeline type change requests
     */
    public function scopeTimelineType($query)
    {
        return $query->where('type', 'timeline');
    }

    /**
     * Scope to get resource type change requests
     */
    public function scopeResourceType($query)
    {
        return $query->where('type', 'resource');
    }

    /**
     * Scope to get change requests for a specific issue
     */
    public function scopeForIssue($query, string $issueId)
    {
        return $query->where('issue_id', $issueId);
    }

    // Helper Methods
    
    /**
     * Check if change request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if change request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if change request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if change request has been implemented
     */
    public function isImplemented(): bool
    {
        return $this->status === 'implemented';
    }

    /**
     * Check if change request can be implemented
     * Only approved requests can be implemented
     */
    public function canBeImplemented(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if change request can be approved/rejected
     * Only pending requests can be approved or rejected
     */
    public function canBeReviewed(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if change request is timeline type
     */
    public function isTimelineType(): bool
    {
        return $this->type === 'timeline';
    }

    /**
     * Check if change request is resource type
     */
    public function isResourceType(): bool
    {
        return $this->type === 'resource';
    }

    /**
     * Get number of members to add
     */
    public function getMembersToAddCount(): int
    {
        return $this->members_to_add ? count($this->members_to_add) : 0;
    }

    /**
     * Get number of members to remove
     */
    public function getMembersToRemoveCount(): int
    {
        return $this->members_to_remove ? count($this->members_to_remove) : 0;
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'change_request_id';
    }
}
