<?php

namespace App\Http\Controllers;

use App\Http\Requests\KanbanBoardRequest;
use App\Models\KanbanBoard;
use App\Models\Workspace;
use App\Services\KanbanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KanbanBoardController extends Controller
{
    public function __construct(private readonly KanbanService $kanbanService)
    {
        $this->middleware('auth');
    }

    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $boards = $this->kanbanService->getBoardsByWorkspace($workspace->workspace_id);

        return response()->json([
            'success' => true,
            'data' => $this->kanbanService->formatBoardsForKanban($boards),
        ]);
    }

    public function store(KanbanBoardRequest $request): JsonResponse
    {
        $workspace = Workspace::findOrFail($request->workspace_id);
        $this->authorize('update', $workspace);

        $board = $this->kanbanService->createBoard($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Board created successfully',
            'data' => $board,
        ], 201);
    }

    public function update(KanbanBoardRequest $request, KanbanBoard $board): JsonResponse
    {
        $this->authorize('update', $board->workspace);

        $updatedBoard = $this->kanbanService->updateBoard($board, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Board updated successfully',
            'data' => $updatedBoard,
        ]);
    }

    public function destroy(KanbanBoard $board): JsonResponse
    {
        $this->authorize('update', $board->workspace);

        $this->kanbanService->deleteBoard($board);

        return response()->json([
            'success' => true,
            'message' => 'Board deleted successfully',
        ]);
    }

    public function reorder(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $request->validate([
            'positions' => 'required|array',
            'positions.*' => 'required|string',
        ]);

        $this->kanbanService->reorderBoards($workspace->workspace_id, $request->positions);

        return response()->json([
            'success' => true,
            'message' => 'Boards reordered successfully',
        ]);
    }
}
