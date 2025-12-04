<?php

namespace App\Services;

use App\Models\Risk;
use App\Models\Issue;
use App\Models\RiskActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RiskService
{
    /**
     * Generate unique code for risk per workspace
     */
    public function generateCode(string $workspaceId): string
    {
        $lastRisk = Risk::where('workspace_id', $workspaceId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastRisk) {
            return 'R001';
        }

        $lastNumber = (int) substr($lastRisk->code, 1);
        return 'R' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create new risk
     */
    public function createRisk(array $data): Risk
    {
        return DB::transaction(function () use ($data) {
            // Generate code
            $data['code'] = $this->generateCode($data['workspace_id']);
            
            // Calculate urgency
            $score = $data['probability'] * $data['impact'];
            $data['urgency'] = $this->calculateUrgency($score);
            
            // Set creator
            $data['created_by'] = auth()->id();

            $risk = Risk::create($data);

            // Log activity
            $this->logActivity($risk, 'created', 'Risk created');

            return $risk->load(['workspace', 'creator']);
        });
    }

    /**
     * Update risk
     */
    public function updateRisk(Risk $risk, array $data): Risk
    {
        return DB::transaction(function () use ($risk, $data) {
            $changes = $this->detectChanges($risk, $data);

            // Recalculate urgency if probability/impact changed
            if (isset($data['probability']) || isset($data['impact'])) {
                $probability = $data['probability'] ?? $risk->probability;
                $impact = $data['impact'] ?? $risk->impact;
                $score = $probability * $impact;
                $data['urgency'] = $this->calculateUrgency($score);
            }

            $risk->update($data);

            // Log if there are changes
            if (!empty($changes)) {
                $this->logActivity($risk, 'updated', 'Risk updated', $changes);
            }

            return $risk->fresh(['workspace', 'creator']);
        });
    }

    /**
     * Convert risk to issue
     */
    public function convertToIssue(Risk $risk, array $issueData): Issue
    {
        return DB::transaction(function () use ($risk, $issueData) {
            // Generate issue code
            $issueData['code'] = app(IssueService::class)->generateCode($risk->workspace_id);
            $issueData['workspace_id'] = $risk->workspace_id;
            $issueData['risk_id'] = $risk->risk_id;
            $issueData['created_by'] = auth()->id();
            
            // Copy affected_module from risk to linked_task_id (now both store task_id ULID)
            if (!isset($issueData['linked_task_id']) && $risk->affected_module) {
                $issueData['linked_task_id'] = $risk->affected_module;
            }

            $issue = Issue::create($issueData);

            // Update risk status
            $risk->update(['status' => 'materialized']);

            // Log activities
            $this->logActivity($risk, 'converted', "Risk converted to Issue {$issue->code}");
            app(IssueService::class)->logActivity($issue, 'created', "Issue created from Risk {$risk->code}");

            return $issue->load(['workspace', 'risk', 'creator', 'linkedTask']);
        });
    }

    /**
     * Get risks by workspace with filters
     */
    public function getRisksByWorkspace(string $workspaceId, array $filters = []): Collection
    {
        $query = Risk::where('workspace_id', $workspaceId)
            ->with(['workspace', 'creator']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['urgency'])) {
            $query->where('urgency', $filters['urgency']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->latest()->get();
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(string $workspaceId = null)
    {
        $query = Risk::query();

        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }

        return [
            'total' => $query->count(),
            'by_status' => [
                'active' => (clone $query)->where('status', 'active')->count(),
                'monitoring' => (clone $query)->where('status', 'monitoring')->count(),
                'mitigated' => (clone $query)->where('status', 'mitigated')->count(),
                'materialized' => (clone $query)->where('status', 'materialized')->count(),
                'closed' => (clone $query)->where('status', 'closed')->count(),
            ],
            'by_urgency' => [
                'critical' => (clone $query)->where('urgency', 'Critical')->count(),
                'high' => (clone $query)->where('urgency', 'High')->count(),
                'medium' => (clone $query)->where('urgency', 'Medium')->count(),
                'low' => (clone $query)->where('urgency', 'Low')->count(),
            ],
        ];
    }

    /**
     * Calculate urgency from score
     */
    protected function calculateUrgency(int $score): string
    {
        return match(true) {
            $score >= 20 => 'Critical',
            $score >= 15 => 'High',
            $score >= 10 => 'Medium',
            default => 'Low',
        };
    }

    /**
     * Detect changes between old and new data
     */
    protected function detectChanges(Risk $risk, array $newData): array
    {
        $changes = [];
        $trackFields = ['description', 'probability', 'impact', 'status', 'mitigation_actions'];

        foreach ($trackFields as $field) {
            if (isset($newData[$field]) && $newData[$field] != $risk->$field) {
                $changes[$field] = [
                    'from' => $risk->$field,
                    'to' => $newData[$field],
                ];
            }
        }

        return $changes;
    }

    /**
     * Log activity
     */
    public function logActivity(Risk $risk, string $type, string $description, array $changes = []): void
    {
        RiskActivity::create([
            'subject_type' => Risk::class,
            'subject_id' => $risk->risk_id,
            'activity_type' => $type,
            'description' => $description,
            'changes' => $changes,
            'user_id' => auth()->id(),
        ]);
    }
}
