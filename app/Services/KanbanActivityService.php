<?php

namespace App\Services;

use App\Models\KanbanTask;
use App\Models\KanbanTaskActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * Kanban Activity Service
 * 
 * Handles logging and retrieval of activities for Kanban tasks.
 * Tracks task creation, updates, moves, assignments, comments, attachments,
 * and issue-related activities.
 * 
 * @package App\Services
 */
class KanbanActivityService
{
    /**
     * Log task creation activity
     * 
     * @param KanbanTask $task The created task
     * @return void
     */
    public function logTaskCreated(KanbanTask $task): void
    {
        $this->log($task, 'created', "Created task \"{$task->title}\"");
    }

    public function logTaskUpdated(KanbanTask $task, array $changes): void
    {
        foreach ($changes as $field => $values) {
            $description = $this->getUpdateDescription($field, $values['old'], $values['new']);
            
            $this->log($task, 'updated', $description, [
                'field' => $field,
                'value' => $values['old']
            ], [
                'field' => $field,
                'value' => $values['new']
            ]);
        }
    }

    public function logTaskMoved(KanbanTask $task, string $fromBoard, string $toBoard): void
    {
        $this->log(
            $task,
            'moved',
            "Moved task from \"{$fromBoard}\" to \"{$toBoard}\"",
            ['board' => $fromBoard],
            ['board' => $toBoard]
        );
    }

    public function logAssigned(KanbanTask $task, string $assigneeName): void
    {
        $this->log($task, 'assigned', "Assigned to {$assigneeName}");
    }

    public function logUnassigned(KanbanTask $task, string $assigneeName): void
    {
        $this->log($task, 'unassigned', "Unassigned from {$assigneeName}");
    }

    public function logCommented(KanbanTask $task): void
    {
        $this->log($task, 'commented', 'Added a comment');
    }

    public function logAttachmentAdded(KanbanTask $task, string $fileName): void
    {
        $this->log($task, 'attached', "Attached file: {$fileName}");
    }

    public function logAttachmentDeleted(KanbanTask $task, string $fileName): void
    {
        $this->log($task, 'deleted_attachment', "Removed file: {$fileName}");
    }

    public function logPriorityChanged(KanbanTask $task, string $oldPriority, string $newPriority): void
    {
        $this->log(
            $task,
            'priority_changed',
            "Changed priority from {$oldPriority} to {$newPriority}",
            ['priority' => $oldPriority],
            ['priority' => $newPriority]
        );
    }

    public function logLabelChanged(KanbanTask $task, ?string $oldLabel, ?string $newLabel): void
    {
        $old = $oldLabel ?? 'none';
        $new = $newLabel ?? 'none';
        
        $this->log(
            $task,
            'label_changed',
            "Changed label from {$old} to {$new}",
            ['label' => $oldLabel],
            ['label' => $newLabel]
        );
    }

    public function logDueDateChanged(KanbanTask $task, ?string $oldDate, ?string $newDate): void
    {
        $old = $oldDate ?? 'no due date';
        $new = $newDate ?? 'no due date';
        
        $this->log(
            $task,
            'due_date_changed',
            "Changed due date from {$old} to {$new}",
            ['due_date' => $oldDate],
            ['due_date' => $newDate]
        );
    }

    // Issue Tracking Activities
    
    /**
     * Log when an issue is linked to a task
     * 
     * @param KanbanTask $task The task the issue is linked to
     * @param string $issueCode The issue code (e.g., ISS001)
     * @param string $issueTitle The issue title
     * @return void
     */
    public function logIssueLinked(KanbanTask $task, string $issueCode, string $issueTitle): void
    {
        $this->log(
            $task, 
            'issue_linked', 
            "Issue {$issueCode} linked: {$issueTitle}",
            null,
            ['issue_code' => $issueCode, 'issue_title' => $issueTitle]
        );
    }

    public function logIssueResolved(KanbanTask $task, string $issueCode): void
    {
        $this->log(
            $task,
            'issue_resolved',
            "Issue {$issueCode} resolved",
            ['status' => 'open'],
            ['status' => 'resolved']
        );
    }

    public function logIssueClosed(KanbanTask $task, string $issueCode): void
    {
        $this->log(
            $task,
            'issue_closed',
            "Issue {$issueCode} closed",
            ['status' => 'resolved'],
            ['status' => 'closed']
        );
    }

    public function logIssueReopened(KanbanTask $task, string $issueCode): void
    {
        $this->log(
            $task,
            'issue_reopened',
            "Issue {$issueCode} reopened",
            ['status' => 'resolved'],
            ['status' => 'reopened']
        );
    }

    public function logIssueStatusChanged(KanbanTask $task, string $issueCode, string $oldStatus, string $newStatus): void
    {
        $this->log(
            $task,
            'issue_status_changed',
            "Issue {$issueCode} status changed from {$oldStatus} to {$newStatus}",
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );
    }

    private function log(
        KanbanTask $task,
        string $action,
        string $description,
        ?array $oldValue = null,
        ?array $newValue = null
    ): void {
        KanbanTaskActivity::create([
            'task_id' => $task->task_id,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }

    private function getUpdateDescription(string $field, mixed $oldValue, mixed $newValue): string
    {
        return match($field) {
            'title' => "Changed title from \"{$oldValue}\" to \"{$newValue}\"",
            'description' => 'Updated description',
            'priority' => "Changed priority from {$oldValue} to {$newValue}",
            'label' => "Changed label from " . ($oldValue ?? 'none') . " to " . ($newValue ?? 'none'),
            'due_date' => "Changed due date from " . ($oldValue ?? 'none') . " to " . ($newValue ?? 'none'),
            default => "Updated {$field}",
        };
    }

    /**
     * Get all activities for a specific task
     * 
     * Returns formatted activity data with user information and timestamps.
     * Activities are sorted by most recent first.
     * 
     * @param string $taskId The task ID to get activities for
     * @param int $limit Maximum number of activities to return (default: 50)
     * @return Collection Collection of formatted activity data
     */
    public function getTaskActivities(string $taskId, int $limit = 50): Collection
    {
        return KanbanTaskActivity::where('task_id', $taskId)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->activity_id,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'icon' => $activity->action_icon,
                    'color' => $activity->action_color,
                    'user' => [
                        'name' => $activity->user->name ?? 'System',
                        'avatar' => $activity->user->avatar_url ?? null,
                        'initials' => $this->getInitials($activity->user->name ?? 'SY'),
                    ],
                    'time' => $activity->created_at->diffForHumans(),
                    'timestamp' => $activity->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    private function getInitials(string $name): string
    {
        return collect(explode(' ', $name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->join('');
    }
}
