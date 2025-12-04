<?php

namespace App\Services;

use App\Models\KanbanBoard;
use App\Models\KanbanTask;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class KanbanService
{
    public function __construct(
        private readonly KanbanActivityService $activityService
    ) {}

    public function getBoardsByWorkspace(string $workspaceId): Collection
    {
        return KanbanBoard::where('workspace_id', $workspaceId)
            ->where('is_archived', false)
            ->with(['tasks' => function ($query) {
                $query->with(['assignees.user'])
                    ->withCount(['attachments', 'comments'])
                    ->orderBy('position');
            }])
            ->orderBy('position')
            ->get();
    }

    public function createBoard(array $data): KanbanBoard
    {
        $maxPosition = KanbanBoard::where('workspace_id', $data['workspace_id'])->max('position') ?? -1;

        return KanbanBoard::create([
            'workspace_id' => $data['workspace_id'],
            'title' => $data['title'],
            'color' => $data['color'] ?? '#6366f1',
            'position' => $maxPosition + 1,
        ]);
    }

    public function updateBoard(KanbanBoard $board, array $data): KanbanBoard
    {
        $board->update(Arr::only($data, ['title', 'color']));
        return $board->fresh();
    }

    public function deleteBoard(KanbanBoard $board): void
    {
        DB::transaction(function () use ($board) {
            $board->tasks()->delete();
            $board->delete();
        });
    }

    public function reorderBoards(string $workspaceId, array $positions): void
    {
        DB::transaction(function () use ($workspaceId, $positions) {
            foreach ($positions as $index => $boardId) {
                KanbanBoard::where('board_id', $boardId)
                    ->where('workspace_id', $workspaceId)
                    ->update(['position' => $index]);
            }
        });
    }

    public function createTask(array $data): KanbanTask
    {
        return DB::transaction(function () use ($data) {
            $maxPosition = KanbanTask::where('board_id', $data['board_id'])->max('position') ?? -1;

            $task = KanbanTask::create([
                'board_id' => $data['board_id'],
                'workspace_id' => $data['workspace_id'],
                'created_by' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'label' => $data['label'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'position' => $maxPosition + 1,
            ]);

            if (!empty($data['assignees'])) {
                $task->assignees()->sync($data['assignees']);
                
                foreach ($data['assignees'] as $employeeId) {
                    $employee = \App\Models\EmployeeProfile::find($employeeId);
                    if ($employee) {
                        $this->activityService->logAssigned($task, $employee->user->name);
                    }
                }
            }

            return $task->loadCount(['attachments', 'comments'])
                ->load(['assignees.user', 'creator']);
        });
    }

    public function updateTask(KanbanTask $task, array $data): KanbanTask
    {
        return DB::transaction(function () use ($task, $data) {
            $oldAssignees = $task->assignees->pluck('employee_id')->toArray();

            $task->update(Arr::only($data, [
                'title',
                'description',
                'priority',
                'label',
                'due_date',
            ]));

            if (isset($data['assignees'])) {
                $newAssignees = $data['assignees'];
                $task->assignees()->sync($newAssignees);

                $added = array_diff($newAssignees, $oldAssignees);
                $removed = array_diff($oldAssignees, $newAssignees);

                foreach ($added as $employeeId) {
                    $employee = \App\Models\EmployeeProfile::find($employeeId);
                    if ($employee) {
                        $this->activityService->logAssigned($task, $employee->user->name);
                    }
                }

                foreach ($removed as $employeeId) {
                    $employee = \App\Models\EmployeeProfile::find($employeeId);
                    if ($employee) {
                        $this->activityService->logUnassigned($task, $employee->user->name);
                    }
                }
            }

            return $task->loadCount(['attachments', 'comments'])
                ->fresh(['assignees.user', 'creator']);
        });
    }

    public function moveTask(KanbanTask $task, string $newBoardId, int $newPosition): KanbanTask
    {
        return DB::transaction(function () use ($task, $newBoardId, $newPosition) {
            $oldBoardId = $task->board_id;

            if ($oldBoardId !== $newBoardId) {
                KanbanTask::where('board_id', $newBoardId)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');
            } else {
                if ($newPosition < $task->position) {
                    KanbanTask::where('board_id', $oldBoardId)
                        ->whereBetween('position', [$newPosition, $task->position - 1])
                        ->increment('position');
                } else {
                    KanbanTask::where('board_id', $oldBoardId)
                        ->whereBetween('position', [$task->position + 1, $newPosition])
                        ->decrement('position');
                }
            }

            $task->update([
                'board_id' => $newBoardId,
                'position' => $newPosition,
            ]);

            return $task->fresh();
        });
    }

    public function deleteTask(KanbanTask $task): void
    {
        DB::transaction(function () use ($task) {
            $task->assignees()->detach();
            $task->attachments()->delete();
            $task->comments()->delete();
            $task->activities()->delete();
            $task->delete();
        });
    }

    public function addComment(KanbanTask $task, string $comment): void
    {
        $task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $comment,
        ]);

        $this->activityService->logCommented($task);
    }

    public function attachFile(KanbanTask $task, $file): void
    {
        $path = $file->store('kanban-attachments', 'public');

        $task->attachments()->create([
            'uploaded_by' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $this->activityService->logAttachmentAdded($task, $file->getClientOriginalName());
    }

    public function deleteAttachment(string $attachmentId): void
    {
        $attachment = \App\Models\KanbanTaskAttachment::findOrFail($attachmentId);
        $task = $attachment->task;

        \Storage::disk('public')->delete($attachment->file_path);
        
        $this->activityService->logAttachmentDeleted($task, $attachment->file_name);
        
        $attachment->delete();
    }

    public function getTaskActivities(string $taskId)
    {
        return $this->activityService->getTaskActivities($taskId);
    }

    public function formatBoardsForKanban(Collection $boards): array
    {
        return $boards->map(function ($board) {
            return [
                'id' => $board->board_id,
                'title' => $board->title,
                'tasks' => $board->tasks->map(function ($task) {
                    return $this->formatTaskForKanban($task);
                }),
            ];
        })->toArray();
    }

    private function formatTaskForKanban(KanbanTask $task): array
    {
        return [
            'id' => $task->task_id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority,
            'priority_badge' => $task->priority_badge,
            'label' => $task->label,
            'label_badge' => $task->label_badge,
            'due_date' => $task->due_date?->format('Y-m-d'),
            'is_overdue' => $task->isOverdue(),
            'assignees' => $task->assignees->map(function ($assignee) {
                return [
                    'id' => $assignee->employee_id,
                    'name' => $assignee->user->name,
                    'avatar' => $assignee->user->avatar_url,
                ];
            }),
            'attachments_count' => $task->attachments_count ?? 0,
            'comments_count' => $task->comments_count ?? 0,
            'active_issues_count' => $task->activeIssuesCount(),
            'has_active_issues' => $task->hasActiveIssues(),
        ];
    }
}
