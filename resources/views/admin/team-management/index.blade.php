@extends('layouts.app')

@section('title', 'Team Management')

@section('content')

    <div class="row g-4 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Employees</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['total'] }}</h4>
                                <small class="text-success fw-medium">(+2 New)</small>
                            </div>
                            <p class="mb-0 text-muted">Total headcount</p>
                        </div>
                        <span class="avatar p-2 rounded bg-label-primary">
                            <i class="ti ti-users ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Fully Booked</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['busy'] }}</h4>
                                @php $busyPercent = ($stats['busy'] / $stats['total']) * 100; @endphp
                                <small class="text-danger fw-medium">({{ round($busyPercent) }}%)</small>
                            </div>
                            <p class="mb-0 text-muted">High workload</p>
                        </div>
                        <span class="avatar p-2 rounded bg-label-danger">
                            <i class="ti ti-user-exclamation ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Available / Free</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['available'] }}</h4>
                                <small class="text-success fw-medium">Ready</small>
                            </div>
                            <p class="mb-0 text-muted">Open for tasks</p>
                        </div>
                        <span class="avatar p-2 rounded bg-label-success">
                            <i class="ti ti-user-check ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>On Leave / Offline</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['offline'] }}</h4>
                                <small class="text-muted fw-medium">Today</small>
                            </div>
                            <p class="mb-0 text-muted">Not available</p>
                        </div>
                        <span class="avatar p-2 rounded bg-label-secondary">
                            <i class="ti ti-user-off ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Employee List</h5>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"
                    aria-controls="offcanvasAddUser">
                    <i class="ti ti-plus me-1"></i> Add Employee
                </button>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th style="min-width: 250px;">Employee</th>
                        <th style="min-width: 200px;">Role & Specialization</th>
                        <th style="min-width: 150px;">Active Projects</th>
                        <th style="min-width: 150px;">Workload</th>
                        <th style="min-width: 200px;">Main Skills</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teams as $team)
                        <tr>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ asset($team['avatar']) }}" alt="Avatar"
                                                class="rounded-circle object-fit-cover">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#" class="text-body text-truncate"><span
                                                class="fw-semibold">{{ $team['name'] }}</span></a>
                                        <small class="text-muted">{{ $team['email'] }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="avatar-initial rounded bg-label-secondary me-2 p-1 d-flex align-items-center justify-content-center"
                                        style="width:32px; height:32px;">
                                        @if (str_contains(strtolower($team['role']), 'programmer'))
                                            <i class="ti ti-code"></i>
                                        @elseif(str_contains(strtolower($team['role']), 'design'))
                                            <i class="ti ti-palette"></i>
                                        @elseif(str_contains(strtolower($team['role']), 'qa'))
                                            <i class="ti ti-bug"></i>
                                        @else
                                            <i class="ti ti-briefcase"></i>
                                        @endif
                                    </span>
                                    <div class="d-flex flex-column">
                                        <span
                                            class="text-heading text-truncate fw-medium">{{ $team['specialization'] }}</span>

                                        @php
                                            $levelClass = match ($team['level']) {
                                                'Senior' => 'text-warning',
                                                'Lead' => 'text-primary',
                                                'Junior' => 'text-success',
                                                default => 'text-secondary',
                                            };
                                        @endphp
                                        <small class="{{ $levelClass }}">{{ $team['level'] }} •
                                            {{ $team['role'] }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center avatar-group">
                                    <div class="avatar avatar-xs pull-up" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Velocity App">
                                        <span class="avatar-initial rounded-circle bg-primary"><i
                                                class="ti ti-bolt ti-xs"></i></span>
                                    </div>
                                    <div class="avatar avatar-xs pull-up" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="CRM Dashboard">
                                        <span class="avatar-initial rounded-circle bg-success"><i
                                                class="ti ti-chart-pie-2 ti-xs"></i></span>
                                    </div>
                                    @if ($team['projects_count'] > 2)
                                        <div class="avatar avatar-xs">
                                            <span class="avatar-initial rounded-circle pull-up bg-secondary text-white"
                                                style="font-size: 0.7rem;" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ $team['projects_count'] - 2 }} more">
                                                +{{ $team['projects_count'] - 2 }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @php
                                    $load = $team['projects_count'] * 20;
                                    if ($load > 100) {
                                        $load = 100;
                                    }
                                    $barColor = $load > 80 ? 'bg-danger' : ($load > 50 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <div class="d-flex align-items-center mb-1">
                                    <small class="text-muted me-1">{{ $load }}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $barColor }}" role="progressbar"
                                        style="width: {{ $load }}%" aria-valuenow="{{ $load }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach ($team['skills'] as $skill)
                                        <span class="badge rounded-pill bg-label-secondary"
                                            style="font-size: 0.7rem;">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </td>

                            <td>
                                @php
                                    $statusBadge = match ($team['status']) {
                                        'online' => 'bg-label-success',
                                        'busy' => 'bg-label-danger',
                                        'offline' => 'bg-label-secondary',
                                        default => 'bg-label-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ ucfirst($team['status']) }}</span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:;" class="text-body me-2"><i class="ti ti-edit ti-sm"></i></a>
                                    <a href="javascript:;" class="text-body"><i
                                            class="ti ti-trash ti-sm text-danger"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item prev disabled"><a class="page-link" href="javascript:void(0);"><i
                                class="ti ti-chevron-left ti-xs"></i></a></li>
                    <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                    <li class="page-item next"><a class="page-link" href="javascript:void(0);"><i
                                class="ti ti-chevron-right ti-xs"></i></a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Employee</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 p-4 h-100">
            <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false">

                <div class="mb-3">
                    <label class="form-label" for="add-user-fullname">Full Name</label>
                    <input type="text" class="form-control" id="add-user-fullname" placeholder="John Doe"
                        name="userFullname" aria-label="John Doe" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="add-user-email">Email</label>
                    <input type="text" id="add-user-email" class="form-control" placeholder="john.doe@velocity.com"
                        aria-label="john.doe@example.com" name="userEmail" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="user-role">Role Category</label>
                    <select id="user-role" class="form-select">
                        <option value="Programmer">Programmer</option>
                        <option value="UI/UX Designer">UI/UX Designer</option>
                        <option value="System Analyst">System Analyst</option>
                        <option value="QA Engineer">QA Engineer</option>
                        <option value="Project Manager">Project Manager</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="user-specialization">Specialization</label>
                    <input type="text" id="user-specialization" class="form-control"
                        placeholder="e.g. Frontend Developer, Product Designer" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="user-level">Seniority Level</label>
                    <select id="user-level" class="form-select">
                        <option value="Intern">Intern</option>
                        <option value="Junior">Junior</option>
                        <option value="Middle">Middle</option>
                        <option value="Senior">Senior</option>
                        <option value="Lead">Lead</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="user-skills">Main Skills</label>
                    <input type="text" id="user-skills" class="form-control" placeholder="e.g. Laravel, React" />
                    <div class="form-text">Separate with comma</div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
