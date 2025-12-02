<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KanbanBoard extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $primaryKey = 'board_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'workspace_id',
        'title',
        'color',
        'position',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'workspace_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(KanbanTask::class, 'board_id', 'board_id')
            ->orderBy('position');
    }

    public function getColorBadgeAttribute(): string
    {
        return match($this->color) {
            '#6366f1' => 'bg-primary',
            '#10b981' => 'bg-success',
            '#f59e0b' => 'bg-warning',
            '#ef4444' => 'bg-danger',
            '#8b5cf6' => 'bg-info',
            default => 'bg-secondary',
        };
    }
}
