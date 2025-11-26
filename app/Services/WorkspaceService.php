<?php

namespace App\Services;

use App\Enums\WorkspaceStatus;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class WorkspaceService
{
    public function list(): Collection
    {
        return Workspace::query()
            ->with(['manager.user', 'members.user'])
            ->latest()
            ->get();
    }

    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return Workspace::query()
            ->with(['manager.user', 'members.user'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Workspace
    {
        return DB::transaction(function () use ($data) {
            $prepared = $this->preparePayload($data);
            $members = $prepared['members'] ?? [];
            unset($prepared['members']);

            /** @var Workspace $workspace */
            $workspace = Workspace::create($prepared);
            $workspace->members()->sync($this->normalizeMembers($members, $workspace->manager_id));

            return $workspace->load(['manager.user', 'members.user']);
        });
    }

    public function update(Workspace $workspace, array $data): Workspace
    {
        return DB::transaction(function () use ($workspace, $data) {
            $prepared = $this->preparePayload($data, $workspace);
            $members = $prepared['members'] ?? [];
            unset($prepared['members']);

            $workspace->update($prepared);
            $workspace->members()->sync($this->normalizeMembers($members, $workspace->manager_id));

            return $workspace->load(['manager.user', 'members.user']);
        });
    }

    public function delete(Workspace $workspace): void
    {
        DB::transaction(function () use ($workspace) {
            if ($workspace->image_path) {
                Storage::disk('public')->delete($workspace->image_path);
            }

            $workspace->delete();
        });
    }

    /**
     * Prepare a summary payload for workspace overview.
     * Returns metadata, member previews, progress and recent activities.
     *
     * @param  string|Workspace  $workspace
     * @return array<string,mixed>
     */
    public function summary(string|Workspace $workspace): array
    {
        if ($workspace instanceof Workspace) {
            $w = $workspace->loadMissing(['manager.user', 'members.user']);
        } else {
            $w = Workspace::with(['manager.user', 'members.user'])
                ->where('workspace_id', $workspace)
                ->firstOrFail();
        }

        // Map members to a presentation-friendly structure
        $membersAll = $w->members->map(function ($m) {
            $avatar = $m->user->avatar ?? null;
            $avatarUrl = null;

            try {
                if ($avatar && Storage::disk('public')->exists($avatar)) {
                    $avatarUrl = asset('storage/' . ltrim($avatar, '/'));
                } elseif ($avatar && file_exists(public_path('assets/img/avatars/' . $avatar))) {
                    $avatarUrl = asset('assets/img/avatars/' . $avatar);
                }
            } catch (\Throwable $e) {
                // Leave avatarUrl null on error
            }

            return [
                'employee_id' => $m->employee_id,
                'name' => $m->user->name ?? null,
                'avatar' => $avatar,
                'avatar_url' => $avatarUrl,
                'role' => $m->role ?? null,
                'level' => $m->level ?? null,
                'level_badge' => $m->level_badge ?? null,
                'role_badge' => $m->role_badge ?? null,
            ];
        });

        $members = $membersAll->take(6)->values();

        $membersCount = $w->members->count();

        // Dates and progress
        $start = $w->start_date ? $w->start_date->format('Y-m-d') : null;
        $end = $w->end_date ? $w->end_date->format('Y-m-d') : null;
        $daysLeft = $w->end_date ? now()->diffInDays($w->end_date, false) : null;
        $progress = $w->progress ?? 0;

        // Recent activities, if the activity_log table exists
        $recentActivities = [];
        try {
            if (Schema::hasTable('activity_log')) {
                $recentActivities = DB::table('activity_log')
                    ->where('subject_id', $w->workspace_id)
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'user' => $row->causer_type ? class_basename($row->causer_type) : 'System',
                            'action' => $row->description ?? '',
                            'time' => $row->created_at,
                        ];
                    })->toArray();
            }
        } catch (\Throwable $e) {
            // keep recentActivities empty on error
        }

        return [
            'workspace' => $w,
            'members' => $members,
            'members_full' => $membersAll->values(),
            'members_count' => $membersCount,
            'start_date' => $start,
            'end_date' => $end,
            'days_left' => $daysLeft,
            'progress' => $progress,
            'recent_activities' => $recentActivities,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function preparePayload(array $data, ?Workspace $workspace = null): array
    {
        $payload = Arr::only($data, [
            'title',
            'description',
            'start_date',
            'end_date',
            'manager_id',
            'status',
            'members',
        ]);

        if (isset($payload['status']) && $payload['status'] instanceof WorkspaceStatus) {
            $payload['status'] = $payload['status']->value;
        }

        if (isset($data['image']) && $data['image']) {
            if ($workspace?->image_path) {
                Storage::disk('public')->delete($workspace->image_path);
            }

            $payload['image_path'] = $data['image']->store('workspace-images', 'public');
        }

        return $payload;
    }

    /**
     * Ensure manager is always part of members for visibility.
     *
     * @param  array<int,string>  $memberIds
     * @return array<int,string>
     */
    private function normalizeMembers(array $memberIds, string $managerId): array
    {
        $collection = collect($memberIds)->filter()->push($managerId);

        return $collection->unique()->values()->all();
    }
}

