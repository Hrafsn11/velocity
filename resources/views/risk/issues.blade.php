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
    <x-issue.table :issues="$issues" />

</div>

{{-- All Modals --}}
<x-issue.modals :workspaces="$workspaces" :employees="$employees" />

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

// ===== GLOBAL VARIABLES =====
let currentIssueId = null;

// ===== UTILITIES (Simplified) =====
function getStatusBadge(status) {
    const badges = {open: 'warning', in_progress: 'info', resolved: 'success', closed: 'secondary', reopened: 'danger'};
    return `<span class="badge bg-label-${badges[status] || 'secondary'}">${status.replace('_', ' ').toUpperCase()}</span>`;
}

function getPriorityBadge(priority) {
    const badges = {1: 'secondary', 2: 'success', 3: 'info', 4: 'warning', 5: 'danger'};
    return `<span class="badge bg-${badges[priority] || 'secondary'}">Priority ${priority}/5</span>`;
}

function formatDate(date) {
    return new Date(date).toLocaleString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'});
}

function showSuccessMessage() {
    @if(session('success'))
        Swal.fire({icon: 'success', title: 'Success!', text: '{{ session('success') }}', timer: 3000});
    @endif
}

// ===== RENDER HELPERS (Simplified) =====
function renderIssueOverview(issue) {
    return `
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="ti ti-info-circle me-2"></i>Overview</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Workspace</small>
                        <span class="badge bg-label-primary"><i class="ti ti-briefcase ti-xs me-1"></i>${issue.workspace.title}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Status</small>
                        ${getStatusBadge(issue.status)}
                    </div>
                    ${issue.risk ? `
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">From Risk</small>
                            <span class="badge bg-label-warning"><i class="ti ti-alert-triangle ti-xs me-1"></i>#${issue.risk.code}</span>
                        </div>
                    ` : `
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Source</small>
                            <span class="badge bg-label-info"><i class="ti ti-file-report ti-xs me-1"></i>Direct Report</span>
                        </div>
                    `}
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Priority</small>
                        ${getPriorityBadge(issue.priority)}
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Assigned To</small>
                        ${issue.assignee ? `
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">${issue.assignee.user.name.charAt(0)}</span>
                                </div>
                                <span>${issue.assignee.user.name}</span>
                            </div>
                        ` : '<span class="badge bg-label-secondary"><i class="ti ti-user-off ti-xs me-1"></i>Unassigned</span>'}
                    </div>
                    ${issue.deadline ? `
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Deadline</small>
                            ${new Date(issue.deadline) < new Date() && !issue.resolved_at ? 
                                `<span class="badge bg-danger"><i class="ti ti-alert-triangle ti-xs me-1"></i>OVERDUE: ${formatDate(issue.deadline)}</span>` :
                                `<span class="badge bg-label-success"><i class="ti ti-calendar ti-xs me-1"></i>${formatDate(issue.deadline)}</span>`
                            }
                        </div>
                    ` : ''}
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Reported By</small>
                        <small>${issue.creator?.name || 'Unknown'}</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Reported On</small>
                        <small>${formatDate(issue.created_at)}</small>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderComments(comments) {
    if (!comments?.length) {
        return `<div class="card mb-4"><div class="card-body text-center py-5">
            <i class="ti ti-message-off ti-xl text-muted mb-3 d-block"></i>
            <p class="text-muted mb-0">No comments yet</p>
        </div></div>`;
    }
    
    const timeline = comments.map((c, i) => `
        <li class="timeline-item timeline-item-transparent ${i === comments.length - 1 ? 'border-transparent' : ''} pb-4">
            <span class="timeline-point ${c.is_resolution ? 'timeline-point-success' : 'timeline-point-primary'}"></span>
            <div class="timeline-event">
                <div class="timeline-header mb-2">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar avatar-xs me-2">
                            <span class="avatar-initial rounded-circle bg-label-primary">${c.user.name.charAt(0)}</span>
                        </div>
                        <h6 class="mb-0">${c.user.name}</h6>
                        ${c.is_resolution ? '<span class="badge bg-success ms-2"><i class="ti ti-check-circle ti-xs me-1"></i>Resolution</span>' : ''}
                    </div>
                    <small class="text-muted">${formatDate(c.created_at)}</small>
                </div>
                <p class="mb-2">${c.comment}</p>
                ${c.attachments?.length ? `<div class="d-flex gap-2 mt-3 flex-wrap">
                    ${c.attachments.map(a => `<a href="/storage/${a.file_path}" target="_blank">
                        <img src="/storage/${a.file_path}" class="img-thumbnail rounded" style="width:80px;height:80px;object-fit:cover;" alt="attachment">
                    </a>`).join('')}
                </div>` : ''}
            </div>
        </li>
    `).join('');
    
    return `<div class="card mb-4"><div class="card-body">
        <h6 class="card-title mb-4"><i class="ti ti-messages me-2"></i>Comments & Activity 
            <span class="badge bg-label-secondary ms-2">${comments.length}</span>
        </h6>
        <ul class="timeline ms-2">${timeline}</ul>
    </div></div>`;
}

function renderAttachments(attachments) {
    if (!attachments?.length) return '';
    
    const items = attachments.map(a => {
        const isImage = a.file_path.match(/\.(jpg|jpeg|png|gif|webp)$/i);
        return `<a href="/storage/${a.file_path}" target="_blank" class="text-decoration-none">
            ${isImage ? `<img src="/storage/${a.file_path}" class="img-thumbnail rounded" style="width:100px;height:100px;object-fit:cover;" alt="${a.file_name}">` 
                : `<div class="border rounded p-3 text-center" style="width:100px;">
                    <i class="ti ti-file ti-lg text-muted"></i><br>
                    <small class="text-truncate d-block">${a.file_name.substring(0, 12)}</small>
                </div>`}
        </a>`;
    }).join('');
    
    return `<div class="card mb-4"><div class="card-body">
        <h6 class="card-title mb-3"><i class="ti ti-paperclip me-2"></i>Attachments (${attachments.length})</h6>
        <div class="d-flex flex-wrap gap-2">${items}</div>
    </div></div>`;
}

function renderChangeRequests(changeRequests) {
    if (!changeRequests?.length) return '';
    
    const items = changeRequests.map(cr => `
        <div class="list-group-item px-0">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong class="text-primary">#CR${String(cr.id).padStart(3, '0')}</strong>
                    <small class="text-muted ms-2">${cr.type === 'timeline' ? 'Timeline Extension' : 'Resource Change'}</small>
                    ${cr.timeline_extension_days ? `<br><span class="badge bg-label-info mt-1">+${cr.timeline_extension_days} days</span>` : ''}
                </div>
                <span class="badge ${cr.status === 'approved' ? 'bg-success' : cr.status === 'rejected' ? 'bg-danger' : 'bg-label-warning'}">${cr.status.toUpperCase()}</span>
            </div>
        </div>
    `).join('');
    
    return `<div class="card mb-4"><div class="card-body">
        <h6 class="card-title mb-3"><i class="ti ti-git-pull-request me-2"></i>Change Requests 
            <span class="badge bg-label-secondary ms-2">${changeRequests.length}</span>
        </h6>
        <div class="list-group list-group-flush">${items}</div>
    </div></div>`;
}

function renderResolutionCard(issue) {
    if (!issue.resolved_at) return '';
    
    return `<div class="card border-success mb-4"><div class="card-body">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-md me-3">
                <span class="avatar-initial rounded-circle bg-success"><i class="ti ti-check ti-lg"></i></span>
            </div>
            <div>
                <h6 class="mb-1"><i class="ti ti-check-circle me-1 text-success"></i>Issue Resolved</h6>
                <small class="text-muted">Resolved on ${formatDate(issue.resolved_at)} by <strong>${issue.resolver?.name || 'Unknown'}</strong></small>
            </div>
        </div>
    </div></div>`;
}

// ===== WORKSPACE HANDLER (Simplified) =====
function handleWorkspaceChange() {
    $('#reportWorkspaceSelect').on('change', function() {
        const wsId = $(this).val();
        loadTasksForReport(wsId);
        loadMembersForReport(wsId);
    });
}

function loadTasksForReport(workspaceId) {
    const $select = $('#reportTaskSelect');
    $select.empty().append('<option value="">No specific task</option>');
    
    if (workspaceId && workspaceTasks[workspaceId]) {
        workspaceTasks[workspaceId].forEach(task => {
            $select.append(new Option(task.title, task.task_id));
        });
    }
    $select.trigger('change');
}

function loadMembersForReport(workspaceId) {
    const $select = $('#reportAssigneeSelect');
    $select.empty().append('<option value="">Unassigned</option>');
    
    if (workspaceId && workspaceMembers[workspaceId]) {
        workspaceMembers[workspaceId].forEach(member => {
            $select.append(new Option(`${member.user.name} - ${member.role}`, member.employee_id));
        });
    }
    $select.trigger('change');
}

function createCR(issueId, issueCode) {
    document.getElementById('crIssueId').value = issueId;
    document.getElementById('crIssueCode').textContent = issueCode;
    var modal = new bootstrap.Modal(document.getElementById('createCRModal'));
    modal.show();
}

// ===== VIEW ISSUE DETAIL (Simplified) =====
async function viewIssueDetail(issueId) {
    currentIssueId = issueId;
    const modal = new bootstrap.Modal($('#viewIssueModal'));
    $('#issueDetailContent').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
    
    // Reset button state: hide by default using Bootstrap class
    $('#btnResolveFromDetail').addClass('d-none');
    
    modal.show();
    
    try {
        const res = await fetch(`/risk/issues/${issueId}`, {
            headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        if (!res.ok) throw new Error('Failed to load');
        
        const {data: issue} = await res.json();
        $('#modalIssueCode').text(`#${issue.code}`);
        $('#modalIssueTitle').text(issue.title);
        
        // Show resolve button ONLY if issue NOT yet resolved
        if (!issue.resolved_at) {
            $('#btnResolveFromDetail').removeClass('d-none');
        }
        
        // Build detail content using render functions
        const html = renderIssueOverview(issue) + 
                     (issue.description ? `<div class="card mb-4"><div class="card-body">
                         <h6 class="card-title mb-3"><i class="ti ti-file-description me-2"></i>Description</h6>
                         <p class="mb-0">${issue.description}</p>
                     </div></div>` : '') +
                     renderComments(issue.comments) +
                     renderAttachments(issue.attachments) +
                     renderChangeRequests(issue.change_requests) +
                     renderResolutionCard(issue);
        
        $('#issueDetailContent').html(html);
    } catch (error) {
        $('#issueDetailContent').html('<div class="alert alert-danger"><i class="ti ti-alert-circle me-2"></i>Failed to load issue details</div>');
    }
}

