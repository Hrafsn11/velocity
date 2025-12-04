<?php

namespace App\Models;

use App\Enums\WorkspaceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'workspace_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'manager_id',
        'status',
        'image_path',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => WorkspaceStatus::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'workspace_id';
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'manager_id', 'employee_id')
            ->with('user');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeProfile::class, 'workspace_members', 'workspace_id', 'employee_id')
            ->withTimestamps();
    }

    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class, 'workspace_id', 'workspace_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'workspace_id', 'workspace_id');
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'workspace_id', 'workspace_id');
    }

    public function kanbanTasks(): HasMany
    {
        return $this->hasMany(KanbanTask::class, 'workspace_id', 'workspace_id');
    }
}

