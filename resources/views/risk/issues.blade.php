@extends('layouts.app')

@section('title', 'Issues')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/select2/select2.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="ti ti-bug text-warning me-2"></i>Issue Tracking
            </h4>
            <p class="text-muted mb-0">Track issues dari risks yang materialized</p>
        </div>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addIssueModal">
            <i class="ti ti-plus me-1"></i>Report Issue
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1">33</h5>
                            <small class="text-muted">Total Issues</small>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-bug ti-26px"></i>
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
                            <h5 class="fw-bold mb-1 text-danger">15</h5>
                            <small class="text-muted">Open</small>
                        </div>
                        <span class="badge bg-label-danger rounded-circle p-2">
                            <i class="ti ti-circle-dot ti-26px"></i>
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
                            <h5 class="fw-bold mb-1 text-warning">12</h5>
                            <small class="text-muted">In Progress</small>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-progress ti-26px"></i>
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
                            <h5 class="fw-bold mb-1 text-success">6</h5>
                            <small class="text-muted">Resolved</small>
                        </div>
                        <span class="badge bg-label-success rounded-circle p-2">
                            <i class="ti ti-circle-check ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Issues Table --}}
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">All Issues</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-issues table" id="issuesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Issue Title</th>
                        <th>From Risk</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="fw-semibold">#ISS001</span></td>
                        <td>
                            <div>
                                <span class="fw-medium">Login form validation error</span>
                                <br><small class="text-muted">Deadline: 2025-12-05</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary" style="font-size: 11px;">
                                <i class="ti ti-alert-triangle ti-xs me-1"></i>#R005
                            </span>
                        </td>
                        <td><span class="badge bg-danger rounded-pill">5</span></td>
                        <td><span class="badge bg-label-warning">In Progress</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                                </div>
                                <span class="small">John Doe</span>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit Issue</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-info request-cr-btn" href="#" 
                                           data-issue-id="ISS001" 
                                           data-issue-title="Login form validation error"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#createChangeRequestModal">
                                            <i class="ti ti-clock-edit me-2"></i>Request Timeline Extension
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-success" href="#"><i class="ti ti-check me-2"></i>Mark as Resolved</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="fw-semibold">#ISS002</span></td>
                        <td>
                            <div>
                                <span class="fw-medium">Payment gateway timeout issue</span>
                                <br><small class="text-muted">Deadline: 2025-12-07</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary" style="font-size: 11px;">
                                <i class="ti ti-alert-triangle ti-xs me-1"></i>#R008
                            </span>
                        </td>
                        <td><span class="badge bg-danger rounded-pill">5</span></td>
                        <td><span class="badge bg-label-danger">Open</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-success">JS</span>
                                </div>
                                <span class="small">Jane Smith</span>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit Issue</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-info request-cr-btn" href="#" 
                                           data-issue-id="ISS002" 
                                           data-issue-title="Payment gateway timeout issue"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#createChangeRequestModal">
                                            <i class="ti ti-clock-edit me-2"></i>Request Timeline Extension
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-success" href="#"><i class="ti ti-check me-2"></i>Mark as Resolved</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Create Change Request Modal --}}
<div class="modal fade" id="createChangeRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">
                    <i class="ti ti-clock-edit me-2"></i>Create Change Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Issue Impact:</strong> Issue ini memerlukan perubahan timeline atau resource workspace.
                </div>
                
                <form id="createChangeRequestForm">
                    <div class="card bg-label-warning mb-4">
                        <div class="card-body">
                            <h6 class="mb-2">Issue Details</h6>
                            <div class="mb-2"><strong>ID:</strong> <span id="modal-issue-id">#ISS001</span></div>
                            <div><strong>Title:</strong> <p id="modal-issue-title" class="mb-0 mt-1"></p></div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Change Request Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cr-title" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Change Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="cr-type" required>
                                <option value="">Select Type</option>
                                <option value="timeline">Timeline Extension</option>
                                <option value="resource">Resource Change</option>
                            </select>
                            <small class="text-muted">Pilih jenis perubahan yang diperlukan</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Workspace <span class="text-danger">*</span></label>
                            <select class="form-select" id="cr-workspace" required>
                                <option value="">Select Workspace</option>
                                <option value="ws-001">E-Commerce Development</option>
                                <option value="ws-002">Mobile App Project</option>
                                <option value="ws-003">CRM Integration</option>
                            </select>
                        </div>

                        {{-- Timeline Extension Section --}}
                        <div class="col-12 timeline-section" style="display: none;">
                            <div class="card bg-label-info">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i class="ti ti-clock me-2"></i>Timeline Extension
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Extension (days)</label>
                                            <input type="number" class="form-control" id="timeline-days" min="1" max="90" value="7">
                                            <small class="text-muted">Berapa hari perpanjangan diperlukan?</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Preview</label>
                                            <div class="alert alert-warning mb-0">
                                                <strong>+7 days</strong> (1 week)
                                                <div class="small mt-1">2025-12-15 → 2025-12-22</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Resource Change Section --}}
                        <div class="col-12 resource-section" style="display: none;">
                            <div class="card bg-label-success">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i class="ti ti-users me-2"></i>Resource Changes
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Members to Add</label>
                                            <select class="form-select select2" id="add-members" multiple>
                                                <option value="john">John Doe (Senior Developer)</option>
                                                <option value="jane">Jane Smith (UI/UX Designer)</option>
                                                <option value="mike">Mike Johnson (Frontend Dev)</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Members to Remove (optional)</label>
                                            <select class="form-select select2" id="remove-members" multiple>
                                                <option value="alice">Alice Brown (Backend Dev)</option>
                                                <option value="bob">Bob Wilson (QA Tester)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Justification <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="cr-justification" rows="4" required></textarea>
                            <small class="text-muted">Jelaskan mengapa perubahan ini diperlukan</small>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-label-info rounded">
                        <i class="ti ti-info-circle text-info me-2"></i>
                        <strong>What happens next?</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>CR akan di-review oleh PM</li>
                            <li>Jika approved, workspace akan auto-update</li>
                            <li>Timeline tasks atau members akan adjusted otomatis</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createChangeRequestForm" class="btn btn-info">
                    <i class="ti ti-send me-1"></i>Submit Request
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Add Issue Modal --}}
<div class="modal fade" id="addIssueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report New Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addIssueForm">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">Issue Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <select class="form-select">
                                <option value="5">5 - Critical</option>
                                <option value="4">4 - High</option>
                                <option value="3" selected>3 - Medium</option>
                                <option value="2">2 - Low</option>
                                <option value="1">1 - Very Low</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assign To</label>
                            <select class="form-select">
                                <option value="">Select Member</option>
                                <option value="john">John Doe</option>
                                <option value="jane">Jane Smith</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addIssueForm" class="btn btn-warning">Report Issue</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('libs/select2/select2.js') }}"></script>
