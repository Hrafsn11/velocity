<?php

namespace App\Observers;

use App\Models\KanbanTask;
use App\Services\KanbanActivityService;

class KanbanTaskObserver
{
    public function __construct(
        private readonly KanbanActivityService $activityService
    ) {}

    public function created(KanbanTask $task): void
    {
        $this->activityService->logTaskCreated($task);
    }

    public function updated(KanbanTask $task): void
    {
        if (!$task->wasChanged()) {
            return;
        }

        $changes = [];
        
        if ($task->wasChanged('title')) {
            $changes['title'] = [
                'old' => $task->getOriginal('title'),
                'new' => $task->title,
            ];
        }

        if ($task->wasChanged('description')) {
            $changes['description'] = [
                'old' => $task->getOriginal('description'),
                'new' => $task->description,
            ];
        }

        if ($task->wasChanged('priority')) {
            $this->activityService->logPriorityChanged(
                $task,
                $task->getOriginal('priority'),
                $task->priority
            );
        }

        if ($task->wasChanged('label')) {
            $this->activityService->logLabelChanged(
                $task,
                $task->getOriginal('label'),
                $task->label
            );
        }

        if ($task->wasChanged('due_date')) {
            $this->activityService->logDueDateChanged(
                $task,
                $task->getOriginal('due_date')?->format('Y-m-d'),
                $task->due_date?->format('Y-m-d')
            );
        }

        if ($task->wasChanged('board_id')) {
            $oldBoard = \App\Models\KanbanBoard::find($task->getOriginal('board_id'));
            $newBoard = $task->board;
            
            if ($oldBoard && $newBoard) {
                $this->activityService->logTaskMoved(
                    $task,
                    $oldBoard->title,
                    $newBoard->title
                );
            }
        }

        if (!empty($changes)) {
            $this->activityService->logTaskUpdated($task, $changes);
        }
    }
}
