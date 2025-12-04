<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeRequestRequest;
use App\Models\ChangeRequest;
use App\Models\Workspace;
use App\Models\Issue;
use App\Services\ChangeRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChangeRequestController extends Controller
{
    public function __construct(
        private readonly ChangeRequestService $changeRequestService
    ) {
    }

    /**
     * Display list of change requests
     */
    public function index(Request $request): View
    {
        $workspaceId = $request->get('workspace_id');
        
        $query = ChangeRequest::with(['workspace', 'issue', 'requester', 'approver']);
        
        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }
        
        $changeRequests = $query->latest()->get();
        $workspaces = Workspace::all();
        $issues = Issue::with('workspace')->get();

        return view('risk.change-requests', compact('changeRequests', 'workspaces', 'issues'));
    }

    /**
     * Store new change request
     */
    public function store(ChangeRequestRequest $request): RedirectResponse
    {
        $this->changeRequestService->createChangeRequest($request->validated());

        return redirect()
            ->route('change-requests.index')
            ->with('success', 'Change request successfully created.');
    }

    /**
     * Approve change request
     */
    public function approve(ChangeRequest $changeRequest): RedirectResponse
    {
        $this->changeRequestService->approveChangeRequest($changeRequest);

        return redirect()
            ->route('change-requests.index')
            ->with('success', 'Change request successfully approved.');
    }

    /**
     * Reject change request
     */
    public function reject(Request $request, ChangeRequest $changeRequest): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $this->changeRequestService->rejectChangeRequest($changeRequest, $validated['rejection_reason']);

        return redirect()
            ->route('change-requests.index')
            ->with('success', 'Change request successfully rejected.');
    }

    /**
     * Implement approved change request (auto-update workspace)
     */
    public function implement(ChangeRequest $changeRequest): RedirectResponse
    {
        try {
            $results = $this->changeRequestService->implementChangeRequest($changeRequest);

            $message = 'Change request successfully implemented. ';
            
            if ($results['type'] === 'timeline') {
                $message .= "Workspace deadline extended to {$results['workspace_end_date']}. ";
                $message .= "{$results['tasks_adjusted']} tasks adjusted.";
            }
            
            if ($results['type'] === 'resource') {
                $message .= "Team size now: {$results['current_team_size']} members.";
            }

            return redirect()
                ->route('change-requests.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->route('change-requests.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete change request
     */
    public function destroy(ChangeRequest $changeRequest): RedirectResponse
    {
        $changeRequest->delete();

        return redirect()
            ->route('change-requests.index')
            ->with('success', 'Change request successfully deleted.');
    }
}
