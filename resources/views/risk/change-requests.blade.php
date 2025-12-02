@extends('layouts.app')

@section('title', 'Change Request Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/flatpickr/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/quill/quill.snow.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Change Request Management</h4>
            <p class="text-muted mb-0">Manage scope, timeline, and major project changes</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createChangeRequestModal">
            <i class="ti ti-plus me-1"></i>Create Change Request
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="mb-1">18</h5>
                            <small class="text-muted">Total Requests</small>
                        </div>
                        <span class="badge bg-label-primary rounded-circle p-2">
                            <i class="ti ti-arrow-right-circle ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="mb-1 text-warning">6</h5>
                            <small class="text-muted">Pending Approval</small>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-clock ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="mb-1 text-success">10</h5>
                            <small class="text-muted">Approved</small>
                        </div>
                        <span class="badge bg-label-success rounded-circle p-2">
                            <i class="ti ti-circle-check ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="mb-1 text-danger">2</h5>
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

    <!-- Change Requests Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Change Requests</h5>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="status-filter" id="all-status" autocomplete="off" checked>
                <label class="btn btn-outline-primary btn-sm" for="all-status">All</label>

                <input type="radio" class="btn-check" name="status-filter" id="pending-status" autocomplete="off">
                <label class="btn btn-outline-warning btn-sm" for="pending-status">Pending</label>

                <input type="radio" class="btn-check" name="status-filter" id="approved-status" autocomplete="off">
                <label class="btn btn-outline-success btn-sm" for="approved-status">Approved</label>

                <input type="radio" class="btn-check" name="status-filter" id="rejected-status" autocomplete="off">
                <label class="btn btn-outline-danger btn-sm" for="rejected-status">Rejected</label>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-change-requests table border-top" id="changeRequestsTable">
                <thead>
                    <tr>
                        <th>CR ID</th>
                        <th>Request Title</th>
                        <th>Type</th>
                        <th>Requester</th>
                        <th>Impact</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- CR 1 -->
                    <tr>
                        <td><span class="fw-medium">#CR001</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal">Extend timeline untuk modul authentication</a>
                                <small class="text-muted">Due to security enhancement requirements</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-info">Timeline</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                                </div>
                                <span>John Doe (PM)</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <small class="text-muted d-block">Timeline: +2 weeks</small>
                                <small class="text-muted d-block">Budget: +$5,000</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-warning">Pending Approval</span></td>
                        <td>Nov 28, 2025</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="approveRequest('#CR001')"><i class="ti ti-check me-2"></i>Approve</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="rejectRequest('#CR001')"><i class="ti ti-x me-2"></i>Reject</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- CR 2 -->
                    <tr>
                        <td><span class="fw-medium">#CR002</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal">Add new reporting dashboard feature</a>
                                <small class="text-muted">Client request for real-time analytics</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-primary">Scope</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-success">JS</span>
                                </div>
                                <span>Jane Smith (PM)</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <small class="text-muted d-block">Timeline: +3 weeks</small>
                                <small class="text-muted d-block">Budget: +$12,000</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-success">Approved</span></td>
                        <td>Nov 20, 2025</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Download Approval</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- CR 3 -->
                    <tr>
                        <td><span class="fw-medium">#CR003</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal">Change database dari MySQL ke PostgreSQL</a>
                                <small class="text-muted">Performance and scalability concerns</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-danger">Technical</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-info">MJ</span>
                                </div>
                                <span>Mike Johnson (Tech Lead)</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <small class="text-muted d-block">Timeline: +4 weeks</small>
                                <small class="text-muted d-block">Budget: +$20,000</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-danger">Rejected</span></td>
                        <td>Nov 15, 2025</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Revise & Resubmit</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- CR 4 -->
                    <tr>
                        <td><span class="fw-medium">#CR004</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal">Budget increase untuk additional resources</a>
                                <small class="text-muted">Team expansion needed for sprint 3</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-success">Budget</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning">SW</span>
                                </div>
                                <span>Sarah Williams (PM)</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <small class="text-muted d-block">Timeline: No change</small>
                                <small class="text-muted d-block">Budget: +$15,000</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-warning">Pending Approval</span></td>
                        <td>Nov 30, 2025</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="approveRequest('#CR004')"><i class="ti ti-check me-2"></i>Approve</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="rejectRequest('#CR004')"><i class="ti ti-x me-2"></i>Reject</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- CR 5 -->
                    <tr>
                        <td><span class="fw-medium">#CR005</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal">Remove payment gateway integration</a>
                                <small class="text-muted">Client decided to use manual payment process</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-primary">Scope</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                                </div>
                                <span>John Doe (PM)</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <small class="text-success d-block">Timeline: -1 week</small>
                                <small class="text-success d-block">Budget: -$8,000</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-success">Approved</span></td>
                        <td>Nov 25, 2025</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewChangeRequestModal"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Download Approval</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Change Request Modal -->
<div class="modal fade" id="createChangeRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Change Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createChangeRequestForm">
                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="fw-semibold">Basic Information</h6>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="crTitle">Change Request Title</label>
                            <input type="text" class="form-control" id="crTitle" placeholder="Brief title describing the change" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="crType">Change Type</label>
                            <select class="form-select" id="crType" required>
                                <option value="">Select Type</option>
                                <option value="scope">Scope Change</option>
                                <option value="timeline">Timeline Change</option>
                                <option value="budget">Budget Change</option>
                                <option value="technical">Technical Change</option>
                                <option value="resource">Resource Change</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="crDescription">Detailed Description</label>
                            <div id="crDescription" style="height: 200px;"></div>
                        </div>

                        <!-- Reason & Justification -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-semibold">Reason & Justification</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="crReason">Reason for Change</label>
                            <select class="form-select" id="crReason" required>
                                <option value="">Select Reason</option>
                                <option value="risk">Due to Risk Materialization</option>
                                <option value="client">Client Request</option>
                                <option value="technical">Technical Constraint</option>
                                <option value="resource">Resource Availability</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="relatedRisk">Related Risk (if applicable)</label>
                            <select class="form-select select2" id="relatedRisk">
                                <option value="">Not related to any risk</option>
                                <option value="R001">#R001 - Database server overload</option>
                                <option value="R002">#R002 - Key developer resign</option>
                                <option value="R003">#R003 - Budget overrun</option>
                                <option value="R004">#R004 - API integration delay</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="crJustification">Business Justification</label>
                            <textarea class="form-control" id="crJustification" rows="3" placeholder="Why is this change necessary?" required></textarea>
                        </div>

                        <!-- Impact Analysis -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-semibold">Impact Analysis</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="timelineImpact">Timeline Impact</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="timelineImpact" placeholder="0">
                                <select class="form-select" id="timelineUnit" style="max-width: 100px;">
                                    <option value="days">Days</option>
                                    <option value="weeks">Weeks</option>
                                    <option value="months">Months</option>
                                </select>
                            </div>
                            <small class="text-muted">Use negative for reduction</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="budgetImpact">Budget Impact (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="budgetImpact" placeholder="0">
                            </div>
                            <small class="text-muted">Use negative for savings</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="resourceImpact">Resource Impact</label>
                            <input type="text" class="form-control" id="resourceImpact" placeholder="e.g., +2 developers">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="affectedModules">Affected Modules/Features</label>
                            <select class="form-select select2" id="affectedModules" multiple required>
                                <option value="auth">Authentication Module</option>
                                <option value="dashboard">Dashboard Module</option>
                                <option value="reporting">Reporting Module</option>
                                <option value="integration">Integration Module</option>
                                <option value="payment">Payment Module</option>
                                <option value="notification">Notification Module</option>
                            </select>
                        </div>

                        <!-- Approval Workflow -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-semibold">Approval Workflow</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="approver1">Primary Approver</label>
                            <select class="form-select select2" id="approver1" required>
                                <option value="">Select Approver</option>
                                <option value="cahyo">Pak Cahyo (Tech Director)</option>
                                <option value="budi">Pak Budi (Project Manager)</option>
                                <option value="siti">Bu Siti (Finance Manager)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="approver2">Secondary Approver (Optional)</label>
                            <select class="form-select select2" id="approver2">
                                <option value="">Select Approver</option>
                                <option value="cahyo">Pak Cahyo (Tech Director)</option>
                                <option value="budi">Pak Budi (Project Manager)</option>
                                <option value="siti">Bu Siti (Finance Manager)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="crPriority">Request Priority</label>
                            <select class="form-select" id="crPriority" required>
                                <option value="low">Low - Can wait for next sprint</option>
                                <option value="medium" selected>Medium - Should be reviewed soon</option>
                                <option value="high">High - Needs urgent review</option>
                                <option value="critical">Critical - Blocks progress</option>
                            </select>
                        </div>

                        <!-- Attachments -->
                        <div class="col-12 mt-4">
                            <label class="form-label" for="crAttachments">Supporting Documents</label>
                            <input type="file" class="form-control" id="crAttachments" multiple>
                            <small class="text-muted">Upload impact analysis, client emails, or other supporting documents</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createChangeRequestForm" class="btn btn-primary">Submit for Approval</button>
            </div>
        </div>
    </div>
</div>

<!-- View Change Request Modal -->
<div class="modal fade" id="viewChangeRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">Change Request #CR001</h5>
                    <span class="badge bg-label-warning">Pending Approval</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Basic Info -->
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3">Basic Information</h6>
                        <h5 class="mb-2">Extend timeline untuk modul authentication</h5>
                        <p class="text-muted">Security enhancement requirements discovered during penetration testing require additional development time to implement 2FA and advanced session management.</p>
                    </div>

                    <!-- Details Grid -->
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Change Type</small>
                        <span class="badge bg-label-info">Timeline Change</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Priority</small>
                        <span class="badge bg-label-danger">High</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Requested By</small>
                        <div class="d-flex align-items-center mt-1">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                            </div>
                            <span>John Doe (PM)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Requested Date</small>
                        <span>November 28, 2025</span>
                    </div>

                    <!-- Impact Analysis -->
                    <div class="col-12 mt-4">
                        <h6 class="fw-semibold mb-3">Impact Analysis</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-label-warning">
                            <div class="card-body text-center">
                                <i class="ti ti-clock ti-lg mb-2"></i>
                                <h6 class="mb-0">+2 Weeks</h6>
                                <small class="text-muted">Timeline Impact</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-label-danger">
                            <div class="card-body text-center">
                                <i class="ti ti-currency-dollar ti-lg mb-2"></i>
                                <h6 class="mb-0">+$5,000</h6>
                                <small class="text-muted">Budget Impact</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-label-info">
                            <div class="card-body text-center">
                                <i class="ti ti-users ti-lg mb-2"></i>
                                <h6 class="mb-0">+1 Dev</h6>
                                <small class="text-muted">Resource Impact</small>
                            </div>
                        </div>
                    </div>

                    <!-- Reason & Justification -->
                    <div class="col-12 mt-3">
                        <h6 class="fw-semibold mb-2">Reason & Justification</h6>
                        <div class="alert alert-info mb-2">
                            <strong>Reason:</strong> Due to Risk Materialization (Risk #R005)
                        </div>
                        <p class="text-muted mb-0">Security audit revealed critical vulnerabilities in the authentication flow. Implementing proper security measures is essential before launch to protect user data and maintain compliance with security standards.</p>
                    </div>

                    <!-- Affected Modules -->
                    <div class="col-12 mt-3">
                        <small class="text-muted d-block mb-2">Affected Modules</small>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-label-primary">Authentication Module</span>
                            <span class="badge bg-label-primary">User Management</span>
                            <span class="badge bg-label-primary">Session Management</span>
                        </div>
                    </div>

                    <!-- Approval Workflow -->
                    <div class="col-12 mt-4">
                        <h6 class="fw-semibold mb-3">Approval Workflow</h6>
                        <div class="timeline">
                            <div class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Request Submitted</h6>
                                        <small class="text-muted">Nov 28, 2025 - 09:30 AM</small>
                                    </div>
                                    <p class="mb-0">John Doe submitted the change request</p>
                                </div>
                            </div>
                            <div class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-warning"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Pending Review</h6>
                                        <small class="text-muted">Waiting for approval</small>
                                    </div>
                                    <p class="mb-0">
                                        <strong>Primary Approver:</strong> Pak Cahyo (Tech Director)<br>
                                        <strong>Secondary Approver:</strong> Bu Siti (Finance Manager)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Actions (for approvers only) -->
                    <div class="col-12 mt-4">
                        <div class="card bg-label-secondary">
                            <div class="card-body">
                                <h6 class="mb-3">Approver Actions</h6>
                                <div class="mb-3">
                                    <label class="form-label" for="approvalComments">Comments</label>
                                    <textarea class="form-control" id="approvalComments" rows="3" placeholder="Add your comments here..."></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" onclick="approveRequest('#CR001')">
                                        <i class="ti ti-check me-1"></i>Approve Change Request
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="rejectRequest('#CR001')">
                                        <i class="ti ti-x me-1"></i>Reject Change Request
                                    </button>
                                    <button type="button" class="btn btn-warning">
                                        <i class="ti ti-message-circle me-1"></i>Request Clarification
                                    </button>
                                </div>
                            </div>
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

@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('libs/select2/select2.js') }}"></script>
    <script src="{{ asset('libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('libs/quill/quill.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#changeRequestsTable').DataTable({
                order: [[6, 'desc']], // Sort by date
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search change requests...'
                }
            });

            // Initialize Select2
            $('.select2').select2({
                dropdownParent: function() {
                    return $(this).closest('.modal').length ? $(this).closest('.modal') : $('body');
                }
            });

            // Initialize Quill Editor
            const quill = new Quill('#crDescription', {
                theme: 'snow',
                placeholder: 'Provide detailed description of the change...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link']
                    ]
                }
            });

            // Status Filter
            $('input[name="status-filter"]').on('change', function() {
                const status = $(this).attr('id').replace('-status', '');
                if (status === 'all') {
                    table.search('').draw();
                } else {
                    table.search(status).draw();
                }
            });

            // Form submission
            $('#createChangeRequestForm').on('submit', function(e) {
                e.preventDefault();
                alert('Change Request submitted successfully! Waiting for approval.');
                $('#createChangeRequestModal').modal('hide');
                this.reset();
                quill.setContents([]);
            });
        });

        // Approve Request
        function approveRequest(crId) {
            if (confirm('Are you sure you want to approve this change request? Timeline and budget will be automatically updated.')) {
                alert(crId + ' has been approved! Timeline and workload will be adjusted automatically.');
                location.reload();
            }
        }

        // Reject Request
        function rejectRequest(crId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason) {
                alert(crId + ' has been rejected. Requester will be notified with your feedback.');
                location.reload();
            }
        }
    </script>
@endpush
