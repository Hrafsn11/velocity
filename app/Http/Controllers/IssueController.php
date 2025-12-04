<?php

namespace App\Http\Controllers;

use App\Http\Requests\IssueRequest;
use App\Models\Issue;
use App\Models\Workspace;
use App\Models\EmployeeProfile;
use App\Services\IssueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Issue Controller
 * 
 * Handles all HTTP requests related to issue management including:
 * - Issue listing with workspace filtering
 * - Issue CRUD operations
 * - Status updates and resolution
 * - Comments and attachments
 * - Detail view (AJAX)
 * 
 * @package App\Http\Controllers
 */
class IssueController extends Controller
{
    /**
     * Create a new controller instance
     * 
     * @param IssueService $issueService Service for issue business logic
     */
    public function __construct(
        private readonly IssueService $issueService
    ) {
    }

    /**
     * Display a listing of issues with optional workspace filter
     * 
     * Shows all issues with their relationships (workspace, risk, assignee, creator).
     * Eager loads workspaces with tasks and members, and employee profiles for assignment.
     * 
     * @param Request $request HTTP request with optional workspace_id filter
     * @return View Issue index view with issues, workspaces, and employees
     */
    public function index(Request $request): View
    {
        $workspaceId = $request->get('workspace_id');
        
        // Eager load relationships with selective fields to optimize performance
        $query = Issue::with([
            'workspace:workspace_id,title',
            'risk:risk_id,code,workspace_id',
            'risk.workspace:workspace_id,title',
            'assignee.user:user_id,name',
            'creator:user_id,name'
        ]);
        
        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }
        
        $issues = $query->latest()->get();
        
        // Load workspaces with nested relationships (optimized)
        $workspaces = Workspace::with([
            'kanbanTasks' => function($q) {
                $q->select('task_id', 'workspace_id', 'title');
            }, 
            'members.user:user_id,name'
        ])->get();
        
        // Load employees with user relationship
        $employees = EmployeeProfile::with('user:user_id,name')->get();

        return view('risk.issues', compact('issues', 'workspaces', 'employees'));
    }

    /**
     * Store a newly created issue
     * 
     * Validates the request using IssueRequest and delegates creation to IssueService.
     * The service handles code generation, activity logging, and task linking.
     * 
     * @param IssueRequest $request Validated issue data
     * @return RedirectResponse Redirects to issues index with success message
     */
    public function store(IssueRequest $request): RedirectResponse
    {
        $this->issueService->createIssue($request->validated());

        return redirect()
            ->route('risk.issues')
            ->with('success', 'Issue successfully created.');
    }

    /**
     * Update the specified issue
     * 
     * Validates the request using IssueRequest and delegates update to IssueService.
     * The service handles change detection, activity logging, and task notifications.
     * 
     * @param IssueRequest $request Validated issue data
     * @param Issue $issue The issue to update (route model binding)
     * @return RedirectResponse Redirects to issues index with success message
     */
    public function update(IssueRequest $request, Issue $issue): RedirectResponse
    {
        $this->issueService->updateIssue($issue, $request->validated());

        return redirect()
            ->route('risk.issues')
            ->with('success', 'Issue successfully updated.');
    }

    /**
     * Remove the specified issue from storage
     * 
     * Performs a soft delete on the issue. The issue can be restored later if needed.
     * All related activities, comments, and attachments are preserved.
     * 
     * @param Issue $issue The issue to delete (route model binding)
     * @return RedirectResponse Redirects to issues index with success message
     */
    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->delete();

        return redirect()
            ->route('risk.issues')
            ->with('success', 'Issue successfully deleted.');
    }

    /**
     * Update the status of an issue
     * 
     * Validates the new status and delegates update to IssueService.
     * If the issue is linked to a kanban task, the task activity is also logged.
     * Valid statuses: open, in_progress, resolved, closed, reopened
     * 
     * @param Request $request HTTP request with status field
     * @param Issue $issue The issue to update (route model binding)
     * @return RedirectResponse Redirects to issues index with success message
     */
    public function updateStatus(Request $request, Issue $issue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed,reopened'
        ]);

        $this->issueService->updateIssue($issue, $validated);

        return redirect()
            ->route('risk.issues')
            ->with('success', 'Issue status successfully updated.');
    }

    /**
     * Display the specified issue details (AJAX endpoint)
     * 
     * Loads the issue with all relationships for display in a modal or detail view.
     * Returns JSON response with complete issue data including comments, attachments,
     * related risk, linked task, assignee, and change requests.
     * 
     * @param Issue $issue The issue to display (route model binding)
     * @return JsonResponse JSON response with issue data
     */
    public function show(Issue $issue): JsonResponse
    {
        $issue->load(['workspace', 'risk', 'linkedTask', 'assignee.user', 'creator', 
                      'comments.user', 'comments.attachments', 'attachments', 'resolver', 'changeRequests']);
        
        return response()->json([
            'success' => true,
            'data' => $issue
        ]);
    }

    /**
     * Add a comment to an issue
     * 
     * Validates comment text (minimum 5 characters) and optional image attachments.
     * Delegates to IssueService which handles comment creation and image uploads.
     * Logs activity for audit trail.
     * 
     * @param Request $request HTTP request with comment text and optional images
     * @param Issue $issue The issue to add comment to (route model binding)
     * @return RedirectResponse Redirects to issue detail with success message
     */
    public function addComment(Request $request, Issue $issue): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string|min:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $images = $request->hasFile('images') ? $request->file('images') : [];
        
        $this->issueService->addComment($issue, $validated['comment'], $images);

        return redirect()
            ->route('risk.issues.show', $issue->issue_id)
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Resolve an issue with optional resolution comment and images
     * 
     * Marks the issue as resolved with optional explanation comment and screenshots.
     * Delegates to IssueService which:
     * - Creates resolution comment (marked as is_resolution = true)
     * - Updates issue status to 'resolved'
     * - Records resolution timestamp and resolver user
     * - Logs activity for both issue and linked task (if any)
     * 
     * @param Request $request HTTP request with optional resolution_comment and images
     * @param Issue $issue The issue to resolve (route model binding)
     * @return JsonResponse JSON response indicating success
     */
    public function resolve(Request $request, Issue $issue): JsonResponse
    {
        $validated = $request->validate([
            'resolution_comment' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $comment = $validated['resolution_comment'] ?? 'Issue resolved';
        $images = $request->hasFile('images') ? $request->file('images') : [];
        
        $this->issueService->resolveIssue($issue, $comment, $images);

        return response()->json([
            'success' => true,
            'message' => 'Issue resolved successfully'
        ]);
    }
}
