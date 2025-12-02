<div class="app-kanban p-4 h-100" data-workspace-id="{{ $summary['workspace']->workspace_id ?? '' }}">
    <div class="mb-3">
        <form class="kanban-add-new-board">
            <label class="kanban-add-board-btn" for="kanban-add-board-input">
                <i class="ti ti-plus bg-primary text-white p-2 rounded"></i>
            </label>
            <input type="text" class="form-control w-px-250 kanban-add-board-input mb-4 d-none"
                placeholder="Add Board Title" id="kanban-add-board-input" required />
            <div class="mb-4 kanban-add-board-input d-none">
                <button class="btn btn-primary btn-sm me-4">Add</button>
                <button type="button" class="btn btn-label-secondary btn-sm kanban-add-board-cancel-btn">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    <div class="kanban-wrapper"></div>
</div>
