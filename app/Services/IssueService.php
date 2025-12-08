<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\IssueComment;
use App\Models\IssueAttachment;
use App\Models\KanbanTask;
use App\Models\RiskActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Issue Service
 * 
 * Handles all business logic related to issue management including creation,
 * updating, commenting, resolution, and activity logging.
 * 
 * @package App\Services
 */
class IssueService
{
    /**
     * Create a new Issue Service instance
     * 
     * @param KanbanActivityService $activityService Service for logging activities to linked tasks
     */
    public function __construct(
        private readonly KanbanActivityService $activityService
    ) {
    }
    
    /**
     * Generate unique sequential code for issue within a workspace
     * 
     * Format: ISS001, ISS002, ISS003, etc.
     * 
     * @param string $workspaceId The workspace ID to generate code for
     * @return string Generated issue code
     */
    public function generateCode(string $workspaceId): string
    {
        $lastIssue = Issue::where('workspace_id', $workspaceId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastIssue) {
            return 'ISS001';
        }

        $lastNumber = (int) substr($lastIssue->code, 3);
        return 'ISS' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new issue with automatic code generation
     * 
     * This method handles the complete issue creation process including:
     * - Generating unique issue code
     * - Setting the creator
     * - Logging the creation activity
     * - Notifying linked task (if any)
     * 
     * @param array $data Issue data including workspace_id, title, description, priority, etc.
     * @return Issue The created issue with loaded relationships
     * @throws \Exception If transaction fails
     */
    public function createIssue(array $data): Issue
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $this->generateCode($data['workspace_id']);
            $data['created_by'] = Auth::id();

            $issue = Issue::create($data);

            $this->logActivity($issue, 'created', 'Issue created');

            // Log to task activity if linked to a task
            if ($issue->linked_task_id) {
                $task = KanbanTask::find($issue->linked_task_id);
                if ($task) {
                    $this->activityService->logIssueLinked($task, $issue->code, $issue->title);
                }
            }

            return $issue->load(['workspace', 'risk', 'assignee', 'creator']);
        });
    }

    /**
     * Update an existing issue and log changes
     * 
     * Automatically logs all changes made to tracked fields.
     * If status changes and issue is linked to a task, logs activity to the task as well.
     * 
     * @param Issue $issue The issue to update
     * @param array $data Updated issue data
     * @return Issue The updated issue with fresh relationships
     * @throws \Exception If transaction fails
     */
    public function updateIssue(Issue $issue, array $data): Issue
    {
        return DB::transaction(function () use ($issue, $data) {
            $changes = $this->detectChanges($issue, $data);
            $oldStatus = $issue->status;

            $issue->update($data);

            if (!empty($changes)) {
                $this->logActivity($issue, 'updated', 'Issue updated', $changes);
                
                // Log to task activity if status changed and linked to a task
                if (isset($changes['status']) && $issue->linked_task_id) {
                    $task = KanbanTask::find($issue->linked_task_id);
                    if ($task) {
                        $newStatus = $issue->status;
                        
                        // Use specific methods for common status changes
                        if ($newStatus === 'resolved' && $oldStatus !== 'resolved') {
                            $this->activityService->logIssueResolved($task, $issue->code);
                        } elseif ($newStatus === 'closed' && $oldStatus !== 'closed') {
                            $this->activityService->logIssueClosed($task, $issue->code);
                        } elseif ($newStatus === 'reopened' && $oldStatus !== 'reopened') {
                            $this->activityService->logIssueReopened($task, $issue->code);
                        } else {
                            // Generic status change
                            $this->activityService->logIssueStatusChanged($task, $issue->code, $oldStatus, $newStatus);
                        }
                    }
                }
            }

            return $issue->fresh(['workspace', 'risk', 'assignee', 'creator']);
        });
    }

    /**
     * Get all issues for a specific workspace with optional filters
     * 
     * Supports filtering by status, priority, and assignee.
     * Always eager loads workspace, risk, assignee, and creator relationships.
     * 
     * @param string $workspaceId The workspace ID to fetch issues for
     * @param array $filters Optional filters ['status' => '...', 'priority' => int, 'assignee_id' => '...']
     * @return Collection Collection of Issue models with relationships
     */
    public function getIssuesByWorkspace(string $workspaceId, array $filters = []): Collection
    {
        $query = Issue::where('workspace_id', $workspaceId)
            ->with(['workspace', 'risk', 'assignee', 'creator']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['assignee_id'])) {
            $query->where('assignee_id', $filters['assignee_id']);
        }

        return $query->latest()->get();
    }

    /**
     * Detect changes between current issue data and new data
     * 
     * Tracks changes to: title, priority, severity, status, assignee_id, and deadline
     * Returns an array of changes in format ['field' => ['from' => old, 'to' => new]]
     * 
     * @param Issue $issue The current issue model
     * @param array $newData The new data being applied
     * @return array Array of changes detected
     */
    protected function detectChanges(Issue $issue, array $newData): array
    {
        $changes = [];
        $trackFields = ['title', 'priority', 'severity', 'status', 'assignee_id', 'deadline'];

        foreach ($trackFields as $field) {
            if (isset($newData[$field]) && $newData[$field] != $issue->$field) {
                $changes[$field] = [
                    'from' => $issue->$field,
                    'to' => $newData[$field],
                ];
            }
        }

        return $changes;
    }

    /**
     * Add a comment to an issue
     * 
     * Creates a comment record and optionally attaches images.
     * Logs the comment activity for audit trail.
     * 
     * @param Issue $issue The issue to add comment to
     * @param string $comment The comment text
     * @param array $images Array of uploaded image files
     * @return IssueComment The created comment model
     * @throws \Exception If transaction fails
     */
    public function addComment(Issue $issue, string $comment, array $images = []): IssueComment
    {
        return DB::transaction(function () use ($issue, $comment, $images) {
            $issueComment = IssueComment::create([
                'issue_id' => $issue->issue_id,
                'user_id' => Auth::id(),
                'comment' => $comment,
                'is_resolution' => false,
            ]);

            // Handle image uploads
            if (!empty($images)) {
                foreach ($images as $image) {
                    $this->uploadAttachment($issue, $issueComment, $image);
                }
            }

            $this->logActivity($issue, 'commented', 'Comment added');

            return $issueComment;
        });
    }

    /**
     * Resolve an issue with resolution comment
     * 
     * This method:
     * - Creates a resolution comment (marked as is_resolution = true)
     * - Optionally attaches images to the resolution comment
     * - Updates issue status to 'resolved'
     * - Records resolution timestamp and resolver user
     * - Logs activity for both issue and linked task (if any)
     * 
     * @param Issue $issue The issue to resolve
     * @param string $resolutionComment The resolution explanation
     * @param array $images Array of uploaded image files
     * @return Issue The resolved issue with fresh relationships
     * @throws \Exception If transaction fails
     */
    public function resolveIssue(Issue $issue, string $resolutionComment, array $images = []): Issue
    {
        return DB::transaction(function () use ($issue, $resolutionComment, $images) {
            // Create resolution comment
            $comment = IssueComment::create([
                'issue_id' => $issue->issue_id,
                'user_id' => Auth::id(),
                'comment' => $resolutionComment,
                'is_resolution' => true,
            ]);

            // Handle image uploads
            if (!empty($images)) {
                foreach ($images as $image) {
                    $this->uploadAttachment($issue, $comment, $image);
                }
            }

            // Update issue status
            $issue->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => Auth::id(),
            ]);

            $this->logActivity($issue, 'resolved', 'Issue resolved');

            // Log to task activity if linked to a task
            if ($issue->linked_task_id) {
                $task = KanbanTask::find($issue->linked_task_id);
                if ($task) {
                    $this->activityService->logIssueResolved($task, $issue->code);
                }
            }

            return $issue->fresh(['comments', 'attachments', 'resolver']);
        });
    }

    /**
     * Upload an attachment file for an issue comment
     * 
     * Stores the file in the 'issue_attachments' directory and creates a database record.
     * File name is prefixed with timestamp to ensure uniqueness.
     * 
     * @param Issue $issue The issue the attachment belongs to
     * @param IssueComment $comment The comment the attachment is linked to
     * @param UploadedFile $file The uploaded file
     * @return IssueAttachment The created attachment model
     */
    protected function uploadAttachment(Issue $issue, IssueComment $comment, UploadedFile $file): IssueAttachment
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('issue_attachments', $fileName, 'public');

        return IssueAttachment::create([
            'issue_id' => $issue->issue_id,
            'comment_id' => $comment->comment_id,
            'uploaded_by' => Auth::id(),
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    /**
     * Log an activity for the issue
     * 
     * Creates an activity record to track all changes and actions performed on the issue.
     * Activities are used for audit trail and history display.
     * 
     * @param Issue $issue The issue to log activity for
     * @param string $type Activity type (created, updated, resolved, commented, etc.)
     * @param string $description Human-readable description of the activity
     * @param array $changes Optional array of field changes ['field' => ['from' => ..., 'to' => ...]]
     * @return void
     */
    public function logActivity(Issue $issue, string $type, string $description, array $changes = []): void
    {
        RiskActivity::create([
            'subject_type' => Issue::class,
            'subject_id' => $issue->issue_id,
            'activity_type' => $type,
            'description' => $description,
            'changes' => $changes,
            'user_id' => Auth::id(),
        ]);
    }
}
