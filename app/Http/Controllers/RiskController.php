<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiskRequest;
use App\Models\Risk;
use App\Models\Workspace;
use App\Services\RiskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiskController extends Controller
{
    public function __construct(
        private readonly RiskService $riskService
    ) {
    }

    /**
     * Display global dashboard
     */
    public function dashboard(Request $request): View
    {
        $workspaceId = $request->get('workspace_id');
        
        $stats = $this->riskService->getDashboardStats($workspaceId);
        
        // Get workspaces with risk counts
        $workspaces = Workspace::withCount([
            'risks',
            'risks as active_risks_count' => function($q) {
                $q->where('status', 'active');
            },
            'risks as mitigated_risks_count' => function($q) {
                $q->where('status', 'mitigated');
            },
            'risks as materialized_risks_count' => function($q) {
                $q->where('status', 'materialized');
            },
            'risks as critical_risks_count' => function($q) {
                $q->where('urgency', 'Critical');
            },
            'issues',
            'changeRequests'
        ])->get();

        return view('risk.dashboard', compact('stats', 'workspaces', 'workspaceId'));
    }

    /**
     * Display list of risks
     */
    public function index(Request $request): View
    {
        $workspaceId = $request->get('workspace_id');
        
        $query = Risk::with(['workspace', 'creator', 'issues', 'affectedTask']);
        
        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }
        
        $risks = $query->latest()->get();
        $workspaces = Workspace::with(['kanbanTasks' => function($q) {
            $q->select('task_id', 'workspace_id', 'title');
        }, 'members.user'])->get();
        
        // Calculate stats
        $stats = [
            'total' => $risks->count(),
            'critical' => $risks->where('urgency', 'Critical')->count(),
            'active' => $risks->where('status', 'active')->count(),
            'mitigated' => $risks->where('status', 'mitigated')->count(),
        ];

        return view('risk.index', compact('risks', 'workspaces', 'workspaceId', 'stats'));
    }

    /**
     * Store new risk
     */
    public function store(RiskRequest $request): RedirectResponse
    {
        $this->riskService->createRisk($request->validated());

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk successfully created.');
    }

    /**
     * Update risk
     */
    public function update(RiskRequest $request, Risk $risk): RedirectResponse
    {
        $this->riskService->updateRisk($risk, $request->validated());

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk successfully updated.');
    }

    /**
     * Mark risk as mitigated
     */
    public function markMitigated(Risk $risk): RedirectResponse
    {
        $this->riskService->updateRisk($risk, ['status' => 'mitigated']);

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk marked as mitigated successfully.');
    }

    /**
     * Delete risk
     */
    public function destroy(Risk $risk): RedirectResponse
    {
        $risk->delete();

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk successfully deleted.');
    }

    /**
     * Convert risk to issue
     */
    public function convertToIssue(Request $request, Risk $risk): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:5',
            'severity' => 'required|integer|min:1|max:5',
            'assignee_id' => 'nullable|exists:employee_profiles,employee_id',
            'deadline' => 'nullable|date',
            'linked_task_id' => 'nullable|exists:kanban_tasks,task_id',
        ]);

        $issue = $this->riskService->convertToIssue($risk, $validated);

        return redirect()
            ->route('risk.issues')
            ->with('success', "Risk {$risk->code} successfully converted to Issue {$issue->code}.");
    }
}
