@extends('layouts.app')

@section('title', 'Issue List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="ti ti-bug text-warning me-2"></i>All Issues
            </h4>
            <p class="text-muted mb-0">Manage issues from risks and direct reports</p>
        </div>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addIssueModal">
            <i class="ti ti-plus me-1"></i>Report Issue
        </button>
    </div>

    {{-- Workspace Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('risk.issues') }}" class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label mb-1 small">
                        <i class="ti ti-filter me-1"></i>Filter by Workspace
                    </label>
                    <select class="form-select form-select-sm" name="workspace_id" onchange="this.form.submit()">
                        <option value="">All Workspaces</option>
                        @foreach($workspaces as $ws)
                            <option value="{{ $ws->workspace_id }}" {{ request('workspace_id') == $ws->workspace_id ? 'selected' : '' }}>
                                {{ $ws->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-end mt-2 mt-md-0">
                    @if(request('workspace_id'))
                        <a href="{{ route('risk.issues') }}" class="btn btn-sm btn-label-secondary">
                            <i class="ti ti-x me-1"></i>Clear Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Issue List Table --}}
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Workspace</th>
                        <th>From Risk</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issues as $issue)
                    <tr class="{{ $issue->isOverdue() ? 'table-danger' : '' }}">
                        <td>
                            <strong class="text-warning">#{{ $issue->code }}</strong>
                        </td>
                        <td>
                            <div style="max-width: 250px;">
                                <strong>{{ Str::limit($issue->title, 60) }}</strong>
                                @if($issue->description)
                                    <br><small class="text-muted">{{ Str::limit($issue->description, 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">
                                {{ strtoupper(substr($issue->workspace->title, 0, 2)) }}
                            </span>
                            <br><small class="text-muted">{{ $issue->workspace->title }}</small>
                        </td>
                        <td>
                            @if($issue->isFromRisk() && $issue->risk && $issue->risk->workspace)
                                <span class="badge bg-label-primary">
                                    {{ strtoupper(substr($issue->risk->workspace->title, 0, 2)) }}
                                </span>
                                <strong class="text-primary">#{{ $issue->risk->code }}</strong>
                            @else
                                <span class="badge bg-label-secondary">Direct</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $issue->priority_badge }}">
                                Priority {{ $issue->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $issue->status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                            </span>
                        </td>
                        <td>
                            @if($issue->assignee)
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($issue->assignee->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <small>{{ $issue->assignee->user->name }}</small>
                                </div>
                            @else
                                <span class="badge bg-label-secondary">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            @if($issue->deadline)
                                <small class="{{ $issue->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                    <i class="ti ti-calendar me-1"></i>{{ $issue->deadline->format('d M Y') }}
                                    @if($issue->isOverdue())
                                        <br><span class="badge bg-danger">OVERDUE</span>
                                    @endif
                                </small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-label-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="viewIssueDetail('{{ $issue->issue_id }}')">
                                            <i class="ti ti-eye me-1"></i>View Details & Comments
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if($issue->canStartWorking())
                                    <li>
                                        <form action="{{ route('risk.issues.status', $issue->issue_id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="dropdown-item">
                                                <i class="ti ti-player-play me-1"></i>Start Working
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @if(!$issue->isResolved())
                                    <li>
                                        <a class="dropdown-item text-success" href="javascript:void(0)" onclick="openResolveModal('{{ $issue->issue_id }}', '{{ $issue->code }}')">
                                            <i class="ti ti-check me-1"></i>Resolve Issue
                                        </a>
                                    </li>
                                    @endif
                                    @if($issue->canCreateChangeRequest())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-info" href="javascript:void(0)" onclick="createCR('{{ $issue->issue_id }}', '{{ $issue->code }}')">
                                        <i class="ti ti-arrow-right-circle me-1"></i>Request Timeline Extension
                                    </a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('risk.issues.destroy', $issue->issue_id) }}" method="POST" onsubmit="return confirm('Delete this issue?')">
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
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="ti ti-bug-off ti-lg mb-2"></i>
                            <p class="mb-0">No issues found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

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
                <button type="button" id="btnResolveFromDetail" class="btn btn-success" style="display:none" onclick="openResolveModalFromDetail()">
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

@endsection

@push('scripts')
<script>
// Workspace tasks and members data
const workspaceTasks = @json($workspaces->mapWithKeys(function($ws) {
    return [$ws->workspace_id => $ws->kanbanTasks ?? []];
}));

const workspaceMembers = @json($workspaces->mapWithKeys(function($ws) {
    return [$ws->workspace_id => $ws->members ?? []];
}));

// Load tasks for Report Issue modal
function loadTasksForReport(workspaceId) {
    const select = $('#reportTaskSelect');
    select.empty().append('<option value="">No specific task</option>');
    
    if (workspaceId && workspaceTasks[workspaceId]) {
        workspaceTasks[workspaceId].forEach(task => {
            const option = new Option(task.title, task.task_id);
            select.append(option);
        });
    }
    
    // Trigger Select2 update
    select.trigger('change');
}

// Load members for Report Issue modal
function loadMembersForReport(workspaceId) {
    const select = $('#reportAssigneeSelect');
    select.empty().append('<option value="">Unassigned</option>');
    
    if (workspaceId && workspaceMembers[workspaceId]) {
        workspaceMembers[workspaceId].forEach(member => {
            const option = new Option(member.user.name + ' - ' + member.role, member.employee_id);
            select.append(option);
        });
    }
    
    // Trigger Select2 update
    select.trigger('change');
}

function createCR(issueId, issueCode) {
    document.getElementById('crIssueId').value = issueId;
    document.getElementById('crIssueCode').textContent = issueCode;
    var modal = new bootstrap.Modal(document.getElementById('createCRModal'));
    modal.show();
}

// Global variable for current issue ID
let currentIssueId = null;

// View Issue Detail
async function viewIssueDetail(issueId) {
    currentIssueId = issueId;
    const modal = new bootstrap.Modal(document.getElementById('viewIssueModal'));
    modal.show();
    
    // Reset content
    document.getElementById('issueDetailContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    try {
        const response = await fetch(`/risk/issues/${issueId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        // Debug: Log response details
        console.log('Response status:', response.status);
        console.log('Response content-type:', response.headers.get('content-type'));
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Response error:', text.substring(0, 500));
            throw new Error(`Failed to fetch issue details (${response.status})`);
        }
        
        const text = await response.text();
        console.log('Response text (first 200 chars):', text.substring(0, 200));
        
        const result = JSON.parse(text);
        const issue = result.data;
        
        // Update modal title
        document.getElementById('modalIssueCode').textContent = '#' + issue.code;
        document.getElementById('modalIssueTitle').textContent = issue.title;
        
        // Show/hide resolve button
        if (!issue.resolved_at) {
            document.getElementById('btnResolveFromDetail').style.display = 'inline-block';
        } else {
            document.getElementById('btnResolveFromDetail').style.display = 'none';
        }
        
        // Build detail content with Vuexy card-based layout
        let html = `
            <!-- Overview Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="ti ti-info-circle me-2"></i>Overview
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Workspace</small>
                            <span class="badge bg-label-primary">
                                <i class="ti ti-briefcase ti-xs me-1"></i>${issue.workspace.title}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Status</small>
                            <span class="${issue.status_badge}">
                                <i class="ti ti-point-filled ti-xs me-1"></i>${issue.status.replace('_', ' ').toUpperCase()}
                            </span>
                        </div>
                        ${issue.risk ? `
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">From Risk</small>
                                <div>
                                    <span class="badge bg-label-warning">
                                        <i class="ti ti-alert-triangle ti-xs me-1"></i>#${issue.risk.code}
                                    </span>
                                    <small class="ms-2">${issue.risk.description ? issue.risk.description.substring(0, 40) + '...' : ''}</small>
                                </div>
                            </div>
                        ` : `
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Source</small>
                                <span class="badge bg-label-info">
                                    <i class="ti ti-file-report ti-xs me-1"></i>Direct Report
                                </span>
                            </div>
                        `}
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Priority / Severity</small>
                            <span class="${issue.priority_badge}">
                                <i class="ti ti-alert-circle ti-xs me-1"></i>Priority ${issue.priority}/5
                            </span>
                            <span class="badge bg-label-secondary ms-2">Severity ${issue.severity}/5</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Assigned To</small>
                            <div>
                                ${issue.assignee ? `
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                ${issue.assignee.user.name.charAt(0)}
                                            </span>
                                        </div>
                                        <span>${issue.assignee.user.name}</span>
                                    </div>
                                ` : '<span class="badge bg-label-secondary"><i class="ti ti-user-off ti-xs me-1"></i>Unassigned</span>'}
                            </div>
                        </div>
                        ${issue.deadline ? `
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Deadline</small>
                                ${new Date(issue.deadline) < new Date() && !issue.resolved_at ? `
                                    <span class="badge bg-danger">
                                        <i class="ti ti-alert-triangle ti-xs me-1"></i>OVERDUE: ${new Date(issue.deadline).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                    </span>
                                ` : `
                                    <span class="badge bg-label-success">
                                        <i class="ti ti-calendar ti-xs me-1"></i>${new Date(issue.deadline).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                    </span>
                                `}
                            </div>
                        ` : ''}
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Reported By</small>
                            <small>${issue.creator ? issue.creator.name : 'Unknown'}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Reported On</small>
                            <small>${new Date(issue.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</small>
                        </div>
                    </div>
                </div>
            </div>
            
            ${issue.description ? `
                <!-- Description Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-file-description me-2"></i>Description
                        </h6>
                        <p class="mb-0">${issue.description}</p>
                    </div>
                </div>
            ` : ''}
            
            ${issue.linked_task ? `
                <!-- Related Task Card -->
                <div class="card mb-4 border-info">
                    <div class="card-body">
                        <h6 class="card-title mb-2">
                            <i class="ti ti-link me-2 text-info"></i>Related Kanban Task
                        </h6>
                        <div class="d-flex align-items-center">
                            <i class="ti ti-clipboard-check ti-md text-info me-3"></i>
                            <div>
                                <strong>${issue.linked_task.title}</strong>
                                <br><small class="text-muted">Task in ${issue.workspace.title}</small>
                            </div>
                        </div>
                    </div>
                </div>
            ` : ''}
            
            ${issue.attachments && issue.attachments.length > 0 ? `
                <!-- Attachments Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-paperclip me-2"></i>Attachments (${issue.attachments.length})
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            ${issue.attachments.map(att => `
                                <a href="/storage/${att.file_path}" target="_blank" class="text-decoration-none">
                                    ${att.file_path.match(/\.(jpg|jpeg|png|gif|webp)$/i) ? `
                                        <img src="/storage/${att.file_path}" class="img-thumbnail rounded" 
                                             style="width: 100px; height: 100px; object-fit: cover;" 
                                             alt="${att.file_name}">
                                    ` : `
                                        <div class="border rounded p-3 text-center" style="width: 100px;">
                                            <i class="ti ti-file ti-lg text-muted"></i>
                                            <br><small class="text-truncate d-block">${att.file_name.substring(0, 12)}</small>
                                        </div>
                                    `}
                                </a>
                            `).join('')}
                        </div>
                    </div>
                </div>
            ` : ''}
        `;
        
        // Comments section with Vuexy timeline styling
        if (issue.comments && issue.comments.length > 0) {
            html += `
                <!-- Comments & Activity Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-4">
                            <i class="ti ti-messages me-2"></i>Comments & Activity
                            <span class="badge bg-label-secondary ms-2">${issue.comments.length}</span>
                        </h6>
                        <ul class="timeline ms-2">
            `;
            
            issue.comments.forEach((comment, index) => {
                const isResolution = comment.is_resolution;
                const isLast = index === issue.comments.length - 1;
                html += `
                    <li class="timeline-item timeline-item-transparent ${isLast ? 'border-transparent' : ''} pb-4">
                        <span class="timeline-point ${isResolution ? 'timeline-point-success' : 'timeline-point-primary'}"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-2">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            ${comment.user.name.charAt(0).toUpperCase()}
                                        </span>
                                    </div>
                                    <h6 class="mb-0">${comment.user.name}</h6>
                                    ${isResolution ? '<span class="badge bg-success ms-2"><i class="ti ti-check-circle ti-xs me-1"></i>Resolution</span>' : ''}
                                </div>
                                <small class="text-muted">${new Date(comment.created_at).toLocaleString('en-US', { 
                                    month: 'short', 
                                    day: 'numeric', 
                                    year: 'numeric',
                                    hour: '2-digit', 
                                    minute: '2-digit' 
                                })}</small>
                            </div>
                            <p class="mb-2">${comment.comment}</p>
                            ${comment.attachments && comment.attachments.length > 0 ? `
                                <div class="d-flex gap-2 mt-3 flex-wrap">
                                    ${comment.attachments.map(att => `
                                        <a href="/storage/${att.file_path}" target="_blank" class="text-decoration-none">
                                            <img src="/storage/${att.file_path}" 
                                                 class="img-thumbnail rounded" 
                                                 style="width: 80px; height: 80px; object-fit: cover;" 
                                                 alt="attachment">
                                        </a>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </li>
                `;
            });
            
            html += `
                        </ul>
                    </div>
                </div>
            `;
        } else {
            html += `
                <!-- Comments & Activity Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-messages me-2"></i>Comments & Activity
                        </h6>
                        <div class="text-center py-5">
                            <i class="ti ti-message-off ti-xl text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">No comments or activity yet</p>
                            <small class="text-muted">Comments will appear here when added</small>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Change Requests section (if available)
        if (issue.change_requests && issue.change_requests.length > 0) {
            html += `
                <!-- Change Requests Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="ti ti-git-pull-request me-2"></i>Change Requests
                            <span class="badge bg-label-secondary ms-2">${issue.change_requests.length}</span>
                        </h6>
                        <div class="list-group list-group-flush">
                            ${issue.change_requests.map(cr => `
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-primary">#CR${cr.id.toString().padStart(3, '0')}</strong>
                                            <small class="text-muted ms-2">${cr.type === 'timeline' ? 'Timeline Extension' : 'Resource Change'}</small>
                                            ${cr.timeline_extension_days ? `<br><span class="badge bg-label-info mt-1">+${cr.timeline_extension_days} days</span>` : ''}
                                        </div>
                                        <span class="${cr.status === 'approved' ? 'badge bg-success' : cr.status === 'rejected' ? 'badge bg-danger' : 'badge bg-label-warning'}">
                                            ${cr.status.toUpperCase()}
                                        </span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Resolved info
        if (issue.resolved_at) {
            html += `
                <!-- Resolution Card -->
                <div class="card border-success mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded-circle bg-success">
                                    <i class="ti ti-check ti-lg"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">
                                    <i class="ti ti-check-circle me-1 text-success"></i>Issue Resolved
                                </h6>
                                <small class="text-muted">
                                    Resolved on ${new Date(issue.resolved_at).toLocaleDateString('en-US', { 
                                        month: 'long', 
                                        day: 'numeric', 
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })} by <strong>${issue.resolver ? issue.resolver.name : 'Unknown'}</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        document.getElementById('issueDetailContent').innerHTML = html;
        
    } catch (error) {
        console.error('Error loading issue details:', error);
        document.getElementById('issueDetailContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="ti ti-alert-circle me-2"></i>Failed to load issue details. Please try again.
            </div>
        `;
    }
}

// Open Resolve Modal
function openResolveModal(issueId, issueCode) {
    currentIssueId = issueId;
    document.getElementById('resolveIssueCode').textContent = '#' + issueCode;
    document.getElementById('resolveIssueForm').action = `/risk/issues/${issueId}/resolve`;
    
    // Reset form
    document.getElementById('resolveIssueForm').reset();
    
    const modal = new bootstrap.Modal(document.getElementById('resolveIssueModal'));
    modal.show();
}

// Open Resolve Modal from Detail View
function openResolveModalFromDetail() {
    // Close detail modal first
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('viewIssueModal'));
    if (detailModal) detailModal.hide();
    
    // Get issue code from detail modal
    const issueCode = document.getElementById('modalIssueCode').textContent.replace('#', '');
    
    // Open resolve modal
    setTimeout(() => {
        openResolveModal(currentIssueId, issueCode);
    }, 300);
}

// Handle resolve form submission
document.addEventListener('DOMContentLoaded', function() {
    const resolveForm = document.getElementById('resolveIssueForm');
    if (resolveForm) {
        resolveForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Resolving...';
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('resolveIssueModal'));
                    if (modal) modal.hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Issue resolved successfully',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(result.message || 'Failed to resolve issue');
                }
            } catch (error) {
                console.error('Error resolving issue:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to resolve issue'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
});

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000
    });
@endif

// ===== Initialize Select2 for all dropdowns =====
function initSelect2Issues() {
    // Add Issue Modal - Workspace (FIX: Correct modal ID is addIssueModal, not reportIssueModal)
    if ($('#reportWorkspaceSelect').length && !$('#reportWorkspaceSelect').hasClass('select2-hidden-accessible')) {
        $('#reportWorkspaceSelect').select2({
            dropdownParent: $('#addIssueModal'),
            placeholder: 'Select Workspace',
            allowClear: false
        }).on('change', function() {
            const workspaceId = $(this).val();
            loadTasksForReport(workspaceId);
            loadMembersForReport(workspaceId);
        });
    }
    
    // Add Issue Modal - Task
    if ($('#reportTaskSelect').length && !$('#reportTaskSelect').hasClass('select2-hidden-accessible')) {
        $('#reportTaskSelect').select2({
            dropdownParent: $('#addIssueModal'),
            placeholder: 'No specific task',
            allowClear: true
        });
    }
    
    // Add Issue Modal - Assignee
    if ($('#reportAssigneeSelect').length && !$('#reportAssigneeSelect').hasClass('select2-hidden-accessible')) {
        $('#reportAssigneeSelect').select2({
            dropdownParent: $('#addIssueModal'),
            placeholder: 'Unassigned',
            allowClear: true
        });
    }
}

// ===== Initialize Flatpickr for date fields =====
function initFlatpickrIssues() {
    // Deadline in Add Issue Modal (FIX: Correct modal ID)
    const deadlineInput = document.querySelector('#addIssueModal input[name="deadline"]');
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
    initSelect2Issues();
    initFlatpickrIssues();
});

// Re-initialize when modal is shown (FIX: Correct modal ID)
$('#addIssueModal').on('shown.bs.modal', function() {
    initSelect2Issues();
    initFlatpickrIssues();
});

// Destroy Select2 when modal is hidden (FIX: Correct modal ID)
$('#addIssueModal').on('hidden.bs.modal', function() {
    $(this).find('select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
});
</script>
@endpush
