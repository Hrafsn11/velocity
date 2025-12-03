@extends('layouts.app')

@section('title', 'Risk List')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="ti ti-alert-triangle text-primary me-2"></i>Risk Management
            </h4>
            <p class="text-muted mb-0">Identifikasi dan kelola risiko proyek</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRiskModal">
            <i class="ti ti-plus me-1"></i>Add New Risk
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1">28</h5>
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
                            <h5 class="fw-bold mb-1 text-danger">8</h5>
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
                            <h5 class="fw-bold mb-1 text-warning">12</h5>
                            <small class="text-muted">High</small>
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
                            <h5 class="fw-bold mb-1 text-info">8</h5>
                            <small class="text-muted">Medium/Low</small>
                        </div>
                        <span class="badge bg-label-info rounded-circle p-2">
                            <i class="ti ti-info-circle ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Risk Table --}}
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">All Risks</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-risks table" id="risksTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Risk Description</th>
                        <th>Category</th>
                        <th>Score</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Risk 1 --}}
                    <tr>
                        <td><span class="fw-semibold">#R001</span></td>
                        <td>
                            <div>
                                <span class="fw-medium">Database server overload pada peak hours</span>
                                <br><small class="text-muted">Cause: Kurang optimasi query dan indexing</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-primary">Technical</span></td>
                        <td><span class="badge bg-danger rounded-pill">20</span></td>
                        <td><span class="badge bg-danger">Critical</span></td>
                        <td><span class="badge bg-label-success">Active</span></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item view-risk-btn" href="#" 
                                           data-risk-id="R001"
                                           data-risk-desc="Database server overload pada peak hours"
                                           data-risk-cause="Kurang optimasi query dan indexing"
                                           data-risk-mitigation="Implement query optimization, add database indexing, and setup load balancing. Monitor server performance with alerts for CPU/memory usage above 70%."
                                           data-bs-toggle="modal" 
                                           data-bs-target="#viewRiskModal">
                                            <i class="ti ti-eye me-2"></i>View Details & Mitigation
                                        </a>
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit Risk</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-primary convert-to-issue-btn" href="#" 
                                           data-risk-id="R001" 
                                           data-risk-desc="Database server overload pada peak hours"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#convertToIssueModal">
                                            <i class="ti ti-arrow-right-circle me-2"></i>Convert to Issue
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-check me-2"></i>Mark as Mitigated</a></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-x me-2"></i>Close Risk</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    {{-- Add more hardcoded risks --}}
                    <tr>
                        <td><span class="fw-semibold">#R002</span></td>
                        <td>
                            <div>
                                <span class="fw-medium">Key developer resign mendadak</span>
                                <br><small class="text-muted">Cause: Work-life balance issues</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-warning">SDM</span></td>
                        <td><span class="badge bg-warning rounded-pill">12</span></td>
                        <td><span class="badge bg-warning">High</span></td>
                        <td><span class="badge bg-label-info">Monitoring</span></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item view-risk-btn" href="#" 
                                           data-risk-id="R002"
                                           data-risk-desc="Key developer resign mendadak"
                                           data-risk-cause="Work-life balance issues"
                                           data-risk-mitigation="Prepare backup resources and cross-training plan. Create comprehensive knowledge transfer documentation. Improve team work-life balance and retention programs."
                                           data-bs-toggle="modal" 
                                           data-bs-target="#viewRiskModal">
                                            <i class="ti ti-eye me-2"></i>View Details & Mitigation
                                        </a>
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit Risk</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-primary convert-to-issue-btn" href="#" 
                                           data-risk-id="R002" 
                                           data-risk-desc="Key developer resign mendadak"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#convertToIssueModal">
                                            <i class="ti ti-arrow-right-circle me-2"></i>Convert to Issue
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-check me-2"></i>Mark as Mitigated</a></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-x me-2"></i>Close Risk</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- View Risk Details Modal --}}
<div class="modal fade" id="viewRiskModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="ti ti-alert-triangle me-2"></i>Risk Details & Mitigation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    {{-- Risk Info --}}
                    <div class="col-12">
                        <div class="card bg-label-primary">
                            <div class="card-body">
                                <h6 class="mb-2">Risk Information</h6>
                                <div class="mb-2"><strong>ID:</strong> <span id="view-risk-id">#R001</span></div>
                                <div class="mb-2"><strong>Description:</strong> <p id="view-risk-desc" class="mb-0 mt-1"></p></div>
                                <div><strong>Cause:</strong> <p id="view-risk-cause" class="mb-0 mt-1 text-muted"></p></div>
                            </div>
                        </div>
                    </div>

                    {{-- Mitigation Actions --}}
                    <div class="col-12">
                        <h6 class="fw-semibold">
                            <i class="ti ti-shield-check text-success me-2"></i>Mitigation Actions
                        </h6>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-success">
                            <p id="view-risk-mitigation" class="mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i>Edit Risk
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Convert to Issue Modal --}}
<div class="modal fade" id="convertToIssueModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="ti ti-arrow-right-circle me-2"></i>Convert Risk to Issue
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Risk Materialization:</strong> This risk has occurred and will be tracked as an issue.
                </div>
                
                <form id="convertToIssueForm">
                    <div class="card bg-label-secondary mb-4">
                        <div class="card-body">
                            <h6 class="mb-2">Original Risk</h6>
                            <div class="mb-2"><strong>ID:</strong> <span id="modal-risk-id">#R001</span></div>
                            <div><strong>Description:</strong> <p id="modal-risk-desc" class="mb-0 mt-1"></p></div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Issue Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="issue-title" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Issue Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="issue-description" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="issue-priority" required>
                                <option value="5">5 - Critical (Must fix now)</option>
                                <option value="4">4 - High (Fix ASAP)</option>
                                <option value="3" selected>3 - Medium (Fix soon)</option>
                                <option value="2">2 - Low (Can wait)</option>
                                <option value="1">1 - Very Low</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign PIC <span class="text-danger">*</span></label>
                            <select class="form-select" id="issue-assignee" required>
                                <option value="">Select Team Member</option>
                                <option value="john">John Doe (Senior Developer)</option>
                                <option value="jane">Jane Smith (UI/UX Designer)</option>
                                <option value="mike">Mike Johnson (Frontend Dev)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-label-success rounded">
                        <i class="ti ti-circle-check text-success me-2"></i>
                        <strong>Issue will be created as #ISS001</strong>
                        <div class="small mt-1 text-muted">Status: Open | From Risk: <span id="modal-risk-id-footer">#R001</span></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="convertToIssueForm" class="btn btn-primary">
                    <i class="ti ti-check me-1"></i>Create Issue
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Add Risk Modal --}}
<div class="modal fade" id="addRiskModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Risk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addRiskForm">
                    <div class="row g-4">
                        {{-- Risk Information Section --}}
                        <div class="col-12">
                            <h6 class="fw-semibold">Risk Information</h6>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Risk Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Cause / Root Issue</label>
                            <textarea class="form-control" rows="2" placeholder="What causes this risk?"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="technical">Technical</option>
                                <option value="sdm">SDM (Human Resource)</option>
                                <option value="financial">Financial</option>
                                <option value="timeline">Timeline</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Affected Module</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Probability (1-5)</label>
                            <input type="number" class="form-control" min="1" max="5" value="3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Impact (1-5)</label>
                            <input type="number" class="form-control" min="1" max="5" value="3">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Risk Score: <span id="calc-score">9</span></strong> | 
                                Urgency: <span class="badge bg-info" id="calc-urgency">Medium</span>
                            </div>
                        </div>

                        {{-- Mitigation Actions Section --}}
                        <div class="col-12 mt-4">
                            <h6 class="fw-semibold">
                                <i class="ti ti-shield-check text-success me-2"></i>Mitigation Actions
                            </h6>
                        </div>
                        <div class="col-12">
                            <label class="form-label">What actions will reduce this risk? <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="4" placeholder="Example: Implement query optimization, add database indexing, setup monitoring alerts..." required></textarea>
                            <small class="text-muted">Describe specific actions to prevent or minimize the risk impact</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addRiskForm" class="btn btn-primary">Add Risk</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script>
$(document).ready(function() {
    $('#risksTable').DataTable({order: [[3, 'desc']]});
    
    // View Risk Details & Mitigation
    $('.view-risk-btn').on('click', function() {
        const riskId = $(this).data('risk-id');
        const riskDesc = $(this).data('risk-desc');
        const riskCause = $(this).data('risk-cause');
        const riskMitigation = $(this).data('risk-mitigation');
        
        $('#view-risk-id').text('#' + riskId);
        $('#view-risk-desc').text(riskDesc);
        $('#view-risk-cause').text(riskCause);
        $('#view-risk-mitigation').text(riskMitigation);
    });
    
    // Convert to Issue
    $('.convert-to-issue-btn').on('click', function() {
        const riskId = $(this).data('risk-id');
        const riskDesc = $(this).data('risk-desc');
        $('#modal-risk-id, #modal-risk-id-footer').text('#' + riskId);
        $('#modal-risk-desc').text(riskDesc);
        $('#issue-title').val(riskDesc);
    });

    $('#convertToIssueForm').on('submit', function(e) {
        e.preventDefault();
        alert('Issue created! Redirecting to issues page...');
        window.location.href = '{{ route("risk.issues") }}';
    });
});
</script>
@endpush
