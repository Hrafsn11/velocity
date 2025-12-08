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
    <x-risk.table :risks="$risks" />

</div>

{{-- All Modals --}}
<x-risk.modals :workspaces="$workspaces" />

@endsection

@push('scripts')
<script>
// ===== DATA SOURCES =====
const workspaceTasks = @json($workspaces->mapWithKeys(function($ws) {
    return [$ws->workspace_id => $ws->kanbanTasks];
}));

// ===== UTILITIES (Simplified) =====
function getStatusBadge(status) {
    const badges = {active: 'danger', mitigating: 'warning', monitoring: 'primary', mitigated: 'success', materialized: 'danger', analyzing: 'info'};
    return `<span class="badge bg-label-${badges[status] || 'secondary'}">${status}</span>`;
}

function getUrgencyBadge(urgency) {
    const badges = {Critical: 'danger', High: 'warning', Medium: 'info', Low: 'success'};
    return `<span class="badge bg-${badges[urgency] || 'secondary'}">${urgency}</span>`;
}

function getCategoryBadge(category) {
    const badges = {Technical: 'primary', SDM: 'warning', Financial: 'success', Timeline: 'info'};
    return `<span class="badge bg-label-${badges[category] || 'secondary'}">${category}</span>`;
}

function formatDate(date) {
    return new Date(date).toLocaleString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'});
}

function showSuccessMessage() {
    @if(session('success'))
        Swal.fire({icon: 'success', title: 'Success!', text: '{{ session('success') }}', timer: 3000});
    @endif
}

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

// ===== VIEW RISK DETAILS (Best Practice: AJAX Load) =====
function viewRisk(risk) {
    // Populate modal dengan data dari parameter risk object
    $('#viewRiskCode').text(risk.code);
    $('#viewRiskWorkspace').text(risk.workspace.title);
    $('#viewRiskCategory').text(risk.category);
    $('#viewRiskDescription').text(risk.description);
    $('#viewRiskCause').text(risk.cause || 'Not specified');
    $('#viewRiskProbability').text(risk.probability);
    $('#viewRiskImpact').text(risk.impact);
    $('#viewRiskScore').text(risk.score);
    $('#viewRiskUrgency').text(risk.urgency);
    $('#viewRiskStatus').text(risk.status.toUpperCase());
    $('#viewRiskMitigation').text(risk.mitigation_actions || 'Not specified');
    $('#viewRiskCreatedAt').text(formatDate(risk.created_at));
    $('#viewRiskCreator').text(risk.creator ? risk.creator.name : 'Unknown');
    
    // Show/hide conditional sections
    $('#viewRiskCauseSection').toggle(!!risk.cause);
    $('#viewRiskTaskSection').toggle(!!risk.affected_task);
    $('#viewRiskMitigationSection').toggle(!!risk.mitigation_actions);
    
    if (risk.affected_task) {
        $('#viewRiskTaskTitle').text(risk.affected_task.title);
    }
    
    // Render related issues
    const issuesContainer = $('#viewRiskIssues');
    if (risk.issues?.length) {
        const issuesList = risk.issues.map(issue => `
            <div class="list-group-item px-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong class="text-primary">#${issue.code}</strong>
                        <span class="ms-2">${issue.title}</span>
                        ${issue.assignee ? `<br><small class="text-muted">Assigned to: ${issue.assignee.user.name}</small>` : ''}
                    </div>
                    <span class="badge ${issue.status === 'resolved' ? 'bg-success' : issue.status === 'in_progress' ? 'bg-info' : 'bg-secondary'}">
                        ${issue.status.replace('_', ' ').toUpperCase()}
                    </span>
                </div>
            </div>
        `).join('');
        issuesContainer.html(issuesList);
        $('#viewRiskIssuesEmpty').hide();
    } else {
        issuesContainer.empty();
        $('#viewRiskIssuesEmpty').show();
    }
    
    // Update badge colors based on values
    updateRiskBadges(risk);
    
    // Show modal
    new bootstrap.Modal(document.getElementById('viewRiskModal')).show();
}

// Helper: Update badge colors dynamically
function updateRiskBadges(risk) {
    const urgencyBadges = {Critical: 'bg-danger', High: 'bg-warning', Medium: 'bg-info', Low: 'bg-success'};
    const statusBadges = {mitigated: 'bg-success', mitigating: 'bg-warning', analyzing: 'bg-info', active: 'bg-danger'};
    const scoreBadge = risk.score >= 20 ? 'bg-danger' : risk.score >= 15 ? 'bg-warning' : 'bg-info';
    
    $('#viewRiskUrgency').removeClass('bg-danger bg-warning bg-info bg-success').addClass(urgencyBadges[risk.urgency] || 'bg-secondary');
    $('#viewRiskStatus').removeClass('bg-success bg-warning bg-info bg-danger').addClass(statusBadges[risk.status] || 'bg-label-secondary');
    $('#viewRiskScore').removeClass('bg-danger bg-warning bg-info').addClass(scoreBadge);
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
