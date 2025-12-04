@extends('layouts.app')

@section('title', 'Risk Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="ti ti-dashboard text-primary me-2"></i>Global Risk Management Dashboard
                </h4>
                <p class="text-muted mb-0">Overview dan monitoring risiko across all workspaces</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('risk.index') }}" class="btn btn-label-primary">
                    <i class="ti ti-list me-1"></i>All Risks
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

    {{-- Workspace Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('risk.dashboard') }}" class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label mb-1">
                        <i class="ti ti-filter me-1"></i>Filter by Workspace
                    </label>
                    <select class="form-select" name="workspace_id" onchange="this.form.submit()">
                        <option value="">🌐 All Workspaces (Global View)</option>
                        @foreach($workspaces as $ws)
                            <option value="{{ $ws->workspace_id }}" {{ $workspaceId == $ws->workspace_id ? 'selected' : '' }}>
                                {{ $ws->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2 mt-3 mt-md-0 justify-content-md-end">
                        <a href="{{ route('risk.dashboard') }}" class="btn btn-label-secondary">
                            <i class="ti ti-refresh me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Global Statistics Cards --}}
    <div class="row g-4 mb-4">
        {{-- Total Risks --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1">{{ $stats['total'] }}</h5>
                            <small class="text-muted">Total Risks</small>
                            @if(!$workspaceId)
                            <div class="mt-2">
                                <span class="badge bg-label-primary me-1">Across {{ $workspaces->count() }} workspaces</span>
                            </div>
                            @endif
                        </div>
                        <span class="badge bg-label-primary rounded-circle p-2">
                            <i class="ti ti-alert-triangle ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Risks --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-danger">{{ $stats['by_status']['active'] }}</h5>
                            <small class="text-muted">Active Risks</small>
                            <div class="mt-2">
                                <span class="badge bg-label-danger me-1">
                                    {{ $stats['total'] > 0 ? round(($stats['by_status']['active'] / $stats['total']) * 100, 1) : 0 }}%
                                </span>
                                <small class="text-muted">of total</small>
                            </div>
                        </div>
                        <span class="badge bg-label-danger rounded-circle p-2">
                            <i class="ti ti-alert-circle ti-26px"></i>
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
                            <h5 class="fw-bold mb-1 text-success">{{ $stats['by_status']['mitigated'] }}</h5>
                            <small class="text-muted">Mitigated</small>
                            <div class="mt-2">
                                <span class="badge bg-label-success me-1">
                                    {{ $stats['total'] > 0 ? round(($stats['by_status']['mitigated'] / $stats['total']) * 100, 1) : 0 }}%
                                </span>
                                <small class="text-muted">success rate</small>
                            </div>
                        </div>
                        <span class="badge bg-label-success rounded-circle p-2">
                            <i class="ti ti-shield-check ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Critical Risks --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h5 class="fw-bold mb-1 text-warning">{{ $stats['by_urgency']['critical'] }}</h5>
                            <small class="text-muted">Critical Risks</small>
                            <div class="mt-2">
                                <span class="badge bg-label-warning me-1">Need immediate action</span>
                            </div>
                        </div>
                        <span class="badge bg-label-warning rounded-circle p-2">
                            <i class="ti ti-alert-octagon ti-26px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Risk Status Breakdown --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Risk Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Active</span>
                            <strong class="text-danger">{{ $stats['by_status']['active'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['total'] > 0 ? ($stats['by_status']['active'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Monitoring</span>
                            <strong class="text-info">{{ $stats['by_status']['monitoring'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['total'] > 0 ? ($stats['by_status']['monitoring'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Mitigated</span>
                            <strong class="text-success">{{ $stats['by_status']['mitigated'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? ($stats['by_status']['mitigated'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Materialized (To Issue)</span>
                            <strong class="text-warning">{{ $stats['by_status']['materialized'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total'] > 0 ? ($stats['by_status']['materialized'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Closed</span>
                            <strong class="text-secondary">{{ $stats['by_status']['closed'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $stats['total'] > 0 ? ($stats['by_status']['closed'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Risk Urgency Levels</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="ti ti-alert-octagon text-danger me-1"></i>Critical</span>
                            <strong class="text-danger">{{ $stats['by_urgency']['critical'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['total'] > 0 ? ($stats['by_urgency']['critical'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="ti ti-alert-triangle text-warning me-1"></i>High</span>
                            <strong class="text-warning">{{ $stats['by_urgency']['high'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total'] > 0 ? ($stats['by_urgency']['high'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="ti ti-alert-circle text-info me-1"></i>Medium</span>
                            <strong class="text-info">{{ $stats['by_urgency']['medium'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['total'] > 0 ? ($stats['by_urgency']['medium'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="ti ti-check text-success me-1"></i>Low</span>
                            <strong class="text-success">{{ $stats['by_urgency']['low'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? ($stats['by_urgency']['low'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$workspaceId)
    {{-- Workspace Comparison Table --}}
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">
                <i class="ti ti-building me-2"></i>Risk Status by Workspace
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Workspace</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Active</th>
                        <th class="text-center">Mitigated</th>
                        <th class="text-center">To Issues</th>
                        <th class="text-center">Critical</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workspaces as $ws)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-primary">
                                    <span class="avatar-initial rounded">{{ strtoupper(substr($ws->title, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <strong>{{ $ws->title }}</strong>
                                    <br><small class="text-muted">{{ $ws->description ?? 'No description' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-primary rounded-pill">{{ $ws->risks_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger rounded-pill">{{ $ws->active_risks_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success rounded-pill">{{ $ws->mitigated_risks_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning rounded-pill">{{ $ws->issues_count }}</span>
                        </td>
                        <td class="text-center">
                            @if($ws->critical_risks_count > 0)
                                <span class="badge bg-danger">{{ $ws->critical_risks_count }} critical</span>
                            @else
                                <span class="badge bg-success">None</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('risk.index', ['workspace_id' => $ws->workspace_id]) }}" class="btn btn-sm btn-label-primary">
                                <i class="ti ti-eye me-1"></i>View Risks
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="ti ti-folder-off ti-lg mb-2"></i>
                            <p class="mb-0">No workspaces found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
