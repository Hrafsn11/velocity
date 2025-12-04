<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiskRequest;
use App\Models\Risk;
use App\Models\Workspace;
use App\Services\RiskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Risk Controller
 * 
 * Handles all HTTP requests related to risk management including:
 * - Dashboard with statistics and workspace filtering
 * - Risk listing with filters
 * - Risk CRUD operations
 * - Risk to Issue conversion
 * 
 * @package App\Http\Controllers
 */
class RiskController extends Controller
{
    /**
     * Create a new controller instance
     * 
     * @param RiskService $riskService Service for risk business logic
     */
    public function __construct(
        private readonly RiskService $riskService
    ) {
    }

    /**
     * Display the global risk management dashboard
     * 
     * Shows statistics for all risks or filtered by workspace.
     * Includes risk counts by status, urgency, and workspace comparison.
     * 
     * @param Request $request HTTP request with optional workspace_id filter
     * @return View Dashboard view with statistics and workspace data
     */
    public function dashboard(Request $request): View
    {
        $workspaceId = $request->get('workspace_id');
        
        $stats = $this->riskService->getDashboardStats($workspaceId);
        
        // Get workspaces with risk counts (eager load all counts in one query)
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

        // Single workspace view: load workspace details and recent data
        $workspace = null;
        $recentRisks = null;
        $recentIssues = null;
        
        if ($workspaceId) {
            // Eager load workspace with counts
            $workspace = Workspace::withCount(['members', 'kanbanTasks'])
                ->find($workspaceId);
            
            // Get 5 most recent risks for this workspace (optimized with select)
            $recentRisks = Risk::where('workspace_id', $workspaceId)
                ->with(['creator:user_id,name'])
                ->latest()
                ->limit(5)
                ->get();
            
            // Get 5 most recent issues from risks in this workspace (optimized)
            $recentIssues = \App\Models\Issue::where('workspace_id', $workspaceId)
                ->whereNotNull('risk_id')
                ->with([
                    'risk:risk_id,code',
                    'assignee.user:user_id,name'
                ])
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('risk.dashboard', compact(
            'stats', 
            'workspaces', 
            'workspaceId',
            'workspace',
            'recentRisks',
            'recentIssues'
        ));
    }

    /**
     * Display a listing of risks with optional workspace filter
     * 
     * Shows all risks with their relationships (workspace, creator, issues, affected task).
     * Calculates statistics for total, critical, active, and mitigated risks.
     * Eager loads workspaces with their tasks and members for the UI.
     * 
     * @param Request $request HTTP request with optional workspace_id filter
     * @return View Risk index view with risks list and statistics
     */
    public function index(Request $request): View
    {
        $workspaceId = $request->get('workspace_id');
        
        // Eager load relationships with selective fields to reduce query payload
        $query = Risk::with([
            'workspace:workspace_id,title',
            'creator:user_id,name',
            'issues:issue_id,risk_id,code,title,status',
            'affectedTask:task_id,title'
        ]);
        
        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }
        
        $risks = $query->latest()->get();
        
        // Load workspaces with nested relationships (one query per relationship)
        $workspaces = Workspace::with([
            'kanbanTasks' => function($q) {
                $q->select('task_id', 'workspace_id', 'title');
            }, 
            'members.user:user_id,name'
        ])->get();
        
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
     * Store a newly created risk
     * 
     * Validates the request using RiskRequest and delegates creation to RiskService.
     * The service handles code generation, urgency calculation, and activity logging.
     * 
     * @param RiskRequest $request Validated risk data
     * @return RedirectResponse Redirects to risk index with success message
     */
    public function store(RiskRequest $request): RedirectResponse
    {
        $this->riskService->createRisk($request->validated());

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk successfully created.');
    }

    /**
     * Update the specified risk
     * 
     * Validates the request using RiskRequest and delegates update to RiskService.
     * The service handles urgency recalculation and change detection/logging.
     * 
     * @param RiskRequest $request Validated risk data
     * @param Risk $risk The risk to update (route model binding)
     * @return RedirectResponse Redirects to risk index with success message
     */
    public function update(RiskRequest $request, Risk $risk): RedirectResponse
    {
        $this->riskService->updateRisk($risk, $request->validated());

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk successfully updated.');
    }

    /**
     * Mark a risk as mitigated
     * 
     * Updates the risk status to 'mitigated' indicating that mitigation actions
     * have been successfully completed.
     * 
     * @param Risk $risk The risk to mark as mitigated (route model binding)
     * @return RedirectResponse Redirects to risk index with success message
     */
    public function markMitigated(Risk $risk): RedirectResponse
    {
        $this->riskService->updateRisk($risk, ['status' => 'mitigated']);

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk marked as mitigated successfully.');
    }

    /**
     * Remove the specified risk from storage
     * 
     * Performs a soft delete on the risk. The risk can be restored later if needed.
     * All related activities are preserved.
     * 
     * @param Risk $risk The risk to delete (route model binding)
     * @return RedirectResponse Redirects to risk index with success message
     */
    public function destroy(Risk $risk): RedirectResponse
    {
        $risk->delete();

        return redirect()
            ->route('risk.index')
            ->with('success', 'Risk successfully deleted.');
    }

    /**
     * Convert a risk to an issue when it materializes
     * 
     * Validates issue data and delegates conversion to RiskService.
     * This process:
     * - Creates a new issue linked to the risk
     * - Updates risk status to 'materialized'
     * - Logs activities for both risk and issue
     * - Optionally links to a kanban task
     * 
     * @param Request $request HTTP request with issue data
     * @param Risk $risk The risk to convert (route model binding)
     * @return RedirectResponse Redirects to issues page with success message
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
