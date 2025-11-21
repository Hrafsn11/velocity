@extends('layouts.app')

@section('title', 'My Workspaces')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex flex-column justify-content-center">
            <h4 class="fw-bold mb-0">My Workspaces</h4>
            <p class="text-muted mb-0">Manage your projects and team collaborations</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="input-group input-group-merge" style="width: 250px;">
                <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" placeholder="Search workspace..." aria-label="Search..."
                    aria-describedby="basic-addon-search31" />
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-bolt ti-md"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Velocity App</h5>
                            <small class="text-muted">SaaS Project</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="ws1" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="ti ti-dots-vertical text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="ws1">
                            <a class="dropdown-item" href="javascript:void(0);">View Details</a>
                            <a class="dropdown-item" href="javascript:void(0);">Edit Workspace</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="javascript:void(0);">Archive</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-4 text-muted">
                        Project management dashboard for agile teams with sprint capabilities.
                    </p>

                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-label-success me-2 p-1 px-2">Active</span>
                        <small class="ms-auto fw-semibold">85% Completed</small>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%" aria-valuenow="85"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                        <div class="d-flex align-items-center">
                            <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    title="Vinnie Mostowy" class="avatar avatar-xs pull-up">
                                    <img class="rounded-circle" src="{{ asset('assets/img/avatars/1.png') }}"
                                        alt="Avatar" />
                                </li>
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    title="Allen Rieske" class="avatar avatar-xs pull-up">
                                    <img class="rounded-circle" src="{{ asset('assets/img/avatars/9.png') }}"
                                        alt="Avatar" />
                                </li>
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    title="Julee Rossignol" class="avatar avatar-xs pull-up">
                                    <span class="avatar-initial rounded-circle bg-label-info">JR</span>
                                </li>
                                <li class="avatar avatar-xs">
                                    <span class="avatar-initial rounded-circle pull-up" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="3 more">+3</span>
                                </li>
                            </ul>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Due Date</small>
                            <small class="fw-bold">12 Nov 2025</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti ti-chart-pie-2 ti-md"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">CRM Dashboard</h5>
                            <small class="text-muted">Internal Tool</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="ws2" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="javascript:void(0);">View Details</a>
                            <a class="dropdown-item text-danger" href="javascript:void(0);">Archive</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-4 text-muted">
                        Customer relationship management for sales team tracking leads.
                    </p>

                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-label-warning me-2 p-1 px-2">Review</span>
                        <small class="ms-auto fw-semibold">45% Completed</small>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 45%" aria-valuenow="45"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                        <div class="d-flex align-items-center">
                            <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    title="Kaith D'souza" class="avatar avatar-xs pull-up">
                                    <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png') }}"
                                        alt="Avatar" />
                                </li>
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    title="John Doe" class="avatar avatar-xs pull-up">
                                    <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png') }}"
                                        alt="Avatar" />
                                </li>
                            </ul>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Due Date</small>
                            <small class="fw-bold">25 Dec 2025</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti ti-device-mobile ti-md"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Velocity Mobile</h5>
                            <small class="text-muted">App Development</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="ws3" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="javascript:void(0);">View Details</a>
                            <a class="dropdown-item text-danger" href="javascript:void(0);">Archive</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-4 text-muted">
                        Native mobile application for iOS and Android platforms.
                    </p>

                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-label-info me-2 p-1 px-2">Planning</span>
                        <small class="ms-auto fw-semibold">15% Completed</small>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 15%" aria-valuenow="15"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                        <div class="d-flex align-items-center">
                            <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    title="Andrew Tye" class="avatar avatar-xs pull-up">
                                    <span class="avatar-initial rounded-circle bg-label-success">AT</span>
                                </li>
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                    title="Roxie Miles" class="avatar avatar-xs pull-up">
                                    <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}"
                                        alt="Avatar" />
                                </li>
                            </ul>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Due Date</small>
                            <small class="fw-bold">01 Jan 2026</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
