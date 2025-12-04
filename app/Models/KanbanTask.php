<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KanbanTask extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'task_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'board_id',
        'workspace_id',
        'created_by',
        'title',
        'description',
        'priority',
        'label',
        'due_date',
        'position',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(KanbanBoard::class, 'board_id', 'board_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeProfile::class, 'kanban_task_assignees', 'task_id', 'employee_id')
            ->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(KanbanTaskAttachment::class, 'task_id', 'task_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(KanbanTaskComment::class, 'task_id', 'task_id')
            ->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(KanbanTaskActivity::class, 'task_id', 'task_id')
            ->with('user')
            ->latest();
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'bg-danger',
            'high' => 'bg-warning',
            'medium' => 'bg-info',
            'low' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getLabelBadgeAttribute(): string
    {
        return match($this->label) {
            'UX' => 'bg-success',
            'Images' => 'bg-warning',
            'Info' => 'bg-info',
            'Code Review' => 'bg-danger',
            'App' => 'bg-secondary',
            'Charts & Maps' => 'bg-primary',
            'Feature' => 'bg-success',
            'Bug' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'linked_task_id', 'task_id');
    }

    public function hasActiveIssues(): bool
    {
        return $this->issues()->whereNotIn('status', ['resolved', 'closed'])->exists();
    }

    public function activeIssuesCount(): int
    {
        return $this->issues()->whereNotIn('status', ['resolved', 'closed'])->count();
    }
}