// ===== CREATE CHANGE REQUEST =====
function createCR(issueId, issueCode) {
    document.getElementById('crIssueId').value = issueId;
    document.getElementById('crIssueCode').textContent = issueCode;
    var modal = new bootstrap.Modal(document.getElementById('createCRModal'));
    modal.show();
}

// ===== WORKSPACE HANDLER =====
function handleWorkspaceChange() {
    $('#reportWorkspaceSelect').on('change', function() {
        const wsId = $(this).val();
        loadTasksForReport(wsId);
        loadMembersForReport(wsId);
    });
}

function loadTasksForReport(workspaceId) {
    const $select = $('#reportTaskSelect');
    $select.empty().append('<option value="">No specific task</option>');
    
    if (workspaceId && workspaceTasks[workspaceId]) {
        workspaceTasks[workspaceId].forEach(task => {
            $select.append(new Option(task.title, task.task_id));
        });
    }
    $select.trigger('change');
}

function loadMembersForReport(workspaceId) {
    const $select = $('#reportAssigneeSelect');
    $select.empty().append('<option value="">Unassigned</option>');
    
    if (workspaceId && workspaceMembers[workspaceId]) {
        workspaceMembers[workspaceId].forEach(member => {
            $select.append(new Option(`${member.user.name} - ${member.role}`, member.employee_id));
        });
    }
    $select.trigger('change');
}

