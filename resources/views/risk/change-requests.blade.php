@extends('layouts.app')

@section('title', 'Change Requests')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="ti ti-arrow-right-circle text-info me-2"></i>All Change Requests
            </h4>
            <p class="text-muted mb-0">Timeline & Resource change requests</p>
        </div>
    </div>

    {{-- Workspace Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('risk.change-requests') }}" class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label mb-1 small">
                        <i class="ti ti-filter me-1"></i>Filter by Workspace
                    </label>
                    <select class="form-select form-select-sm" name="workspace_id" onchange="this.form.submit()">
                        <option value="">All Workspaces</option>
                        @foreach($workspaces as $ws)
                            <option value="{{ $ws->workspace_id }}" {{ request('workspace_id') == $ws->workspace_id ? 'selected' : '' }}>
                                {{ $ws->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-end mt-2 mt-md-0">
                    @if(request('workspace_id'))
                        <a href="{{ route('risk.change-requests') }}" class="btn btn-sm btn-label-secondary">
                            <i class="ti ti-x me-1"></i>Clear Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Change Requests List --}}
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Workspace</th>
                        <th>Type</th>
                        <th>Impact</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($changeRequests as $cr)
                    <tr>
                        <td>
                            <strong class="text-info">#{{ $cr->code }}</strong>
                        </td>
                        <td>
                            <div style="max-width: 250px;">
                                <strong>{{ Str::limit($cr->title, 60) }}</strong>
                                @if($cr->issue)
                                    <br><small class="text-muted">From Issue: <span class="text-warning">#{{ $cr->issue->code }}</span></small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">
                                {{ strtoupper(substr($cr->workspace->title, 0, 2)) }}
                            </span>
                            <br><small class="text-muted">{{ $cr->workspace->title }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $cr->type_badge }}">
                                {{ $cr->isTimelineType() ? 'Timeline Extension' : 'Resource Change' }}
                            </span>
                        </td>
                        <td>
                            @if($cr->isTimelineType())
                                <div>
                                    <strong class="text-info">+{{ $cr->timeline_extension_days }} days</strong>
                                    @if($cr->proposed_end_date)
                                        <br><small class="text-muted">New deadline: {{ $cr->proposed_end_date->format('d M Y') }}</small>
                                    @endif
                                </div>
                            @else
                                <div>
                                    @if(!empty($cr->members_to_add))
                                        <span class="badge bg-label-success">+{{ count($cr->members_to_add) }} members</span>
                                    @endif
                                    @if(!empty($cr->members_to_remove))
                                        <span class="badge bg-label-danger">-{{ count($cr->members_to_remove) }} members</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $cr->status_badge }}">
                                {{ ucfirst($cr->status) }}
                            </span>
                            @if($cr->approved_at)
                                <br><small class="text-muted">{{ $cr->approved_at->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ strtoupper(substr($cr->requester->name, 0, 1)) }}
                                    </span>
                                </div>
                                <small>{{ $cr->requester->name }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-label-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewCR('{{ $cr->change_request_id }}')">
                                        <i class="ti ti-eye me-1"></i>View Details
                                    </a></li>
                                    
                                    @if($cr->isPending())
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('risk.change-requests.approve', $cr->change_request_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success" onclick="return confirm('Approve this change request?')">
                                                    <i class="ti ti-check me-1"></i>Approve
                                                </button>
                                            </form>
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="rejectCR('{{ $cr->change_request_id }}')">
                                            <i class="ti ti-x me-1"></i>Reject
                                        </a></li>
                                    @endif
                                    
                                    @if($cr->isApproved())
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('risk.change-requests.implement', $cr->change_request_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-info fw-bold" onclick="return confirm('Implement this change request? This will update the workspace.')">
                                                    <i class="ti ti-rocket me-1"></i>Implement (Auto-Update)
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('risk.change-requests.destroy', $cr->change_request_id) }}" method="POST" onsubmit="return confirm('Delete this change request?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ti ti-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="ti ti-folder-off ti-lg mb-2"></i>
                            <p class="mb-0">No change requests found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Reject CR Modal --}}
<div class="modal fade" id="rejectCRModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="rejectCRForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Reject Change Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    You are about to reject this change request. Please provide a reason.
                </div>
                <div class="mb-3">
                    <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Explain why this change request is rejected..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">
                    <i class="ti ti-x me-1"></i>Reject Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- View CR Details Modal --}}
<div class="modal fade" id="viewCRModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="crDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function rejectCR(crId) {
    document.getElementById('rejectCRForm').action = '/risk/change-requests/' + crId + '/reject';
    var modal = new bootstrap.Modal(document.getElementById('rejectCRModal'));
    modal.show();
}

function viewCR(crId) {
    var modal = new bootstrap.Modal(document.getElementById('viewCRModal'));
    modal.show();
    
    // Load CR details (simplified - you can enhance this with AJAX)
    document.getElementById('crDetailsContent').innerHTML = '<p class="text-center text-muted">Details loading...</p>';
}

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        timer: 3000
    });
@endif
</script>
@endpush
