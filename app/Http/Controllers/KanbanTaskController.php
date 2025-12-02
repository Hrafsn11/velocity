<?php

namespace App\Http\Controllers;

use App\Http\Requests\KanbanTaskRequest;
use App\Models\KanbanTask;
use App\Models\Workspace;
use App\Services\KanbanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KanbanTaskController extends Controller
{
    public function __construct(private readonly KanbanService $kanbanService)
    {
        $this->middleware('auth');
    }

    public function show(Workspace $workspace, KanbanTask $task): JsonResponse
    {
        $this->authorize('view', $workspace);

        // Verify task belongs to workspace
        if ($task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $task->load([
            'board',
            'creator',
            'assignees.user',
            'attachments.uploader',
            'comments.user',
        ]);

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    public function store(Workspace $workspace, KanbanTaskRequest $request): JsonResponse
    {
        $this->authorize('update', $workspace);

        $data = $request->validated();
        $data['workspace_id'] = $workspace->workspace_id; // Use correct primary key

        $task = $this->kanbanService->createTask($data);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    public function update(Workspace $workspace, KanbanTask $task, KanbanTaskRequest $request): JsonResponse
    {
        $this->authorize('update', $workspace);

        // Verify task belongs to workspace
        if ($task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $updatedTask = $this->kanbanService->updateTask($task, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $updatedTask,
        ]);
    }

    public function move(Workspace $workspace, KanbanTask $task, Request $request): JsonResponse
    {
        $this->authorize('update', $workspace);

        // Verify task belongs to workspace
        if ($task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $request->validate([
            'board_id' => 'required|exists:kanban_boards,board_id',
            'position' => 'required|integer|min:0',
        ]);

        $movedTask = $this->kanbanService->moveTask(
            $task,
            $request->board_id,
            $request->position
        );

        return response()->json([
            'success' => true,
            'message' => 'Task moved successfully',
            'data' => $movedTask,
        ]);
    }

    public function destroy(Workspace $workspace, KanbanTask $task): JsonResponse
    {
        $this->authorize('update', $workspace);

        // Verify task belongs to workspace
        if ($task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $this->kanbanService->deleteTask($task);

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully',
        ]);
    }

    public function comment(Workspace $workspace, KanbanTask $task, Request $request): JsonResponse
    {
        $this->authorize('view', $workspace);

        // Verify task belongs to workspace
        if ($task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $this->kanbanService->addComment($task, $request->comment);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
        ]);
    }

    public function attach(Workspace $workspace, KanbanTask $task, Request $request): JsonResponse
    {
        $this->authorize('update', $workspace);

        // Verify task belongs to workspace
        if ($task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        $this->kanbanService->attachFile($task, $request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'File attached successfully',
        ]);
    }

    public function deleteAttachment(Workspace $workspace, string $attachmentId): JsonResponse
    {
        $attachment = \App\Models\KanbanTaskAttachment::findOrFail($attachmentId);
        $this->authorize('update', $workspace);

        if ($attachment->task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $this->kanbanService->deleteAttachment($attachmentId);

        return response()->json([
            'success' => true,
            'message' => 'Attachment deleted successfully',
        ]);
    }

    public function activities(Workspace $workspace, KanbanTask $task): JsonResponse
    {
        $this->authorize('view', $workspace);

        // Verify task belongs to workspace
        if ($task->workspace_id !== $workspace->workspace_id) {
            abort(404);
        }

        $activities = $this->kanbanService->getTaskActivities($task->task_id);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
