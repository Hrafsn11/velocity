<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    // Accessors
    public function getScoreAttribute(): int
    {
        return $this->probability * $this->impact;
    }

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

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-label-success',
            'monitoring' => 'bg-label-info',
            'mitigated' => 'bg-label-success',
            'materialized' => 'bg-label-danger',
            'closed' => 'bg-label-secondary',
            default => 'bg-label-secondary',
        };
    }

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

    // Methods
    public function canConvertToIssue(): bool
    {
        // Only active and monitoring risks can be converted (not already materialized)
        return in_array($this->status, ['active', 'monitoring']);
    }

    public function isCritical(): bool
    {
        return $this->urgency === 'Critical';
    }

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
