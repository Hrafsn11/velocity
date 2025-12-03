@extends('layouts.app')

@section('title', 'Change Requests')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="ti ti-arrow-right-circle text-info me-2"></i>Change Requests
            </h4>
            <p class="text-muted mb-0">Manage timeline & resource change requests</p>
        </div>
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createCRModal">
            <i class="ti ti-plus me-1"></i>Create Change Request
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1">16</h5>
                            <small class="text-muted">Total CR</small>
                        </div>
                        <span class="badge bg-label-info rounded-circle p-2">
                            <i class="ti ti-arrow-right-circle ti-26px"></i>
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
                            <h5 class="fw-bold mb-1 text-warning">6</h5>
                            <small class="text-muted">Pending Approval</small>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-clock ti-26px"></i>
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
                            <h5 class="fw-bold mb-1 text-success">8</h5>
                            <small class="text-muted">Approved</small>
                        </div>
                        <span class="badge bg-label-success rounded-circle p-2">
                            <i class="ti ti-circle-check ti-26px"></i>
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
                            <h5 class="fw-bold mb-1 text-danger">2</h5>
                            <small class="text-muted">Rejected</small>
                        </div>
                        <span class="badge bg-label-danger rounded-circle p-2">
                            <i class="ti ti-x ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Requests Table --}}
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">All Change Requests</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-cr table" id="crTable">
                <thead>
                    <tr>
                        <th>CR ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Impact</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- CR 1: Timeline Extension --}}
                    <tr>
                        <td><span class="fw-semibold">#CR001</span></td>
                        <td>
                            <div>
                                <span class="fw-medium">Extend timeline untuk modul authentication</span>
                                <br><small class="text-muted">Requested: 2025-11-28</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-info">Timeline</span></td>
                        <td>
                            <small class="d-block"><strong>Extension:</strong> +2 weeks (14 days)</small>
                            <small class="d-block"><strong>Workspace:</strong> E-Commerce Development</small>
                        </td>
                        <td><span class="badge bg-label-warning">Pending Approval</span></td>
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
                                    <li>
                                        <a class="dropdown-item view-cr-btn" href="#" 
                                           data-cr-id="CR001"
                                           data-cr-title="Extend timeline untuk modul authentication"
                                           data-cr-type="Timeline"
                                           data-cr-impact-ext="+2 weeks (14 days)"
                                           data-cr-impact-ws="E-Commerce Development"
                                           data-cr-status="Pending Approval"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#viewCRModal">
                                            <i class="ti ti-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-success" href="#"><i class="ti ti-check me-2"></i>Approve</a></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-x me-2"></i>Reject</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- CR 2: Resource Change --}}
                    <tr>
                        <td><span class="fw-semibold">#CR002</span></td>
                        <td>
                            <div>
                                <span class="fw-medium">Add developers untuk sprint terakhir</span>
                                <br><small class="text-muted">Requested: 2025-11-29</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-success">Resource</span></td>
                        <td>
                            <small class="d-block"><strong>Add:</strong> 2 members (John, Mike)</small>
                            <small class="d-block"><strong>Workspace:</strong> Mobile App Project</small>
                        </td>
                        <td><span class="badge bg-success">Approved</span></td>
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
                                    <li>
                                        <a class="dropdown-item view-cr-btn" href="#" 
                                           data-cr-id="CR002"
                                           data-cr-title="Add developers untuk sprint terakhir"
                                           data-cr-type="Resource"
                                           data-cr-impact-add="2 members (John, Mike)"
                                           data-cr-impact-ws="Mobile App Project"
                                           data-cr-status="Approved"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#viewCRModal">
                                            <i class="ti ti-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-history me-2"></i>View History</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    {{-- CR 3: Another Timeline --}}
                    <tr>
                        <td><span class="fw-semibold">#CR003</span></td>
                        <td>
                            <div>
                                <span class="fw-medium">Extend deadline karena dependency delay</span>
                                <br><small class="text-muted">Requested: 2025-12-01</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-info">Timeline</span></td>
                        <td>
                            <small class="d-block"><strong>Extension:</strong> +1 week (7 days)</small>
                            <small class="d-block"><strong>Workspace:</strong> CRM Integration</small>
                        </td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning">MJ</span>
                                </div>
                                <span class="small">Mike Johnson</span>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item view-cr-btn" href="#" 
                                           data-cr-id="CR003"
                                           data-cr-title="Extend deadline karena dependency delay"
                                           data-cr-type="Timeline"
                                           data-cr-impact-ext="+1 week (7 days)"
                                           data-cr-impact-ws="CRM Integration"
                                           data-cr-status="Approved"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#viewCRModal">
                                            <i class="ti ti-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-history me-2"></i>View History</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- View CR Modal --}}
