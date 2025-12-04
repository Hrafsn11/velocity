@props(['workspaces'])

{{-- Add Risk Modal --}}
<div class="modal fade" id="addRiskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('risk.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Risk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Workspace <span class="text-danger">*</span></label>
                        <select name="workspace_id" id="addWorkspaceSelect" class="form-select" required>
                            <option value="">Select Workspace</option>
                            @foreach($workspaces as $ws)
                                <option value="{{ $ws->workspace_id }}">{{ $ws->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" id="addCategory" class="form-select" required>
                            <option value="Technical">Technical</option>
                            <option value="SDM">SDM</option>
                            <option value="Financial">Financial</option>
                            <option value="Timeline">Timeline</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Risk Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Cause/Root Problem</label>
                        <textarea name="cause" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Affected Module/Task <small class="text-muted">(Optional)</small></label>
                        <select name="affected_module" id="addTaskSelect" class="form-select">
                            <option value="">Not related to specific task</option>
                        </select>
                        <small class="text-muted">Select workspace first to see tasks</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Probability (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="probability" class="form-control" min="1" max="5" value="3" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Impact (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="impact" class="form-control" min="1" max="5" value="3" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mitigation Actions</label>
                        <textarea name="mitigation_actions" class="form-control" rows="3" placeholder="List the actions to mitigate this risk...&#10;1. Action one&#10;2. Action two"></textarea>
                        <small class="text-muted">Required to mark as mitigated later</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check me-1"></i>Create Risk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Risk Modal --}}
<div class="modal fade" id="editRiskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editRiskForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Risk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Workspace <span class="text-danger">*</span></label>
                        <select name="workspace_id" id="editWorkspaceSelect" class="form-select" required>
                            @foreach($workspaces as $ws)
                                <option value="{{ $ws->workspace_id }}">{{ $ws->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" id="editCategory" class="form-select" required>
                            <option value="Technical">Technical</option>
                            <option value="SDM">SDM</option>
                            <option value="Financial">Financial</option>
                            <option value="Timeline">Timeline</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Risk Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Cause/Root Problem</label>
                        <textarea name="cause" id="editCause" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Affected Module/Task</label>
                        <select name="affected_module" id="editTaskSelect" class="form-select">
                            <option value="">Not related to specific task</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Probability (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="probability" id="editProbability" class="form-control" min="1" max="5" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Impact (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="impact" id="editImpact" class="form-control" min="1" max="5" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mitigation Actions</label>
                        <textarea name="mitigation_actions" id="editMitigation" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check me-1"></i>Update Risk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Convert to Issue Modal --}}
<div class="modal fade" id="convertIssueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="convertIssueForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">Convert Risk to Issue</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Converting <strong id="convertRiskCode"></strong> to an Issue. This will mark the risk as "materialized".
                </div>
                <input type="hidden" id="convertWorkspaceId" name="workspace_id">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Issue Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assign To <small class="text-muted">(Member of this workspace)</small></label>
                        <select name="assignee_id" id="convertAssigneeSelect" class="form-select">
                            <option value="">Unassigned</option>
                        </select>
                        <small class="text-muted">Will be populated with workspace members</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Priority (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="priority" class="form-control" min="1" max="5" value="5" required>
                        <small class="text-muted">1=Low, 5=Critical</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Severity (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="severity" class="form-control" min="1" max="5" value="5" required>
                        <small class="text-muted">1=Minor, 5=Critical</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">
                    <i class="ti ti-arrow-right me-1"></i>Convert to Issue
                </button>
            </div>
        </form>
    </div>
</div>

{{-- View Risk Details Modal --}}
<div class="modal fade" id="viewRiskModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-alert-triangle text-danger me-2"></i>
                    Risk Details: <span class="badge bg-label-danger" id="viewRiskCode">#RISK-001</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Overview Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-1">Workspace</small>
                                <span class="badge bg-label-primary" id="viewRiskWorkspace">Loading...</span>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-1">Category</small>
                                <span class="badge bg-label-secondary" id="viewRiskCategory">Loading...</span>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block mb-1">Risk Score</small>
                                <span class="badge" id="viewRiskScore">0/25</span>
                                <br><small class="text-muted"><span id="viewRiskProbability">0</span> × <span id="viewRiskImpact">0</span></small>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block mb-1">Urgency</small>
                                <span class="badge" id="viewRiskUrgency">Medium</span>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted d-block mb-1">Status</small>
                                <span class="badge" id="viewRiskStatus">ACTIVE</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="ti ti-file-description me-2"></i>Description</h6>
                        <p class="mb-0" id="viewRiskDescription">Loading...</p>
                    </div>
                </div>

                {{-- Root Cause (Conditional) --}}
                <div class="card mb-4" id="viewRiskCauseSection" style="display:none;">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="ti ti-search me-2"></i>Root Cause / Problem</h6>
                        <p class="mb-0" id="viewRiskCause">-</p>
                    </div>
                </div>

                {{-- Affected Task (Conditional) --}}
                <div class="card mb-4 border-primary" id="viewRiskTaskSection" style="display:none;">
                    <div class="card-body">
                        <h6 class="card-title mb-2"><i class="ti ti-link me-2 text-primary"></i>Affected Kanban Task</h6>
                        <div class="d-flex align-items-center">
                            <i class="ti ti-clipboard-check ti-md text-primary me-3"></i>
                            <strong id="viewRiskTaskTitle">-</strong>
                        </div>
                    </div>
                </div>

                {{-- Mitigation Actions (Conditional) --}}
                <div class="card mb-4 border-success" id="viewRiskMitigationSection" style="display:none;">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="ti ti-shield-check me-2 text-success"></i>Mitigation Actions</h6>
                        <div class="alert alert-success mb-0">
                            <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;" id="viewRiskMitigation">-</pre>
                        </div>
                    </div>
                </div>

                {{-- Related Issues --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="ti ti-bug me-2"></i>Related Issues</h6>
                        <div class="list-group list-group-flush" id="viewRiskIssues"></div>
                        <div class="text-center py-4" id="viewRiskIssuesEmpty" style="display:none;">
                            <i class="ti ti-circle-off ti-xl text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">No issues created from this risk yet</p>
                        </div>
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Created <span id="viewRiskCreatedAt">-</span> by <strong id="viewRiskCreator">-</strong>
                        </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
