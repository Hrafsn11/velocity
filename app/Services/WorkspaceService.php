<?php

namespace App\Services;

use App\Enums\WorkspaceStatus;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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