// ===== RESOLVE MODAL (Simplified) =====
function openResolveModal(issueId, code) {
    currentIssueId = issueId;
    $('#resolveIssueCode').text(`#${code}`);
    $('#resolveIssueForm').attr('action', `/risk/issues/${issueId}/resolve`).trigger('reset');
    new bootstrap.Modal($('#resolveIssueModal')).show();
}

function openResolveModalFromDetail() {
    const detailModal = bootstrap.Modal.getInstance($('#viewIssueModal'));
    if (detailModal) detailModal.hide();
    
    const issueCode = $('#modalIssueCode').text().replace('#', '');
    setTimeout(() => openResolveModal(currentIssueId, issueCode), 300);
}

// ===== FORM SUBMIT (Simplified) =====
function handleFormSubmit() {
    $('#resolveIssueForm').on('submit', async function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Resolving...');
        
        try {
            const res = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                bootstrap.Modal.getInstance($('#resolveIssueModal')).hide();
                Swal.fire({icon: 'success', title: 'Success!', text: 'Issue resolved successfully', timer: 2000})
                    .then(() => location.reload());
            } else {
                throw new Error(data.message || 'Failed');
            }
        } catch (error) {
            Swal.fire({icon: 'error', title: 'Error!', text: error.message});
        } finally {
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

// ===== PLUGIN INITIALIZATION (Simplified) =====
function initPlugins() {
    // Select2 - All selects in modal
    $('#addIssueModal select').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                dropdownParent: $('#addIssueModal'),
                placeholder: $(this).attr('placeholder') || 'Select...',
                allowClear: true
            });
        }
    });
    
    // Flatpickr - Deadline field
    const deadlineInput = document.querySelector('#addIssueModal input[name="deadline"]');
    if (deadlineInput && !deadlineInput._flatpickr) {
        flatpickr(deadlineInput, {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            altInput: true,
            altFormat: 'F j, Y'
        });
    }
}

// Initialize on page load
$(document).ready(function() {
    initPlugins();
    handleWorkspaceChange();
    handleFormSubmit();
    showSuccessMessage();
});

// Re-init on modal shown
$('#addIssueModal').on('shown.bs.modal', initPlugins);

// Cleanup on modal hidden
$('#addIssueModal').on('hidden.bs.modal', function() {
    $(this).find('select.select2-hidden-accessible').select2('destroy');
});
</script>
@endpush

