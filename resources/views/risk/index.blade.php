@extends('layouts.app')

@section('title', 'Risk List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="ti ti-alert-triangle text-primary me-2"></i>All Risks
            </h4>
            <p class="text-muted mb-0">Manage risks across all workspaces</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRiskModal">
            <i class="ti ti-plus me-1"></i>Add New Risk
        </button>
    </div>

    {{-- Workspace Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('risk.index') }}" class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label mb-1 small">
                        <i class="ti ti-filter me-1"></i>Filter by Workspace
                    </label>
                    <select class="form-select form-select-sm" name="workspace_id" onchange="this.form.submit()">
                        <option value="">All Workspaces</option>
                        @foreach($workspaces as $ws)
                            <option value="{{ $ws->workspace_id }}" {{ $workspaceId == $ws->workspace_id ? 'selected' : '' }}>
                                {{ $ws->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-end mt-2 mt-md-0">
                    @if($workspaceId)
                        <a href="{{ route('risk.index') }}" class="btn btn-sm btn-label-secondary">
                            <i class="ti ti-x me-1"></i>Clear Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1">{{ $stats['total'] }}</h5>
                            <small class="text-muted">Total Risks</small>
                        </div>
                        <span class="badge bg-label-primary rounded-circle p-2">
                            <i class="ti ti-alert-triangle ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-danger">{{ $stats['critical'] }}</h5>
                            <small class="text-muted">Critical</small>
                        </div>
                        <span class="badge bg-label-danger rounded-circle p-2">
                            <i class="ti ti-flame ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-warning">{{ $stats['active'] }}</h5>
                            <small class="text-muted">Active</small>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-alert-circle ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-success">{{ $stats['mitigated'] }}</h5>
                            <small class="text-muted">Mitigated</small>
                        </div>
                        <span class="badge bg-label-success rounded-circle p-2">
                            <i class="ti ti-shield-check ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Risk List Table --}}
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Workspace</th>
                        <th>Category</th>
                        <th>Score</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($risks as $risk)
                    <tr>
                        <td>
                            <strong class="text-primary">#{{ $risk->code }}</strong>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                <strong>{{ Str::limit($risk->description, 60) }}</strong>
                                @if($risk->cause)
                                    <br><small class="text-muted"><i class="ti ti-alert-circle me-1"></i>{{ Str::limit($risk->cause, 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">
                                {{ strtoupper(substr($risk->workspace->title, 0, 2)) }}
                            </span>
                            <br><small class="text-muted">{{ $risk->workspace->title }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $risk->category_badge }}">{{ $risk->category }}</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <strong>{{ $risk->score }}</strong>
                                <br><small class="text-muted">{{ $risk->probability }}x{{ $risk->impact }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $risk->urgency_badge }}">{{ $risk->urgency }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $risk->status_badge }}">{{ ucfirst($risk->status) }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-label-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewRisk({{ json_encode($risk) }})">
                                        <i class="ti ti-eye me-1"></i>View Details
                                    </a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="editRisk({{ json_encode($risk) }})">
                                        <i class="ti ti-edit me-1"></i>Edit
                                    </a></li>
                                    @if($risk->status == 'active' && $risk->mitigation_actions)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('risk.mark-mitigated', $risk->risk_id) }}" method="POST" class="mark-mitigated-form">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-success">
                                                <i class="ti ti-shield-check me-1"></i>Mark as Mitigated
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @if($risk->canConvertToIssue())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="javascript:void(0)" onclick="convertToIssue('{{ $risk->risk_id }}', '{{ $risk->code }}', '{{ $risk->workspace_id }}')">
                                        <i class="ti ti-arrow-right me-1"></i>Convert to Issue
                                    </a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('risk.destroy', $risk->risk_id) }}" method="POST" class="delete-risk-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ti ti-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="ti ti-alert-circle ti-lg mb-2"></i>
                            <p class="mb-0">No risks found. Click "Add New Risk" to create one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

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

{{-- View Details Modal --}}
<div class="modal fade" id="viewRiskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Risk Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Risk Code</label>
                        <p id="viewCode" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Workspace</label>
                        <p id="viewWorkspace" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category</label>
                        <p id="viewCategory" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <p id="viewStatus" class="mb-0"></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <p id="viewDescription" class="mb-0"></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Cause/Root Problem</label>
                        <p id="viewCause" class="mb-0 text-muted"></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Affected Module/Task</label>
                        <p id="viewAffectedModule" class="mb-0 text-muted"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Probability</label>
                        <p id="viewProbability" class="mb-0"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Impact</label>
                        <p id="viewImpact" class="mb-0"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Risk Score</label>
                        <p id="viewScore" class="mb-0"></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Urgency Level</label>
                        <p id="viewUrgency" class="mb-0"></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Mitigation Actions</label>
                        <div id="viewMitigation" class="border rounded p-3 bg-label-success"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Created By</label>
                        <p id="viewCreator" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Created At</label>
                        <p id="viewCreatedAt" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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

@endsection

@push('scripts')
<script>
// Workspace tasks data
const workspaceTasks = @json($workspaces->mapWithKeys(function($ws) {
    return [$ws->workspace_id => $ws->kanbanTasks];
}));

// Workspace members data
const workspaceMembers = @json($workspaces->mapWithKeys(function($ws) {
    return [$ws->workspace_id => $ws->members];
}));

// Load tasks for Add form
function loadTasksForAdd(workspaceId) {
    const select = $('#addTaskSelect');
    const currentValue = select.val();
    select.empty().append('<option value="">Not related to specific task</option>');
    
    if (workspaceId && workspaceTasks[workspaceId]) {
        workspaceTasks[workspaceId].forEach(task => {
            const option = new Option(task.title, task.task_id);
            select.append(option);
        });
    }
    
    // Trigger Select2 update
    select.trigger('change');
}

// Load tasks for Edit form
function loadTasksForEdit(workspaceId) {
    const select = $('#editTaskSelect');
    const currentValue = select.data('currentValue') || select.val() || '';
    select.empty().append('<option value="">Not related to specific task</option>');
    
    if (workspaceId && workspaceTasks[workspaceId]) {
        workspaceTasks[workspaceId].forEach(task => {
            const option = new Option(task.title, task.task_id, false, task.task_id === currentValue);
            select.append(option);
        });
    }
    
    // Trigger Select2 update
    select.val(currentValue).trigger('change');
}

// View Risk Details
function viewRisk(risk) {
    document.getElementById('viewCode').innerHTML = '<strong class="text-primary">#' + risk.code + '</strong>';
    document.getElementById('viewWorkspace').innerHTML = '<span class="badge bg-label-primary">' + risk.workspace.title + '</span>';
    document.getElementById('viewCategory').innerHTML = '<span class="badge bg-label-primary">' + risk.category + '</span>';
    document.getElementById('viewStatus').innerHTML = '<span class="badge ' + (risk.status === 'active' ? 'bg-label-success' : 'bg-label-secondary') + '">' + risk.status.charAt(0).toUpperCase() + risk.status.slice(1) + '</span>';
    document.getElementById('viewDescription').textContent = risk.description;
    document.getElementById('viewCause').textContent = risk.cause || '-';
    // Show task title instead of ULID
    document.getElementById('viewAffectedModule').textContent = risk.affected_task ? risk.affected_task.title : 'Not related to specific task';
    document.getElementById('viewProbability').innerHTML = '<strong>' + risk.probability + '</strong>/5';
    document.getElementById('viewImpact').innerHTML = '<strong>' + risk.impact + '</strong>/5';
    document.getElementById('viewScore').innerHTML = '<strong class="text-danger">' + risk.score + '</strong>';
    document.getElementById('viewUrgency').innerHTML = '<span class="badge bg-' + (risk.urgency === 'Critical' ? 'danger' : risk.urgency === 'High' ? 'warning' : 'info') + '">' + risk.urgency + '</span>';
    
    if (risk.mitigation_actions) {
        document.getElementById('viewMitigation').innerHTML = '<pre class="mb-0" style="white-space: pre-wrap;">' + risk.mitigation_actions + '</pre>';
    } else {
        document.getElementById('viewMitigation').innerHTML = '<span class="text-muted">No mitigation actions defined</span>';
    }
    
    document.getElementById('viewCreator').textContent = risk.creator ? risk.creator.name : '-';
    document.getElementById('viewCreatedAt').textContent = new Date(risk.created_at).toLocaleString();
    
    var modal = new bootstrap.Modal(document.getElementById('viewRiskModal'));
    modal.show();
}

// Edit Risk
function editRisk(risk) {
    document.getElementById('editRiskForm').action = '/risk/' + risk.risk_id;
    document.getElementById('editWorkspaceSelect').value = risk.workspace_id;
    document.getElementById('editCategory').value = risk.category;
    document.getElementById('editDescription').value = risk.description;
    document.getElementById('editCause').value = risk.cause || '';
    document.getElementById('editProbability').value = risk.probability;
    document.getElementById('editImpact').value = risk.impact;
    document.getElementById('editMitigation').value = risk.mitigation_actions || '';
    
    // Load tasks and set affected module
    const editTaskSelect = document.getElementById('editTaskSelect');
    editTaskSelect.dataset.currentValue = risk.affected_module || '';
    loadTasksForEdit(risk.workspace_id);
    
    var modal = new bootstrap.Modal(document.getElementById('editRiskModal'));
    modal.show();
}

// Convert to Issue
function convertToIssue(riskId, riskCode, workspaceId) {
    document.getElementById('convertRiskCode').textContent = riskCode;
    document.getElementById('convertIssueForm').action = '/risk/' + riskId + '/convert-to-issue';
    document.getElementById('convertWorkspaceId').value = workspaceId;
    
    // Load workspace members for assignee dropdown
    const assigneeSelect = document.getElementById('convertAssigneeSelect');
    assigneeSelect.innerHTML = '<option value="">Unassigned</option>';
    
    if (workspaceId && workspaceMembers[workspaceId]) {
        workspaceMembers[workspaceId].forEach(member => {
            const option = document.createElement('option');
            option.value = member.employee_id;
            option.textContent = member.user.name + ' - ' + member.role;
            assigneeSelect.appendChild(option);
        });
    }
    
    var modal = new bootstrap.Modal(document.getElementById('convertIssueModal'));
    modal.show();
}

// Show success/error messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        timer: 3000
    });
