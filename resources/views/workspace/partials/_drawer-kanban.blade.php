<div class="offcanvas offcanvas-end kanban-update-item-sidebar" tabindex="-1" id="kanban-update-item-sidebar">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0">
        <div class="nav-align-top">
            <ul class="nav nav-tabs mb-5 rounded-0">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-update">
                        <i class="ti ti-edit ti-18px me-1_5"></i>
                        <span class="align-middle">Edit</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
                        <i class="ti ti-chart-pie-2 ti-18px me-1_5"></i>
                        <span class="align-middle">Activity</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content p-0">
            <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                <form>
                    <div class="mb-5">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" id="title" class="form-control" placeholder="Enter Title" />
                    </div>
                    <div class="mb-5">
                        <label class="form-label" for="due-date">Due Date</label>
                        <input type="text" id="due-date" class="form-control flatpickr-date"
                            placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="mb-5">
                        <label class="form-label" for="label">Label</label>
                        <select class="select2 select2-label form-select" id="label">
                            <option data-color="bg-label-success" value="UX">UX</option>
                            <option data-color="bg-label-warning" value="Images">Images</option>
                            <option data-color="bg-label-info" value="Info">Info</option>
                            <option data-color="bg-label-danger" value="Code Review">Code Review</option>
                            <option data-color="bg-label-secondary" value="App">App</option>
                            <option data-color="bg-label-primary" value="Charts & Maps">Charts & Maps</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Assigned</label>
                        <div class="assigned d-flex flex-wrap"></div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label" for="attachments">Attachments</label>
                        <input type="file" class="form-control" id="attachments" />
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Comment</label>
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
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="offcanvas">Update</button>
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Delete</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade text-heading" id="tab-activity" role="tabpanel">
                <div class="media mb-4 d-flex align-items-center">
                    <div class="avatar me-3 flex-shrink-0">
                        <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
                    </div>
                    <div class="media-body">
                        <p class="mb-0"><span>Jordan</span> Left the board.</p>
                        <small class="text-muted">Today 11:00 AM</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
