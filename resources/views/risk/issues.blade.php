@extends('layouts.app')

@section('title', 'Issue Tracker')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/flatpickr/flatpickr.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Issue Tracker</h4>
            <p class="text-muted mb-0">Track and manage project issues</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIssueModal">
            <i class="ti ti-plus me-1"></i>Report New Issue
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="mb-1">42</h5>
                            <small class="text-muted">Total Issues</small>
                        </div>
                        <span class="badge bg-label-primary rounded-circle p-2">
                            <i class="ti ti-bug ti-26px"></i>
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
                            <h5 class="mb-1 text-warning">15</h5>
                            <small class="text-muted">Open</small>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-circle-dot ti-26px"></i>
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
                            <h5 class="mb-1 text-info">18</h5>
                            <small class="text-muted">In Progress</small>
                        </div>
                        <span class="badge bg-label-info rounded-circle p-2">
                            <i class="ti ti-refresh ti-26px"></i>
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
                            <h5 class="mb-1 text-success">9</h5>
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

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="in-progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select class="form-select" id="filterPriority">
                        <option value="">All Priorities</option>
                        <option value="5">Critical (5)</option>
                        <option value="4">High (4)</option>
                        <option value="3">Medium (3)</option>
                        <option value="2">Low (2)</option>
                        <option value="1">Very Low (1)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Severity</label>
                    <select class="form-select" id="filterSeverity">
                        <option value="">All Severities</option>
                        <option value="5">Critical (5)</option>
                        <option value="4">High (4)</option>
                        <option value="3">Medium (3)</option>
                        <option value="2">Low (2)</option>
                        <option value="1">Very Low (1)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Assigned To</label>
                    <select class="form-select select2" id="filterAssignee">
                        <option value="">All Assignees</option>
                        <option value="john">John Doe</option>
                        <option value="jane">Jane Smith</option>
                        <option value="mike">Mike Johnson</option>
                        <option value="sarah">Sarah Williams</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">All Issues</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-issues table border-top" id="issuesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Issue Title</th>
                        <th>Related To</th>
                        <th>PIC</th>
                        <th>Priority</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Issue 1 -->
                    <tr>
                        <td><span class="fw-medium">#ISS001</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium text-truncate" data-bs-toggle="modal" data-bs-target="#issueDetailModal">Login form validation error</a>
                                <small class="text-muted">From Risk: #R005 - Authentication vulnerability</small>
                            </div>
                        </td>
                        <td>
                            <a href="#" class="badge bg-label-primary">Task #T142</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                                </div>
                                <span>John Doe</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">5</span>
                                <span class="text-danger">Critical</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">5</span>
                                <span class="text-danger">Critical</span>
                            </div>
                        </td>
                        <td><span class="badge bg-label-info">In Progress</span></td>
                        <td><span class="text-nowrap">Dec 5, 2025</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#issueDetailModal">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Issue 2 -->
                    <tr>
                        <td><span class="fw-medium">#ISS002</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium text-truncate" data-bs-toggle="modal" data-bs-target="#issueDetailModal">Database connection timeout</a>
                                <small class="text-muted">From Risk: #R001 - DB overload</small>
                            </div>
                        </td>
                        <td>
                            <a href="#" class="badge bg-label-primary">Task #T089</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-success">JS</span>
                                </div>
                                <span>Jane Smith</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">4</span>
                                <span class="text-warning">High</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">5</span>
                                <span class="text-danger">Critical</span>
                            </div>
                        </td>
                        <td><span class="badge bg-label-warning">Open</span></td>
                        <td><span class="text-nowrap">Dec 8, 2025</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Issue 3 -->
                    <tr>
                        <td><span class="fw-medium">#ISS003</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium text-truncate" data-bs-toggle="modal" data-bs-target="#issueDetailModal">Missing documentation for API endpoints</a>
                                <small class="text-muted">From Risk: #R004 - API integration delay</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary">New Task</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-info">MJ</span>
                                </div>
                                <span>Mike Johnson</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">3</span>
                                <span class="text-info">Medium</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">4</span>
                                <span class="text-warning">High</span>
                            </div>
                        </td>
                        <td><span class="badge bg-label-success">Resolved</span></td>
                        <td><span class="text-nowrap">Dec 3, 2025</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Issue 4 -->
                    <tr>
                        <td><span class="fw-medium">#ISS004</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium text-truncate" data-bs-toggle="modal" data-bs-target="#issueDetailModal">UI/UX feedback implementation</a>
                                <small class="text-muted">Standalone issue</small>
                            </div>
                        </td>
                        <td>
                            <a href="#" class="badge bg-label-primary">Task #T201</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning">SW</span>
                                </div>
                                <span>Sarah Williams</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">2</span>
                                <span class="text-secondary">Low</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">3</span>
                                <span class="text-info">Medium</span>
                            </div>
                        </td>
                        <td><span class="badge bg-label-info">In Progress</span></td>
                        <td><span class="text-nowrap">Dec 10, 2025</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Issue 5 -->
                    <tr>
                        <td><span class="fw-medium">#ISS005</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-heading fw-medium text-truncate" data-bs-toggle="modal" data-bs-target="#issueDetailModal">Performance bottleneck in report generation</a>
                                <small class="text-muted">From Risk: #R001 - DB overload</small>
                            </div>
                        </td>
                        <td>
                            <a href="#" class="badge bg-label-primary">Task #T156</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                                </div>
                                <span>John Doe</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">4</span>
                                <span class="text-warning">High</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">4</span>
                                <span class="text-warning">High</span>
                            </div>
                        </td>
                        <td><span class="badge bg-label-warning">Open</span></td>
                        <td><span class="text-nowrap text-danger">Dec 4, 2025</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add Issue Modal -->
