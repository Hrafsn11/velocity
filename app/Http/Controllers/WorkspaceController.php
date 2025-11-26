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
        $this->authorizeResource(Workspace::class, 'workspace');
    }

    public function index(): View
    {
        // If the authenticated user is an Employee, only show workspaces they are a member of.
        $user = auth()->user();
        if ($user && $user->hasRole('Employee')) {
            $profile = EmployeeProfile::where('user_id', $user->user_id)->first();
            if ($profile) {
                $workspaces = Workspace::query()
                    ->with(['manager.user', 'members.user'])
                    ->whereHas('members', function ($q) use ($profile) {
                        $q->where('employee_profiles.employee_id', $profile->employee_id);
                    })->get();
            } else {
                $workspaces = collect();
            }
        } else {
            $workspaces = $this->workspaceService->list();
        }
        // Sort employees by role, level priority (lead highest), then user name
        $employees = EmployeeProfile::query()
            ->with('user')
            ->whereHas('user')
            ->leftJoin('users', 'users.user_id', '=', 'employee_profiles.user_id')
            ->select('employee_profiles.*')
            ->orderBy('employee_profiles.role')
            ->orderByRaw("CASE
                WHEN employee_profiles.level = 'lead' THEN 5
                WHEN employee_profiles.level = 'senior' THEN 4
                WHEN employee_profiles.level = 'middle' THEN 3
                WHEN employee_profiles.level = 'junior' THEN 2
                WHEN employee_profiles.level = 'intern' THEN 1
                ELSE 0 END DESC")
            ->orderBy('users.name')
            ->get();

        $managers = EmployeeProfile::query()
            ->with('user')
            ->whereHas('user')
            ->where('employee_profiles.level', 'lead')
            ->leftJoin('users', 'users.user_id', '=', 'employee_profiles.user_id')
            ->select('employee_profiles.*')
            ->orderBy('employee_profiles.role')
            ->orderBy('users.name')
            ->get();

        $statuses = WorkspaceStatus::cases();

        return view('workspace.index', [
            'workspaces' => $workspaces,
            'employees' => $employees,
            'managers' => $managers,
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

