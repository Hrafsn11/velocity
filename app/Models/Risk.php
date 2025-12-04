<?php

namespace App\Models;

use App\Enums\RiskStatus;
use App\Enums\RiskUrgency;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Risk Model
 * 
 * Represents a risk identified in a workspace that could impact project success.
 * 
 * @property string $risk_id Primary key (ULID)
 * @property string $workspace_id Foreign key to workspace
 * @property string $code Human-readable code (e.g., R001)
 * @property string $description Risk description
 * @property string|null $cause Root cause or reason for the risk
 * @property string $category Risk category (Technical, SDM, Financial, Timeline)
 * @property string|null $affected_module Affected task/module ID
 * @property int $probability Probability score (1-5)
 * @property int $impact Impact score (1-5)
 * @property string $urgency Urgency level (Critical, High, Medium, Low)
 * @property string $status Current status
 * @property string|null $mitigation_actions Actions to mitigate the risk
 * @property string $created_by User ID who created the risk
 * @property int $score Calculated risk score (probability × impact)
 * @property-read Workspace $workspace
 * @property-read User $creator
 * @property-read KanbanTask|null $affectedTask
 * @property-read \Illuminate\Database\Eloquent\Collection|Issue[] $issues
 */
class Risk extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'risk_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'workspace_id',
        'code',
        'description',
        'cause',
        'category',
        'affected_module',
        'probability',
        'impact',
        'urgency',
        'status',
        'mitigation_actions',
        'created_by',
    ];

    protected $casts = [
        'probability' => 'integer',
        'impact' => 'integer',
        'score' => 'integer',
    ];

    protected $appends = ['score'];

    // Relationships
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function affectedTask(): BelongsTo
    {
        return $this->belongsTo(KanbanTask::class, 'affected_module', 'task_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'risk_id', 'risk_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(RiskActivity::class, 'subject')->latest();
    }

    // Accessors & Mutators
    
    /**
     * Get calculated risk score (probability × impact)
     */
    public function getScoreAttribute(): int
    {
        return $this->probability * $this->impact;
    }

    /**
     * Get urgency badge CSS class based on urgency level
     */
    public function getUrgencyBadgeAttribute(): string
    {
        return match($this->urgency) {
            'Critical' => 'bg-danger',
            'High' => 'bg-warning',
            'Medium' => 'bg-info',
            'Low' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status badge CSS class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-label-danger',
            'monitoring' => 'bg-label-primary',
            'mitigating' => 'bg-label-warning',
            'analyzing' => 'bg-label-info',
            'mitigated' => 'bg-label-success',
            'materialized' => 'bg-danger',
            'identified' => 'bg-label-secondary',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get category badge CSS class
     */
    public function getCategoryBadgeAttribute(): string
    {
        return match($this->category) {
            'Technical' => 'bg-label-primary',
            'SDM' => 'bg-label-warning',
            'Financial' => 'bg-label-success',
            'Timeline' => 'bg-label-info',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'active' => 'ti-alert-triangle',
            'monitoring' => 'ti-eye-check',
            'mitigating' => 'ti-shield',
            'analyzing' => 'ti-search',
            'mitigated' => 'ti-shield-check',
            'materialized' => 'ti-alert-octagon',
            'identified' => 'ti-eye',
            default => 'ti-circle',
        };
    }

    /**
     * Get urgency icon
     */
    public function getUrgencyIconAttribute(): string
    {
        return match($this->urgency) {
            'Critical' => 'ti-alert-triangle',
            'High' => 'ti-alert-circle',
            'Medium' => 'ti-info-circle',
            'Low' => 'ti-check-circle',
            default => 'ti-circle',
        };
    }

    // Query Scopes
    
    /**
     * Scope to filter risks by workspace
     */
    public function scopeForWorkspace($query, string $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope to get only active risks
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only critical risks (urgency = Critical)
     */
    public function scopeCritical($query)
    {
        return $query->where('urgency', 'Critical');
    }

    /**
     * Scope to get high priority risks (Critical or High urgency)
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('urgency', ['Critical', 'High']);
    }

    /**
     * Scope to get mitigated risks
     */
    public function scopeMitigated($query)
    {
        return $query->where('status', 'mitigated');
    }

    /**
     * Scope to get risks by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get risks by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by urgency (Critical first)
     */
    public function scopeOrderByUrgency($query)
    {
        return $query->orderByRaw("
            CASE urgency
                WHEN 'Critical' THEN 1
                WHEN 'High' THEN 2
                WHEN 'Medium' THEN 3
                WHEN 'Low' THEN 4
                ELSE 5
            END
        ");
    }

    /**
     * Scope to order by score (highest first)
     */
    public function scopeOrderByScore($query)
    {
        return $query->orderByRaw('probability * impact DESC');
    }

    // Helper Methods
    
    /**
     * Check if risk can be converted to issue
     * Only active and monitoring risks can be converted (not already materialized)
     */
    public function canConvertToIssue(): bool
    {
        return in_array($this->status, ['active', 'monitoring']);
    }

    /**
     * Check if risk is critical
     */
    public function isCritical(): bool
    {
        return $this->urgency === 'Critical';
    }

    /**
     * Check if risk is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if risk is mitigated
     */
    public function isMitigated(): bool
    {
        return $this->status === 'mitigated';
    }

    /**
     * Check if risk has materialized
     */
    public function hasMaterialized(): bool
    {
        return $this->status === 'materialized';
    }

    /**
     * Calculate urgency based on score
     */
    public function calculateUrgency(): string
    {
        $score = $this->getScoreAttribute();
        
        return match(true) {
            $score >= 20 => 'Critical',
            $score >= 15 => 'High',
            $score >= 10 => 'Medium',
            default => 'Low',
        };
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'risk_id';
    }
}
