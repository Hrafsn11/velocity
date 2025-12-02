@extends('layouts.app')

@section('title', 'Risk Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/select2/select2.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Risk Management</h4>
            <p class="text-muted mb-0">Identifikasi dan kelola risiko proyek</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRiskModal">
            <i class="ti ti-plus me-1"></i>Add New Risk
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="mb-1">28</h5>
                            <small class="text-muted">Total Risks</small>
                        </div>
                        <span class="badge bg-label-danger rounded-circle p-2">
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
                            <h5 class="mb-1 text-danger">8</h5>
                            <small class="text-muted">Critical Risks</small>
                        </div>
                        <span class="badge bg-label-danger rounded-circle p-2">
                            <i class="ti ti-circle-filled ti-26px"></i>
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
                            <h5 class="mb-1 text-warning">12</h5>
                            <small class="text-muted">High Risks</small>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-circle-filled ti-26px"></i>
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
                            <h5 class="mb-1 text-info">8</h5>
                            <small class="text-muted">Medium/Low Risks</small>
                        </div>
                        <span class="badge bg-label-info rounded-circle p-2">
                            <i class="ti ti-circle-filled ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Risk List</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-risks table border-top" id="risksTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Risk Description</th>
                        <th>Category</th>
                        <th>Probability</th>
                        <th>Impact</th>
                        <th>Risk Score</th>
                        <th>Urgency Level</th>
                        <th>Affected Module</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Risk 1 -->
                    <tr>
                        <td><span class="fw-medium">#R001</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">Database server overload pada peak hours</span>
                                <small class="text-muted">Cause: Kurang optimasi query dan indexing</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-primary">Technical</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">4</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 80%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">5</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-danger" style="width: 100%"></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-danger rounded-pill">20</span></td>
                        <td><span class="badge bg-danger">Critical</span></td>
                        <td><span class="text-nowrap">Module Performance</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Risk 2 -->
                    <tr>
                        <td><span class="fw-medium">#R002</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">Key developer resign mendadak</span>
                                <small class="text-muted">Cause: Work-life balance issues</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-warning">SDM</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">3</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 60%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">4</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 80%"></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-warning rounded-pill">12</span></td>
                        <td><span class="badge bg-warning">High</span></td>
                        <td><span class="text-nowrap">All Development Tasks</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Risk 3 -->
                    <tr>
                        <td><span class="fw-medium">#R003</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">Budget overrun karena scope creep</span>
                                <small class="text-muted">Cause: Permintaan fitur tambahan tanpa approval formal</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-success">Financial</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">4</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 80%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">3</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 60%"></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-warning rounded-pill">12</span></td>
                        <td><span class="badge bg-warning">High</span></td>
                        <td><span class="text-nowrap">Budget Planning</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Risk 4 -->
                    <tr>
                        <td><span class="fw-medium">#R004</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">Delay integrasi third-party API</span>
                                <small class="text-muted">Cause: Dokumentasi API tidak lengkap dari vendor</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-info">Timeline</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">3</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 60%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">3</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 60%"></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-info rounded-pill">9</span></td>
                        <td><span class="badge bg-info">Medium</span></td>
                        <td><span class="text-nowrap">Integration Module</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Risk 5 -->
                    <tr>
                        <td><span class="fw-medium">#R005</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">Security vulnerability pada authentication module</span>
                                <small class="text-muted">Cause: Belum implementasi 2FA dan rate limiting</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-primary">Technical</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">2</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 40%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">5</span>
                                <div class="progress w-px-50" style="height: 6px;">
                                    <div class="progress-bar bg-danger" style="width: 100%"></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-warning rounded-pill">10</span></td>
                        <td><span class="badge bg-warning">High</span></td>
                        <td><span class="text-nowrap">Authentication Module</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-toggle="tooltip" title="Delete">
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

<!-- Add Risk Modal -->
<div class="modal fade" id="addRiskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Risk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRiskForm">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" for="riskDescription">Risk Description</label>
                            <textarea class="form-control" id="riskDescription" rows="3" placeholder="Describe the risk..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="riskCause">Potential Cause</label>
                            <textarea class="form-control" id="riskCause" rows="2" placeholder="What could cause this risk?" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="affectedModule">Affected Module/Feature/Task</label>
                            <select class="form-select select2" id="affectedModule" required>
                                <option value="">Select Module</option>
                                <option value="auth">Authentication Module</option>
                                <option value="dashboard">Dashboard Module</option>
                                <option value="reporting">Reporting Module</option>
                                <option value="integration">Integration Module</option>
                                <option value="performance">Performance Module</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="riskCategory">Risk Category</label>
                            <select class="form-select" id="riskCategory" required>
                                <option value="">Select Category</option>
                                <option value="sdm">SDM (Human Resource)</option>
                                <option value="technical">Technical</option>
                                <option value="financial">Financial</option>
                                <option value="timeline">Timeline</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="probability">Probability (1-5)</label>
                            <input type="number" class="form-control" id="probability" min="1" max="5" value="3" required>
                            <small class="text-muted">1=Very Low, 5=Very High</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="impact">Impact (1-5)</label>
                            <input type="number" class="form-control" id="impact" min="1" max="5" value="3" required>
                            <small class="text-muted">1=Very Low, 5=Very High</small>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="ti ti-info-circle me-2"></i>
                                <div>
                                    <strong>Risk Score: <span id="calculatedScore">9</span></strong><br>
                                    <small>Urgency Level: <span id="calculatedUrgency" class="badge bg-info">Medium</span></small>
                                </div>
                            </div>
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
    <script src="{{ asset('libs/select2/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#risksTable').DataTable({
                order: [[5, 'desc']], // Sort by Risk Score
                columnDefs: [
                    { orderable: false, targets: [8] } // Disable ordering on Actions column
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search risks...'
                }
            });

            // Initialize Select2
            $('.select2').select2({
                dropdownParent: $('#addRiskModal')
            });

            // Calculate Risk Score automatically
            function calculateRiskScore() {
                const probability = parseInt($('#probability').val()) || 0;
                const impact = parseInt($('#impact').val()) || 0;
                const score = probability * impact;
                
                $('#calculatedScore').text(score);
                
                // Determine urgency level
                let urgency = 'Low';
                let badgeClass = 'bg-success';
                
                if (score >= 15) {
                    urgency = 'Critical';
                    badgeClass = 'bg-danger';
                } else if (score >= 10) {
                    urgency = 'High';
                    badgeClass = 'bg-warning';
                } else if (score >= 6) {
                    urgency = 'Medium';
                    badgeClass = 'bg-info';
                }
                
                $('#calculatedUrgency').removeClass().addClass('badge ' + badgeClass).text(urgency);
            }

            $('#probability, #impact').on('input change', calculateRiskScore);

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Form submission
            $('#addRiskForm').on('submit', function(e) {
                e.preventDefault();
                // Handle form submission
                alert('Risk added successfully!');
                $('#addRiskModal').modal('hide');
                this.reset();
            });
        });
    </script>
@endpush
