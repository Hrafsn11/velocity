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

class IssueController extends Controller
{
    public function __construct(
        private readonly IssueService $issueService
    ) {
    }

    /**
     * Display list of issues
     */
    public function index(Request $request): View
    {
        $workspaceId = $request->get('workspace_id');
        
        $query = Issue::with(['workspace', 'risk.workspace', 'assignee', 'creator']);
        
        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }
        
        $issues = $query->latest()->get();
        $workspaces = Workspace::with(['kanbanTasks' => function($q) {
            $q->select('task_id', 'workspace_id', 'title');
        }, 'members.user'])->get();
        $employees = EmployeeProfile::with('user')->get();

        return view('risk.issues', compact('issues', 'workspaces', 'employees'));
    }

    /**
     * Store new issue
     */
    public function store(IssueRequest $request): RedirectResponse
    {
        $this->issueService->createIssue($request->validated());

        return redirect()
            ->route('issues.index')
            ->with('success', 'Issue successfully created.');
    }

    /**
     * Update issue
     */
    public function update(IssueRequest $request, Issue $issue): RedirectResponse
    {
        $this->issueService->updateIssue($issue, $request->validated());

        return redirect()
            ->route('issues.index')
            ->with('success', 'Issue successfully updated.');
    }

    /**
     * Delete issue
     */
    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->delete();

        return redirect()
            ->route('issues.index')
            ->with('success', 'Issue successfully deleted.');
    }

    /**
     * Update issue status
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
     * Show issue detail (for AJAX modal)
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
     * Add comment to issue
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
     * Resolve issue (comment & images optional)
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
