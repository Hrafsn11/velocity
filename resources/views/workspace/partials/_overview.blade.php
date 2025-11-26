<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-lg rounded bg-label-primary">
                    @if(optional($summary['workspace'])->image_path)
                        <img src="{{ workspace_image_url(optional($summary['workspace'])->image_path) }}" alt="{{ optional($summary['workspace'])->title }}" class="rounded"/>
                    @else
                        <span class="avatar-initial rounded"><i class="ti ti-bolt ti-md"></i></span>
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold text-heading mb-0">{{ optional($summary['workspace'])->title ?? 'Workspace' }}</h4>
                        @if(optional($summary['workspace'])->status)
                            <span class="badge {{ optional($summary['workspace'])->status->badgeClass() ?? 'bg-label-secondary' }}">{{ optional($summary['workspace'])->status->label() ?? optional($summary['workspace'])->status }}</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center text-muted mt-1 small">
                        <span>{{ optional($summary['workspace'])->description ? \Illuminate\Support\Str::limit(optional($summary['workspace'])->description, 80) : '' }}</span>
                        @if($summary['start_date'] || $summary['end_date'])
                            <span class="mx-2">•</span>
                            <span class="fw-medium text-body">{{ $summary['start_date'] ?? 'TBD' }} — {{ $summary['end_date'] ?? 'TBD' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                <div class="d-flex align-items-center">
                    <span class="text-muted small me-2 fw-bold text-uppercase">Team:</span>
                    <div class="avatar-group d-flex align-items-center">
                        @php
                            $members = $summary['members'] ?? [];
                            $totalMembers = $summary['members_count'] ?? count($members);
                            $displayLimit = 4;
                        @endphp
                        @foreach($members as $idx => $member)
                            @if($idx >= $displayLimit) @break @endif
                            @php
                                $tooltip = trim(($member['name'] ?? '') . ' • ' . ($member['role'] ?? '') . ($member['level'] ? ' • ' . $member['level'] : ''));
                            @endphp
                            <div class="avatar avatar-sm pull-up" data-bs-toggle="tooltip" title="{{ $tooltip }}">
                                @if(!empty($member['avatar_url']))
                                    <img src="{{ $member['avatar_url'] }}" alt="{{ $member['name'] }}" class="rounded-circle" />
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-secondary text-heading">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($member['name'] ?? 'NA',0,1)) }}</span>
                                @endif
                            </div>
                        @endforeach
                        @if($totalMembers > $displayLimit)
                            <button type="button" class="avatar avatar-sm btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#membersModal">
                                <span class="avatar-initial rounded-circle">+{{ $totalMembers - $displayLimit }}</span>
                            </button>
                        @endif
                        @endif
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
                    <a href="{{ route('workspaces.show', optional($summary['workspace'])) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-eye me-1"></i> View
                    </a>
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
            {{-- Tabs remain in parent view, keep for backward compatibility --}}
        </ul>
    </div>
</div>

<!-- Members Modal -->
<div class="modal fade" id="membersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Project Members ({{ $summary['members_count'] ?? count($summary['members_full'] ?? []) }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    @foreach($summary['members_full'] ?? [] as $m)
                        @php
                            $initials = collect(explode(' ', $m['name'] ?? ''))->map(fn($w) => strtoupper(substr($w,0,1)))->filter()->take(2)->join('') ?: 'NA';
                            $bg = $m['level_badge'] ?? $m['role_badge'] ?? 'bg-label-secondary';
                        @endphp
                        <div class="list-group-item d-flex align-items-center gap-3">
                            <div style="width:40px;flex:0 0 40px;">
                                @if(!empty($m['avatar_url']))
                                    <img src="{{ $m['avatar_url'] }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" alt="{{ $m['name'] }}">
                                @else
                                    <span class="avatar-initial rounded-circle {{ $bg }} text-white d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;font-weight:700;">{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $m['name'] }}</div>
                                <small class="text-muted">{{ ucfirst($m['role'] ?? 'Member') }} @if($m['level']) • {{ ucfirst($m['level']) }}@endif</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nav-align-top mb-4">
    <div class="tab-content p-0 bg-transparent shadow-none">
        <div class="tab-pane fade show active" id="tab-overview-inner" role="tabpanel">
            <div class="row g-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="card card-summary mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-initial rounded bg-label-primary p-1"><i class="ti ti-sparkles"></i></span>
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
                                        <p class="small text-muted mt-2 mb-0">Progress berdasarkan data task/sprint.</p>
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
                                <button class="btn btn-sm btn-outline-success {{ optional($summary['workspace'])->status && optional($summary['workspace'])->status->value === \App\Enums\WorkspaceStatus::ACTIVE->value ? 'active' : '' }}">
                                    <span class="status-pulse bg-success"></span> On Track
                                </button>
                                <button class="btn btn-sm btn-outline-warning">At Risk</button>
                                <button class="btn btn-sm btn-outline-danger">Off Track</button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Timeline</span>
                                <span class="fw-bold small">{{ $summary['end_date'] ?? 'TBD' }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ max(0, min(100, $summary['progress'])) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Activity</h5>
                        </div>
                        <div class="card-body pb-0">
                            <ul class="timeline ms-2">
                                @forelse($summary['recent_activities'] as $activity)
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
                                @empty
                                    <li class="timeline-item timeline-item-transparent border-transparent pb-4">
                                        <span class="timeline-point timeline-point-secondary"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                                <h6 class="mb-0 text-sm">No recent activity</h6>
                                            </div>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