<div class="modal fade" id="viewCRModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">
                    <i class="ti ti-file-text me-2"></i>Change Request Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    {{-- CR Header --}}
                    <div class="col-12">
                        <div class="card bg-label-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0" id="view-cr-title">Extend timeline untuk modul authentication</h5>
                                    <span class="badge bg-warning" id="view-cr-status">Pending Approval</span>
                                </div>
                                <div class="text-muted small">
                                    <span><strong>ID:</strong> <span id="view-cr-id">#CR001</span></span>
                                    <span class="mx-2">•</span>
                                    <span><strong>Type:</strong> <span id="view-cr-type-badge"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Impact Summary --}}
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3">Impact Analysis</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <div class="avatar avatar-md rounded-circle bg-label-warning mx-auto mb-3">
                                            <i class="ti ti-clock ti-lg"></i>
                                        </div>
                                        <h5 class="mb-1" id="view-impact-value">+2 weeks</h5>
                                        <p class="text-muted small mb-0" id="view-impact-label">Timeline Extension</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <div class="avatar avatar-md rounded-circle bg-label-secondary mx-auto mb-3">
                                            <i class="ti ti-briefcase ti-lg"></i>
                                        </div>
                                        <h5 class="mb-1" id="view-workspace">E-Commerce Dev</h5>
                                        <p class="text-muted small mb-0">Affected Workspace</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline Comparison (for Timeline CRs) --}}
                    <div class="col-12 timeline-comparison">
                        <h6 class="fw-semibold mb-3">Timeline Comparison</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">Current</th>
                                        <th class="text-center">After Approval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>End Date</strong></td>
                                        <td class="text-center">2025-12-15</td>
                                        <td class="text-center text-info"><strong>2025-12-29</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Days Remaining</strong></td>
                                        <td class="text-center">12 days</td>
                                        <td class="text-center text-info"><strong>26 days</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Justification --}}
                    <div class="col-12">
                        <h6 class="fw-semibold mb-2">Justification</h6>
                        <div class="alert alert-secondary">
                            <p class="mb-0">Modul authentication memerlukan additional testing untuk security vulnerabilities. Discovery phase menemukan potential security risks yang harus di-address sebelum production release.</p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12 approval-actions">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-success">
                                <i class="ti ti-check me-1"></i>Approve CR
                            </button>
                            <button class="btn btn-danger">
                                <i class="ti ti-x me-1"></i>Reject CR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Create CR Modal --}}
<div class="modal fade" id="createCRModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Change Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createCRForm">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">CR Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Change Type <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="timeline">Timeline Extension</option>
                                <option value="resource">Resource Change</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Workspace <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Select Workspace</option>
                                <option value="ws-001">E-Commerce Development</option>
                                <option value="ws-002">Mobile App Project</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Justification <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createCRForm" class="btn btn-info">Submit Request</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script>
$(document).ready(function() {
    $('#crTable').DataTable({order: [[0, 'desc']]});
    
    $('.view-cr-btn').on('click', function() {
        const crId = $(this).data('cr-id');
        const crTitle = $(this).data('cr-title');
        const crType = $(this).data('cr-type');
        const crStatus = $(this).data('cr-status');
        const impactExt = $(this).data('cr-impact-ext');
        const impactAdd = $(this).data('cr-impact-add');
        const impactWs = $(this).data('cr-impact-ws');
        
        $('#view-cr-id').text('#' + crId);
        $('#view-cr-title').text(crTitle);
        $('#view-cr-status').text(crStatus);
        
        if (crType === 'Timeline') {
            $('#view-cr-type-badge').html('<span class="badge bg-label-info">Timeline Extension</span>');
            $('#view-impact-value').text(impactExt);
            $('#view-impact-label').text('Timeline Extension');
            $('.timeline-comparison').show();
        } else {
            $('#view-cr-type-badge').html('<span class="badge bg-label-success">Resource Change</span>');
            $('#view-impact-value').text(impactAdd);
            $('#view-impact-label').text('Members to Add');
            $('.timeline-comparison').hide();
        }
        
        $('#view-workspace').text(impactWs);
        
        if (crStatus === 'Pending Approval') {
            $('.approval-actions').show();
        } else {
            $('.approval-actions').hide();
        }
    });
});
</script>
@endpush
