<?php

namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\Workspace;
use App\Models\KanbanTask;
use App\Models\RiskActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChangeRequestService
{
    /**
     * Generate unique code for change request per workspace
     */
    public function generateCode(string $workspaceId): string
    {
        $lastCR = ChangeRequest::where('workspace_id', $workspaceId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastCR) {
            return 'CR001';
        }

        $lastNumber = (int) substr($lastCR->code, 2);
        return 'CR' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create new change request
     */
    public function createChangeRequest(array $data): ChangeRequest
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $this->generateCode($data['workspace_id']);
            $data['requested_by'] = auth()->id();

            // If timeline type, snapshot current end_date
            if ($data['type'] === 'timeline') {
                $workspace = Workspace::find($data['workspace_id']);
                $data['current_end_date'] = $workspace->end_date;
                
                // Calculate proposed end date
                $data['proposed_end_date'] = Carbon::parse($workspace->end_date)
                    ->addDays($data['timeline_extension_days']);
            }

            $cr = ChangeRequest::create($data);

            $this->logActivity($cr, 'created', 'Change request created');

            return $cr->load(['workspace', 'issue', 'requester']);
        });
    }

    /**
     * Approve change request
     */
    public function approveChangeRequest(ChangeRequest $cr): ChangeRequest
    {
        return DB::transaction(function () use ($cr) {
            $cr->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->logActivity($cr, 'approved', 'Change request approved');

            return $cr->fresh(['workspace', 'issue', 'requester', 'approver']);
        });
    }

    /**
     * Reject change request
     */
    public function rejectChangeRequest(ChangeRequest $cr, string $reason): ChangeRequest
    {
        return DB::transaction(function () use ($cr, $reason) {
            $cr->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->logActivity($cr, 'rejected', "Change request rejected: {$reason}");

            return $cr->fresh(['workspace', 'issue', 'requester', 'approver']);
        });
    }

    /**
     * Implement approved change request (auto-update workspace)
     */
    public function implementChangeRequest(ChangeRequest $cr): array
    {
        if (!$cr->canBeImplemented()) {
            throw new \Exception('Change request must be approved before implementation');
        }

        return DB::transaction(function () use ($cr) {
            $workspace = $cr->workspace;
            $results = [];

            if ($cr->isTimelineType()) {
                // Update workspace end_date
                $workspace->update([
                    'end_date' => $cr->proposed_end_date
                ]);

                // Auto-adjust all kanban tasks deadlines
                $tasksUpdated = KanbanTask::where('workspace_id', $workspace->workspace_id)
                    ->whereNotNull('due_date')
                    ->update([
                        'due_date' => DB::raw("DATE_ADD(due_date, INTERVAL {$cr->timeline_extension_days} DAY)")
                    ]);

                $results['type'] = 'timeline';
                $results['workspace_end_date'] = $cr->proposed_end_date->format('Y-m-d');
                $results['tasks_adjusted'] = $tasksUpdated;
            }

            if ($cr->isResourceType()) {
                // Add members
                if (!empty($cr->members_to_add)) {
                    foreach ($cr->members_to_add as $employeeId) {
                        $workspace->members()->syncWithoutDetaching($employeeId);
                    }
                    $results['members_added'] = count($cr->members_to_add);
                }

                // Remove members
                if (!empty($cr->members_to_remove)) {
                    $workspace->members()->detach($cr->members_to_remove);
                    $results['members_removed'] = count($cr->members_to_remove);
                }

                $results['type'] = 'resource';
                $results['current_team_size'] = $workspace->members()->count();
            }

            // Update CR status
            $cr->update(['status' => 'implemented']);

            $this->logActivity($cr, 'implemented', 'Change request implemented', $results);

            return $results;
        });
    }

    /**
     * Get change requests by workspace with filters
     */
    public function getChangeRequestsByWorkspace(string $workspaceId, array $filters = []): Collection
    {
        $query = ChangeRequest::where('workspace_id', $workspaceId)
            ->with(['workspace', 'issue', 'requester', 'approver']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->get();
    }

    /**
     * Log activity
     */
    public function logActivity(ChangeRequest $cr, string $type, string $description, array $changes = []): void
    {
        RiskActivity::create([
            'subject_type' => ChangeRequest::class,
            'subject_id' => $cr->change_request_id,
            'activity_type' => $type,
            'description' => $description,
            'changes' => $changes,
            'user_id' => auth()->id(),
        ]);
    }
}
