<?php

namespace App\Services;

use App\Models\Risk;
use App\Models\Issue;
use App\Models\RiskActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Risk Service
 * 
 * Handles all business logic related to risk management including creation,
 * updating, conversion to issues, and activity logging.
 * 
 * @package App\Services
 */
class RiskService
{
    /**
     * Generate unique sequential code for risk within a workspace
     * 
     * Format: R001, R002, R003, etc.
     * 
     * @param string $workspaceId The workspace ID to generate code for
     * @return string Generated risk code
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
     * Create a new risk with automatic code generation and urgency calculation
     * 
     * This method handles the complete risk creation process including:
     * - Generating unique risk code
     * - Calculating urgency based on probability × impact
     * - Setting the creator
     * - Logging the creation activity
     * 
     * @param array $data Risk data including workspace_id, description, probability, impact, etc.
     * @return Risk The created risk with loaded relationships
     * @throws \Exception If transaction fails
     */
    public function createRisk(array $data): Risk
    {
        return DB::transaction(function () use ($data) {
            // Generate unique code for this workspace
            $data['code'] = $this->generateCode($data['workspace_id']);
            
            // Calculate urgency based on risk score
            $score = $data['probability'] * $data['impact'];
            $data['urgency'] = $this->calculateUrgency($score);
            
            // Set the authenticated user as creator
            $data['created_by'] = Auth::id();

            $risk = Risk::create($data);

            // Log creation activity
            $this->logActivity($risk, 'created', 'Risk created');

            return $risk->load(['workspace', 'creator']);
        });
    }

    /**
     * Update an existing risk and recalculate urgency if needed
     * 
     * Automatically recalculates urgency when probability or impact changes.
     * Logs all changes made to tracked fields.
     * 
     * @param Risk $risk The risk to update
     * @param array $data Updated risk data
     * @return Risk The updated risk with fresh relationships
     * @throws \Exception If transaction fails
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
     * Convert a risk to an issue when it materializes
     * 
     * This method:
     * - Creates a new issue linked to the risk
     * - Copies the affected module/task from risk to issue
     * - Updates risk status to 'materialized'
     * - Logs activities for both risk and issue
     * 
     * @param Risk $risk The risk to convert
     * @param array $issueData Issue data (title, description, priority, etc.)
     * @return Issue The created issue with loaded relationships
     * @throws \Exception If transaction fails
     */
    public function convertToIssue(Risk $risk, array $issueData): Issue
    {
        return DB::transaction(function () use ($risk, $issueData) {
            // Generate issue code
            $issueData['code'] = app(IssueService::class)->generateCode($risk->workspace_id);
            $issueData['workspace_id'] = $risk->workspace_id;
            $issueData['risk_id'] = $risk->risk_id;
            $issueData['created_by'] = Auth::id();
            
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
     * Get all risks for a specific workspace with optional filters
     * 
     * Supports filtering by status, urgency, and category.
     * Always eager loads workspace and creator relationships.
     * 
     * @param string $workspaceId The workspace ID to fetch risks for
     * @param array $filters Optional filters ['status' => '...', 'urgency' => '...', 'category' => '...']
     * @return Collection Collection of Risk models with relationships
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
     * Get comprehensive dashboard statistics for risks
     * 
     * Returns risk counts grouped by status and urgency.
     * Can be filtered by workspace or return global statistics.
     * 
     * @param string|null $workspaceId Optional workspace ID to filter by
     * @return array Statistics array with 'total', 'by_status', and 'by_urgency' keys
     */
    public function getDashboardStats(?string $workspaceId = null): array
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
     * Calculate urgency level based on risk score
     * 
     * Risk score = probability × impact (1-25)
     * - Critical: score >= 20
     * - High: score >= 15
     * - Medium: score >= 10
     * - Low: score < 10
     * 
     * @param int $score The risk score (probability × impact)
     * @return string Urgency level: 'Critical', 'High', 'Medium', or 'Low'
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
     * Detect changes between current risk data and new data
     * 
     * Tracks changes to: description, probability, impact, status, and mitigation_actions
     * Returns an array of changes in format ['field' => ['from' => old, 'to' => new]]
     * 
     * @param Risk $risk The current risk model
     * @param array $newData The new data being applied
     * @return array Array of changes detected
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
     * Log an activity for the risk
     * 
     * Creates a risk activity record to track all changes and actions performed on the risk.
     * Activities are used for audit trail and history display.
     * 
     * @param Risk $risk The risk to log activity for
     * @param string $type Activity type (created, updated, converted, etc.)
     * @param string $description Human-readable description of the activity
     * @param array $changes Optional array of field changes ['field' => ['from' => ..., 'to' => ...]]
     * @return void
     */
    public function logActivity(Risk $risk, string $type, string $description, array $changes = []): void
    {
        RiskActivity::create([
            'subject_type' => Risk::class,
            'subject_id' => $risk->risk_id,
            'activity_type' => $type,
            'description' => $description,
            'changes' => $changes,
            'user_id' => Auth::id(),
        ]);
    }
}
