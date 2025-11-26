<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class ProjectDetailController extends Controller
{
    public function __construct(private readonly WorkspaceService $workspaceService)
    {
    }

    public function show($id = 1)
    {
        // Use WorkspaceService to build summary payload. Keep defensive: if workspace not found, abort 404.
        $summary = $this->workspaceService->summary($id);

        return view('workspace.detail', ['summary' => $summary]);
    }
}