<div class="modal fade" id="addIssueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report New Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addIssueForm">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" for="issueTitle">Issue Title</label>
                            <input type="text" class="form-control" id="issueTitle" placeholder="Brief title describing the issue" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="issueDescription">Issue Description</label>
                            <textarea class="form-control" id="issueDescription" rows="4" placeholder="Detailed description of the issue..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="relatedRisk">Related Risk (Optional)</label>
                            <select class="form-select select2" id="relatedRisk">
                                <option value="">Not related to any risk</option>
                                <option value="R001">#R001 - Database server overload</option>
                                <option value="R002">#R002 - Key developer resign</option>
                                <option value="R003">#R003 - Budget overrun</option>
                                <option value="R004">#R004 - API integration delay</option>
                                <option value="R005">#R005 - Security vulnerability</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="relatedTask">Related Task</label>
                            <select class="form-select select2" id="relatedTask" required>
                                <option value="">Select existing task</option>
                                <option value="new">Create New Task</option>
                                <option value="T089">Task #T089 - Database Optimization</option>
                                <option value="T142">Task #T142 - Auth Module Security</option>
                                <option value="T156">Task #T156 - Report Generation</option>
                                <option value="T201">Task #T201 - UI Improvements</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="issueAssignee">Assign To (PIC)</label>
                            <select class="form-select select2" id="issueAssignee" required>
                                <option value="">Select assignee</option>
                                <option value="john">John Doe</option>
                                <option value="jane">Jane Smith</option>
                                <option value="mike">Mike Johnson</option>
                                <option value="sarah">Sarah Williams</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="issueDeadline">Deadline</label>
                            <input type="text" class="form-control flatpickr-input" id="issueDeadline" placeholder="Select deadline" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="issuePriority">Priority (1-5)</label>
                            <select class="form-select" id="issuePriority" required>
                                <option value="5">5 - Critical</option>
                                <option value="4">4 - High</option>
                                <option value="3" selected>3 - Medium</option>
                                <option value="2">2 - Low</option>
                                <option value="1">1 - Very Low</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="issueSeverity">Severity (1-5)</label>
                            <select class="form-select" id="issueSeverity" required>
                                <option value="5">5 - Critical</option>
                                <option value="4">4 - High</option>
                                <option value="3" selected>3 - Medium</option>
                                <option value="2">2 - Low</option>
                                <option value="1">1 - Very Low</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="issueStatus">Status</label>
                            <select class="form-select" id="issueStatus" required>
                                <option value="open" selected>Open</option>
                                <option value="in-progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="issueProof">Resolution Proof (Screenshot/Document)</label>
                            <input type="file" class="form-control" id="issueProof" accept="image/*,.pdf">
                            <small class="text-muted">Upload screenshot or document as proof of resolution</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addIssueForm" class="btn btn-primary">Report Issue</button>
            </div>
        </div>
    </div>