@endif

// Handle Mark as Mitigated with SweetAlert
document.querySelectorAll('.mark-mitigated-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Mark as Mitigated?',
            text: "This will mark the risk as successfully mitigated.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-shield-check me-1"></i> Yes, Mark as Mitigated',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

// Handle Delete Risk with SweetAlert
document.querySelectorAll('.delete-risk-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Delete Risk?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-trash me-1"></i> Yes, Delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

// ===== Initialize Select2 for all dropdowns =====
function initSelect2() {
    // Add Risk Modal - Workspace
    if ($('#addWorkspaceSelect').length && !$('#addWorkspaceSelect').hasClass('select2-hidden-accessible')) {
        $('#addWorkspaceSelect').select2({
            dropdownParent: $('#addRiskModal'),
            placeholder: 'Select Workspace',
            allowClear: false
        }).on('change', function() {
            loadTasksForAdd($(this).val());
        });
    }
    
    // Add Risk Modal - Category
    if ($('#addCategory').length && !$('#addCategory').hasClass('select2-hidden-accessible')) {
        $('#addCategory').select2({
            dropdownParent: $('#addRiskModal'),
            placeholder: 'Select Category',
            allowClear: false,
            minimumResultsForSearch: Infinity
        });
    }
    
    // Add Risk Modal - Task
    if ($('#addTaskSelect').length && !$('#addTaskSelect').hasClass('select2-hidden-accessible')) {
        $('#addTaskSelect').select2({
            dropdownParent: $('#addRiskModal'),
            placeholder: 'Not related to specific task',
            allowClear: true
        });
    }
    
    // Edit Risk Modal - Workspace
    if ($('#editWorkspaceSelect').length && !$('#editWorkspaceSelect').hasClass('select2-hidden-accessible')) {
        $('#editWorkspaceSelect').select2({
            dropdownParent: $('#editRiskModal'),
            placeholder: 'Select Workspace',
            allowClear: false
        }).on('change', function() {
            loadTasksForEdit($(this).val());
        });
    }
    
    // Edit Risk Modal - Category
    if ($('#editCategory').length && !$('#editCategory').hasClass('select2-hidden-accessible')) {
        $('#editCategory').select2({
            dropdownParent: $('#editRiskModal'),
            placeholder: 'Select Category',
            allowClear: false,
            minimumResultsForSearch: Infinity
        });
    }
    
    // Edit Risk Modal - Task
    if ($('#editTaskSelect').length && !$('#editTaskSelect').hasClass('select2-hidden-accessible')) {
        $('#editTaskSelect').select2({
            dropdownParent: $('#editRiskModal'),
            placeholder: 'Not related to specific task',
            allowClear: true
        });
    }
    
    // Convert to Issue Modal - Assignee
    if ($('#convertAssigneeSelect').length && !$('#convertAssigneeSelect').hasClass('select2-hidden-accessible')) {
        $('#convertAssigneeSelect').select2({
            dropdownParent: $('#convertIssueModal'),
            placeholder: 'Unassigned',
            allowClear: true
        });
    }
}

// ===== Initialize Flatpickr for date fields =====
function initFlatpickr() {
    // Deadline in Convert Issue Modal
    const deadlineInput = document.querySelector('#convertIssueModal input[name="deadline"]');
    if (deadlineInput && !deadlineInput._flatpickr) {
        flatpickr(deadlineInput, {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            altInput: true,
            altFormat: 'F j, Y',
            locale: {
                firstDayOfWeek: 1
            }
        });
    }
}

// Initialize on page load
$(document).ready(function() {
    initSelect2();
    initFlatpickr();
});

// Re-initialize when modals are shown
$('#addRiskModal').on('shown.bs.modal', function() {
    initSelect2();
});

$('#editRiskModal').on('shown.bs.modal', function() {
    initSelect2();
});

$('#convertIssueModal').on('shown.bs.modal', function() {
    initSelect2();
    initFlatpickr();
});

// Destroy Select2 when modals are hidden to prevent issues
$('#addRiskModal, #editRiskModal, #convertIssueModal').on('hidden.bs.modal', function() {
    $(this).find('select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
});
</script>
@endpush
