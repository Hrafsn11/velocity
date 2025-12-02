<div class="offcanvas offcanvas-end kanban-update-item-sidebar" tabindex="-1" id="kanban-update-item-sidebar">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Task Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0">
        <input type="hidden" id="current-task-id" value="">
        <div class="nav-align-top">
            <ul class="nav nav-tabs mb-4 rounded-0">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-update">
                        <i class="ti ti-file ti-xs me-1"></i>
                        <span class="align-middle">Detail</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
                        <i class="ti ti-timeline ti-xs me-1"></i>
                        <span class="align-middle">Activity</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content p-0">
            <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                <form>
                    <div class="mb-4">
                        <label class="form-label fw-medium" for="title">Task Title</label>
                        <input type="text" id="title" class="form-control" placeholder="Enter task title" />
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium" for="description">Description</label>
                        <textarea id="description" class="form-control" rows="3" placeholder="Add task description..."></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium" for="priority">Priority</label>
                            <select class="select2 form-select" id="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" for="label">Label</label>
                            <select class="select2 form-select" id="label">
                                <option value="">-- Select Label --</option>
                                <option value="ux">UX</option>
                                <option value="images">Images</option>
                                <option value="info">Info</option>
                                <option value="code_review">Code Review</option>
                                <option value="app">App</option>
                                <option value="charts_maps">Charts & Maps</option>
                                <option value="feature">Feature</option>
                                <option value="bug">Bug</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium" for="due-date">Due Date</label>
                        <input type="text" id="due-date" class="form-control flatpickr-date"
                            placeholder="Select due date" />
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium d-block">Assigned To</label>
                        <select class="select2 form-select" id="assignees" multiple="multiple">
                            @if(isset($workspace))
                                @foreach($workspace->members as $member)
                                    <option value="{{ $member->employee_id }}">
                                        {{ $member->user->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium d-block">Attachments <span id="attachments-count" class="badge bg-label-secondary ms-1">0</span></label>
                        <div id="attachments-list" class="mb-3">
                            <small class="text-muted">No attachments</small>
                        </div>
                        <input type="file" class="form-control" id="attachments" />
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium d-block">Comments <span id="comments-count" class="badge bg-label-secondary ms-1">0</span></label>
                        <div id="comments-list" class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <small class="text-muted">No comments yet</small>
                        </div>
                        <div class="comment-editor border-bottom-0"></div>
                        <div class="d-flex justify-content-end">
                            <div class="comment-toolbar">
                                <span class="ql-formats me-0">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <button class="ql-link"></button>
                                    <button class="ql-image"></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Update Task
                        </button>
                        <button type="button" class="btn btn-label-danger">
                            <i class="ti ti-trash me-1"></i> Delete Task
                        </button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade text-heading" id="tab-activity" role="tabpanel">
                <div id="activity-timeline">
                    <p class="text-muted text-center py-4">
                        <i class="ti ti-timeline-event ti-lg d-block mb-2"></i>
                        No activity yet
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