<script>
$(document).ready(function() {
    $('#issuesTable').DataTable({order: [[3, 'desc']]});
    $('.select2').select2({dropdownParent: $('#createChangeRequestModal')});
    
    $('.request-cr-btn').on('click', function() {
        const issueId = $(this).data('issue-id');
        const issueTitle = $(this).data('issue-title');
        $('#modal-issue-id').text('#' + issueId);
        $('#modal-issue-title').text(issueTitle);
        $('#cr-title').val('Timeline extension for ' + issueTitle);
    });

    $('#cr-type').on('change', function() {
        const type = $(this).val();
        $('.timeline-section, .resource-section').hide();
        if (type === 'timeline') $('.timeline-section').show();
        if (type === 'resource') $('.resource-section').show();
    });

    $('#timeline-days').on('input', function() {
        const days = $(this).val();
        const weeks = Math.floor(days / 7);
        const weekText = weeks > 0 ? `(${weeks} week${weeks > 1 ? 's' : ''})` : '';
        $(this).closest('.resource-section, .timeline-section').find('.alert strong').text(`+${days} days ${weekText}`);
    });

    $('#createChangeRequestForm').on('submit', function(e) {
        e.preventDefault();
        alert('Change Request submitted! Awaiting approval...');
        window.location.href = '{{ route("risk.change-requests") }}';
    });
});
</script>
@endpush