</div>

<!-- Issue Detail Modal -->
<div class="modal fade" id="issueDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Issue Details - #ISS001</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-12">
                        <h6 class="fw-semibold">Login form validation error</h6>
                        <p class="text-muted">Email validation is not working properly, allowing invalid email formats to be submitted.</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Related Risk</small>
                        <a href="#" class="badge bg-label-danger">#R005 - Security vulnerability</a>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Related Task</small>
                        <a href="#" class="badge bg-label-primary">Task #T142</a>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Assigned To</small>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                            </div>
                            <span>John Doe</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Deadline</small>
                        <span class="fw-medium">December 5, 2025</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block mb-1">Priority</small>
                        <span class="badge bg-danger">5 - Critical</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block mb-1">Severity</small>
                        <span class="badge bg-danger">5 - Critical</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block mb-1">Status</small>
                        <span class="badge bg-label-info">In Progress</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block mb-2">Resolution Proof</small>
                        <div class="card bg-label-secondary">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-file-text ti-lg me-2"></i>
                                    <div>
                                        <p class="mb-0">validation-fix-screenshot.png</p>
                                        <small class="text-muted">Uploaded: Dec 3, 2025</small>
                                    </div>
                                    <button class="btn btn-sm btn-icon btn-text-primary ms-auto">
                                        <i class="ti ti-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block mb-2">Activity Timeline</small>
                        <div class="timeline">
                            <div class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-warning"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Status changed to In Progress</h6>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                    <p class="mb-0">John Doe started working on this issue</p>
                                </div>
                            </div>
                            <div class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-info"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Issue assigned to John Doe</h6>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Issue created</h6>
                                        <small class="text-muted">2 days ago</small>
                                    </div>
                                    <p class="mb-0">Issue reported by Jane Smith</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success">Mark as Resolved</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('libs/select2/select2.js') }}"></script>
    <script src="{{ asset('libs/flatpickr/flatpickr.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#issuesTable').DataTable({
                order: [[4, 'desc']], // Sort by Priority
                columnDefs: [
                    { orderable: false, targets: [8] }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search issues...'
                }
            });

            // Initialize Select2
            $('.select2').select2({
                dropdownParent: function() {
                    return $(this).closest('.modal').length ? $(this).closest('.modal') : $('body');
                }
            });

            // Initialize Flatpickr
            $('.flatpickr-input').flatpickr({
                dateFormat: 'M d, Y',
                minDate: 'today'
            });

            // Filter functionality
            $('#filterStatus, #filterPriority, #filterSeverity, #filterAssignee').on('change', function() {
                const status = $('#filterStatus').val();
                const priority = $('#filterPriority').val();
                const severity = $('#filterSeverity').val();
                const assignee = $('#filterAssignee').val();
                
                table.draw();
            });

            // Form submission
            $('#addIssueForm').on('submit', function(e) {
                e.preventDefault();
                alert('Issue reported successfully!');
                $('#addIssueModal').modal('hide');
                this.reset();
            });
        });
    </script>
@endpush
