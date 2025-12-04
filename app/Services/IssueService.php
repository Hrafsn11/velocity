<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\RiskActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class IssueService
{
    /**
     * Generate unique code for issue per workspace
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
     * Create new issue
     */
    public function createIssue(array $data): Issue
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $this->generateCode($data['workspace_id']);
            $data['created_by'] = auth()->id();

            $issue = Issue::create($data);

            $this->logActivity($issue, 'created', 'Issue created');

            return $issue->load(['workspace', 'risk', 'assignee', 'creator']);
        });
    }

    /**
     * Update issue
     */
    public function updateIssue(Issue $issue, array $data): Issue
    {
        return DB::transaction(function () use ($issue, $data) {
            $changes = $this->detectChanges($issue, $data);

            $issue->update($data);

            if (!empty($changes)) {
                $this->logActivity($issue, 'updated', 'Issue updated', $changes);
            }

            return $issue->fresh(['workspace', 'risk', 'assignee', 'creator']);
        });
    }

    /**
     * Get issues by workspace with filters
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
     * Detect changes
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
     * Add comment to issue
     */
    public function addComment(Issue $issue, string $comment, array $images = []): \App\Models\IssueComment
    {
        return DB::transaction(function () use ($issue, $comment, $images) {
            $issueComment = \App\Models\IssueComment::create([
                'issue_id' => $issue->issue_id,
                'user_id' => auth()->id(),
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
     * Resolve issue with comment
     */
    public function resolveIssue(Issue $issue, string $resolutionComment, array $images = []): Issue
    {
        return DB::transaction(function () use ($issue, $resolutionComment, $images) {
            // Create resolution comment
            $comment = \App\Models\IssueComment::create([
                'issue_id' => $issue->issue_id,
                'user_id' => auth()->id(),
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
                'resolved_by' => auth()->id(),
            ]);

            $this->logActivity($issue, 'resolved', 'Issue resolved');

            return $issue->fresh(['comments', 'attachments', 'resolver']);
        });
    }

    /**
     * Upload attachment
     */
    protected function uploadAttachment(Issue $issue, \App\Models\IssueComment $comment, $file): \App\Models\IssueAttachment
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('issue_attachments', $fileName, 'public');

        return \App\Models\IssueAttachment::create([
            'issue_id' => $issue->issue_id,
            'comment_id' => $comment->comment_id,
            'uploaded_by' => auth()->id(),
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    /**
     * Log activity
     */
    public function logActivity(Issue $issue, string $type, string $description, array $changes = []): void
    {
        RiskActivity::create([
            'subject_type' => Issue::class,
            'subject_id' => $issue->issue_id,
            'activity_type' => $type,
            'description' => $description,
            'changes' => $changes,
            'user_id' => auth()->id(),
        ]);
    }
}
