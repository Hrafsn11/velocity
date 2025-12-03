@extends('layouts.app')

@section('title', 'Risk Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/apex-charts/apex-charts.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="ti ti-dashboard text-primary me-2"></i>Risk Management Dashboard
                </h4>
                <p class="text-muted mb-0">Overview dan monitoring risiko proyek</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('risk.index') }}" class="btn btn-label-primary">
                    <i class="ti ti-list me-1"></i>Risk List
                </a>
                <a href="{{ route('risk.issues') }}" class="btn btn-label-warning">
                    <i class="ti ti-bug me-1"></i>Issues
                </a>
                <a href="{{ route('risk.change-requests') }}" class="btn btn-label-info">
                    <i class="ti ti-arrow-right-circle me-1"></i>Change Requests
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards - Risk Breakdown --}}
    <div class="row g-4 mb-4">
        {{-- Total Risks --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1">28</h5>
                            <small class="text-muted">Total Risks Identified</small>
                            <div class="mt-2">
                                <span class="badge bg-label-primary me-1">Active tracking</span>
                            </div>
                        </div>
                        <span class="badge bg-label-primary rounded-circle p-2">
                            <i class="ti ti-alert-triangle ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mitigated Risks --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-success">12</h5>
                            <small class="text-muted">Successfully Mitigated</small>
                            <div class="mt-2">
                                <span class="badge bg-label-success me-1">42.8%</span>
                                <small class="text-muted">of total</small>
                            </div>
                        </div>
                        <span class="badge bg-label-success rounded-circle p-2">
                            <i class="ti ti-shield-check ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Converted to Issues --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-warning">8</h5>
                            <small class="text-muted">Converted to Issues</small>
                            <div class="mt-2">
                                <span class="badge bg-label-warning me-1">28.5%</span>
                                <small class="text-muted">materialized</small>
                            </div>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-bug ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- With Change Requests --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-info">5</h5>
                            <small class="text-muted">Has Change Requests</small>
                            <div class="mt-2">
                                <span class="badge bg-label-info me-1">17.8%</span>
                                <small class="text-muted">need changes</small>
                            </div>
                        </div>
                        <span class="badge bg-label-info rounded-circle p-2">
                            <i class="ti ti-arrow-right-circle ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions & Recent Activity --}}
    <div class="row g-4">
        {{-- Quick Actions --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('risk.index') }}" class="btn btn-label-primary text-start">
                            <i class="ti ti-plus me-2"></i>Add New Risk
                        </a>
                        <a href="{{ route('risk.issues') }}" class="btn btn-label-warning text-start">
                            <i class="ti ti-bug me-2"></i>Report Issue
                        </a>
                        <a href="{{ route('risk.change-requests') }}" class="btn btn-label-info text-start">
                            <i class="ti ti-clock-edit me-2"></i>Create Change Request
                        </a>
                        <a href="{{ route('risk.index') }}" class="btn btn-label-secondary text-start">
                            <i class="ti ti-list me-2"></i>View All Risks
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-history me-2"></i>Recent Activity
                    </h5>
                    <small class="text-muted">Last 24 hours</small>
                </div>
                <div class="card-body">
                    <ul class="timeline mb-0">
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-danger"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Risk #R008 Materialized</h6>
                                    <small class="text-muted">2 hours ago</small>
                                </div>
                                <p class="mb-2">Risk "Payment gateway timeout" converted to Issue #ISS012</p>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">JD</span>
                                    </div>
                                    <span class="small">John Doe</span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-success"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">CR #CR001 Approved</h6>
                                    <small class="text-muted">4 hours ago</small>
                                </div>
                                <p class="mb-2">Timeline extension +14 days approved by Pak Cahyo</p>
                                <span class="badge bg-label-success">Auto-updated workspace</span>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-warning"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Issue #ISS011 Resolved</h6>
                                    <small class="text-muted">6 hours ago</small>
                                </div>
                                <p class="mb-2">Login validation error fixed and deployed</p>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-success">JS</span>
                                    </div>
                                    <span class="small">Jane Smith</span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-info"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">New Risk Added</h6>
                                    <small class="text-muted">8 hours ago</small>
                                </div>
                                <p class="mb-0">Risk #R009 - "Server capacity reached 80%"</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('libs/apex-charts/apexcharts.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
