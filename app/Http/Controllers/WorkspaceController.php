<?php

namespace App\Http\Controllers;

use App\Enums\WorkspaceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkspaceRequest;
use App\Models\EmployeeProfile;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaceService
    ) {
    }

    public function index(): View
    {
        $workspaces = $this->workspaceService->list();
        $employees = EmployeeProfile::query()
            ->with('user')
            ->whereHas('user')
            ->leftJoin('users', 'users.user_id', '=', 'employee_profiles.user_id')
            ->orderBy('users.name')
            ->select('employee_profiles.*')
            ->get();

        $statuses = WorkspaceStatus::cases();

        return view('workspace.index', [
            'workspaces' => $workspaces,
            'employees' => $employees,
            'statuses' => $statuses,
        ]);
    }

    public function store(WorkspaceRequest $request): RedirectResponse
    {
        $this->workspaceService->create($request->validated());

        return redirect()
            ->route('workspaces.index')
            ->with('status', 'Workspace berhasil dibuat.');
    }

    public function update(WorkspaceRequest $request, Workspace $workspace): RedirectResponse
    {
        $this->workspaceService->update($workspace, $request->validated());

        return redirect()
            ->route('workspaces.index')
            ->with('status', 'Workspace berhasil diperbarui.');
    }

    public function destroy(Workspace $workspace): RedirectResponse
    {
        $this->workspaceService->delete($workspace);

        return redirect()
            ->route('workspaces.index')
            ->with('status', 'Workspace berhasil diarsipkan.');
    }
}

