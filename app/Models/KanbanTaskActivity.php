<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanTaskActivity extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'activity_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'description',
        'old_value',
        'new_value',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(KanbanTask::class, 'task_id', 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'ti-plus-circle',
            'updated' => 'ti-edit',
            'moved' => 'ti-arrows-move',
            'assigned' => 'ti-user-plus',
            'unassigned' => 'ti-user-minus',
            'commented' => 'ti-message',
            'attached' => 'ti-paperclip',
            'deleted_attachment' => 'ti-trash',
            'priority_changed' => 'ti-flag',
            'label_changed' => 'ti-tag',
            'due_date_changed' => 'ti-calendar',
            'archived' => 'ti-archive',
            'restored' => 'ti-archive-off',
            // Issue tracking icons
            'issue_linked' => 'ti-alert-triangle',
            'issue_resolved' => 'ti-check-circle',
            'issue_closed' => 'ti-circle-x',
            'issue_reopened' => 'ti-refresh-alert',
            'issue_status_changed' => 'ti-exchange',
            default => 'ti-activity',
        };
    }

    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'moved' => 'primary',
            'assigned' => 'success',
            'unassigned' => 'warning',
            'commented' => 'info',
            'attached' => 'primary',
            'deleted_attachment' => 'danger',
            'priority_changed' => 'warning',
            'label_changed' => 'info',
            'due_date_changed' => 'warning',
            'archived' => 'secondary',
            'restored' => 'success',
            // Issue tracking colors
            'issue_linked' => 'danger',
            'issue_resolved' => 'success',
            'issue_closed' => 'secondary',
            'issue_reopened' => 'warning',
            'issue_status_changed' => 'info',
            default => 'secondary',
        };
    }
}
