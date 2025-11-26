@extends('layouts.app')

@section('title', optional($summary['workspace'])->title ?? 'Workspace')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/workspace-project.css') }}">
@endpush

@section('content')

    <div class="card mb-4">
        <div class="card-body">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">

                <div class="d-flex align-items-center gap-3">
                    @php
                        $workspaceImage = workspace_image_url(optional($summary['workspace'])->image_path);
                        $workspaceInitials = workspace_initials(optional($summary['workspace'])->title ?? 'WP');
                    @endphp
                    <div class="avatar avatar-lg rounded">
                        @if($workspaceImage)
                            <img src="{{ $workspaceImage }}" alt="{{ optional($summary['workspace'])->title }}" class="rounded" style="width:56px;height:56px;object-fit:cover;" />
                        @else
                            <span class="avatar-initial rounded-circle bg-label-primary" style="width:56px;height:56px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;">{{ $workspaceInitials }}</span>
                        @endif
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fw-bold text-heading mb-0">{{ optional($summary['workspace'])->title ?? 'Workspace' }}</h4>
                            @if(optional($summary['workspace'])->status)
                                <span class="badge {{ optional($summary['workspace'])->status->badgeClass() ?? 'bg-label-secondary' }}">{{ optional($summary['workspace'])->status->label() ?? optional($summary['workspace'])->status }}</span>
                            @endif
                        </div>
                        {{-- <div class="d-flex align-items-center text-muted mt-1 small">
                            <span>Project Management</span>
                            <span class="mx-2">•</span>
                            <span class="fw-medium text-body">Phase 2</span>
                        </div> --}}
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">

                    <div class="d-flex align-items-center">
                        <span class="text-muted small me-2 fw-bold text-uppercase">Team:</span>

                        <div class="avatar-group d-flex align-items-center">
                            @foreach ($summary['members'] as $member)
                                @php
                                    $initials = collect(explode(' ', $member['name'] ?? ''))->map(fn($w) => strtoupper(substr($w,0,1)))->filter()->take(2)->join('');
                                    $initials = $initials ?: 'NA';
                                    // pick background class: prefer level_badge or role_badge, otherwise deterministic
                                    $bg = $member['level_badge'] ?? $member['role_badge'] ?? null;
                                    if (!$bg) {
                                        $colors = ['bg-label-primary','bg-label-success','bg-label-info','bg-label-warning','bg-label-danger','bg-label-secondary'];
                                        $bg = $colors[crc32($member['name'] ?? 'na') % count($colors)];
                                    }
                                @endphp
                                <div class="avatar avatar-sm pull-up" data-bs-toggle="tooltip" title="{{ $member['name'] }} • {{ $member['role'] }} • {{ $member['level'] }}">
                                    @if(!empty($member['avatar_url']))
                                        <img src="{{ $member['avatar_url'] }}" alt="{{ $member['name'] }}" class="rounded-circle">
                                    @else
                                        <span class="avatar-initial rounded-circle {{ $bg }} text-white" style="font-weight:700;">{{ $initials }}</span>
                                    @endif
                                </div>
                            @endforeach
                            @role('Super Admin')
                                <div class="avatar avatar-sm pull-up" data-bs-toggle="tooltip" title="Add Member">
                                    <span class="avatar-initial rounded-circle bg-label-secondary text-heading">
                                        <i class="ti ti-plus ti-xs"></i>
                                    </span>
                                </div>
                            @endrole
                        </div>
                    </div>

                    <div class="vr d-none d-sm-block" style="height: 24px; opacity: 0.2;"></div>

                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-primary btn-sm">
                            <i class="ti ti-share me-1"></i> Share
                        </button>
                        @role('Super Admin')
                            <button class="btn btn-icon btn-label-secondary btn-sm rounded-circle">
                                <i class="ti ti-settings"></i>
                            </button>
                        @endrole
                    </div>

                </div>
            </div>
        </div>

        <div class="card-footer p-0 border-top">
            <ul class="nav nav-tabs nav-fill w-100" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active py-3" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tab-overview">
                        <i class="ti ti-info-circle me-1"></i> Overview
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tab-list">
                        <i class="ti ti-list-check me-1"></i> List
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tab-board">
                        <i class="ti ti-layout-kanban me-1"></i> Board
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tab-timeline">
                        <i class="ti ti-timeline me-1"></i> Timeline
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tab-dashboard">
                        <i class="ti ti-chart-pie me-1"></i> Dashboard
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tab-calendar">
                        <i class="ti ti-calendar me-1"></i> Calendar
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab"
                        data-bs-target="#tab-files">
                        <i class="ti ti-folder me-1"></i> Files
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="nav-align-top mb-4">
        <div class="tab-content p-0 bg-transparent shadow-none">
            <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                <div class="row g-4">

                    <div class="col-xl-8 col-lg-7">

                        <div class="card card-summary mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar-initial rounded bg-label-primary p-1"><i
                                                class="ti ti-sparkles"></i></span>
                                        <h6 class="mb-0 fw-bold">Project Health Summary</h6>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="bg-white dark:bg-dark p-3 rounded border h-100">
                                            <div class="d-flex justify-content-between mb-2">
                                                <small class="text-muted text-uppercase fw-bold">Completion</small>
                                                <small class="fw-bold text-primary">{{ $summary['progress'] }}%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $summary['progress'] }}%"></div>
                                            </div>
                                            <p class="small text-muted mt-2 mb-0">Progress is steady based on sprint
                                                velocity.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-white dark:bg-dark p-3 rounded border h-100">
                                            <small class="text-muted text-uppercase fw-bold d-block mb-2">Key Risk</small>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-alert-triangle text-warning"></i>
                                                <span class="small fw-medium">API Integration Delay</span>
                                            </div>
                                            <p class="small text-muted mt-1 mb-0">Might affect the timeline by 2 days.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Project Description</h5>
                            </div>
                            <div class="card-body">
                                <div id="project-description">
                                    {!! optional($summary['workspace'])->description ?? '<em>No description</em>' !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-5">

                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4">What's the status?</h5>

                                <div class="d-flex gap-2 mb-4">
                                    <button class="btn btn-sm btn-outline-success active">
                                        <span class="status-pulse bg-success"></span> On Track
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning">
                                        At Risk
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        Off Track
                                    </button>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Timeline</span>
                                    <span class="fw-bold small">{{ $summary['end_date'] ?? 'TBD' }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body pb-0">
                                <ul class="timeline ms-2">
                                    @foreach ($summary['recent_activities'] as $activity)
                                        <li class="timeline-item timeline-item-transparent border-transparent pb-4">
                                            <span class="timeline-point timeline-point-secondary"></span>
                                            <div class="timeline-event">
                                                <div class="timeline-header mb-1">
                                                    <h6 class="mb-0 text-sm">{{ $activity['user'] ?? 'System' }}</h6>
                                                    <small class="text-muted">{{ $activity['time'] ?? '' }}</small>
                                                </div>
                                                <p class="mb-0 text-sm">{{ $activity['action'] ?? '' }}</p>
                                            </div>
                                        </li>
                                    @endforeach

                                    <li class="timeline-item timeline-item-transparent border-transparent">
                                        <span class="timeline-point timeline-point-primary"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header">
                                                <h6 class="mb-0 text-sm">Project Created</h6>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-list" role="tabpanel">
                <h1>ini adalah tab list</h1>
            </div>

            <div class="tab-pane fade" id="tab-board" role="tabpanel">
                @include('workspace.partials._board-tab')
            </div>

            <div class="tab-pane fade" id="tab-timeline" role="tabpanel">
                <h1>ini adalah tab timeline</h1>
            </div>

            <div class="tab-pane fade" id="tab-dashboard" role="tabpanel">
                <h1>ini adalah tab dashboard</h1>
            </div>

            <div class="tab-pane fade" id="tab-calendar" role="tabpanel">
                @include('workspace.partials._calendar-tab')
            </div>

            <div class="tab-pane fade" id="tab-files" role="tabpanel">
                <h1>ini adalah tab files</h1>
            </div>

        </div>
    </div>

    @include('workspace.partials._drawer-kanban')
    @include('workspace.partials._drawer-calendar')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/workspace-calendar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for all assets to load before initializing sub-components
            
            // Initialize Flatpickr for inline calendar
            if (document.querySelector('.inline-calendar')) {
                flatpickr('.inline-calendar', {
                    inline: true,
                    onChange: function(selectedDates) {
                        if (window.workspaceCalendar && window.workspaceCalendar.calendar) {
                            window.workspaceCalendar.calendar.gotoDate(selectedDates[0]);
                        }
                    }
                });
            }

            // Initialize Select2 for event labels
            if ($('.select-event-label').length) {
                $('.select-event-label').select2({
                    dropdownParent: $('#addEventSidebar')
                });
            }

            // Initialize Flatpickr for event form dates
            if (document.getElementById('eventStartDate')) {
                flatpickr("#eventStartDate", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i"
                });
            }

            if (document.getElementById('eventEndDate')) {
                flatpickr("#eventEndDate", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i"
                });
            }

            // Calendar filter logic
            const selectAll = document.querySelector('.select-all');
            const filterInputs = document.querySelectorAll('.input-filter');

            if (selectAll) {
                selectAll.addEventListener('click', function(e) {
                    const isChecked = e.currentTarget.checked;
                    filterInputs.forEach(input => input.checked = isChecked);
                    // TODO: Implement actual event filtering
                });
            }

            // Individual filter checkboxes
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // TODO: Implement individual filter logic
                });
            });
        });
    </script>
@endpush

