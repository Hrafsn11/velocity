@props(['workspaces', 'employees'])

{{-- Add Issue Modal --}}
<div class="modal fade" id="addIssueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('risk.issues.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Report New Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Workspace <span class="text-danger">*</span></label>
                        <select name="workspace_id" id="reportWorkspaceSelect" class="form-select" required>
                            <option value="">Select Workspace</option>
                            @foreach($workspaces as $ws)
                                <option value="{{ $ws->workspace_id }}">{{ $ws->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Related Task <small class="text-muted">(Optional)</small></label>
                        <select name="linked_task_id" id="reportTaskSelect" class="form-select">
                            <option value="">No specific task</option>
                        </select>
                        <small class="text-muted">Select workspace first to see tasks</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assignee</label>
                        <select name="assignee_id" id="reportAssigneeSelect" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->employee_id }}">{{ $emp->user->name }} - {{ $emp->role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Issue Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Priority (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="priority" class="form-control" min="1" max="5" value="3" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Severity (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="severity" class="form-control" min="1" max="5" value="3" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">
                    <i class="ti ti-check me-1"></i>Report Issue
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Create Change Request Modal --}}
<div class="modal fade" id="createCRModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createCRForm" action="{{ route('risk.change-requests.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="issue_id" id="crIssueId">
            <input type="hidden" name="workspace_id" id="crWorkspaceId">
            <input type="hidden" name="type" value="timeline">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Request Timeline Extension</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Creating change request for <strong id="crIssueCode"></strong>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Extension Days <span class="text-danger">*</span></label>
                    <input type="number" name="timeline_extension_days" class="form-control" min="1" max="365" required>
                    <small class="text-muted">How many days to extend the deadline?</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Justification <span class="text-danger">*</span></label>
                    <textarea name="justification" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info">
                    <i class="ti ti-send me-1"></i>Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- View Issue Detail Modal --}}
<div class="modal fade" id="viewIssueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-bug me-2"></i><span id="modalIssueCode"></span> - <span id="modalIssueTitle"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="issueDetailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="btnResolveFromDetail" class="btn btn-success d-none" onclick="openResolveModalFromDetail()">
                    <i class="ti ti-check me-1"></i>Resolve Issue
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Resolve Issue Modal --}}
<div class="modal fade" id="resolveIssueModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="resolveIssueForm" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">
                    <i class="ti ti-check-circle me-2"></i>Resolve Issue: <span id="resolveIssueCode"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <i class="ti ti-info-circle me-2"></i>
                    Mark this issue as resolved. Comment and proof images are optional.
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Resolution Comment <small class="text-muted">(Optional)</small></label>
                    <textarea name="resolution_comment" class="form-control" rows="4" 
                              placeholder="Optional: Explain how you resolved this issue..."></textarea>
                    <small class="text-muted">You can leave this empty if not needed</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Proof Images <small class="text-muted">(Optional)</small></label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                    <small class="text-muted">Optional: Upload screenshots as proof</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i class="ti ti-check me-1"></i>Resolve Issue
                </button>
            </div>
        </form>
    </div>
</div>
