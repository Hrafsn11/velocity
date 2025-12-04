@props(['risks'])

<div class="card">
    <div class="card-datatable table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Workspace</th>
                    <th>Category</th>
                    <th>Score</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($risks as $risk)
                <tr>
                    <td>
                        <strong class="text-primary">#{{ $risk->code }}</strong>
                    </td>
                    <td>
                        <div style="max-width: 300px;">
                            <strong>{{ Str::limit($risk->description, 60) }}</strong>
                            @if($risk->cause)
                                <br><small class="text-muted"><i class="ti ti-alert-circle me-1"></i>{{ Str::limit($risk->cause, 50) }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-label-primary">
                            {{ strtoupper(substr($risk->workspace->title, 0, 2)) }}
                        </span>
                        <br><small class="text-muted">{{ $risk->workspace->title }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $risk->category_badge }}">{{ $risk->category }}</span>
                    </td>
                    <td>
                        <div class="text-center">
                            <strong>{{ $risk->score }}</strong>
                            <br><small class="text-muted">{{ $risk->probability }}x{{ $risk->impact }}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $risk->urgency_badge }}">{{ $risk->urgency }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $risk->status_badge }}">{{ ucfirst($risk->status) }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewRisk({{ json_encode($risk) }})">
                                    <i class="ti ti-eye me-1"></i>View Details
                                </a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="editRisk({{ json_encode($risk) }})">
                                    <i class="ti ti-edit me-1"></i>Edit
                                </a></li>
                                @if($risk->status == 'active' && $risk->mitigation_actions)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('risk.mark-mitigated', $risk->risk_id) }}" method="POST" class="mark-mitigated-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="ti ti-shield-check me-1"></i>Mark as Mitigated
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if($risk->canConvertToIssue())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-warning" href="javascript:void(0)" onclick="convertToIssue('{{ $risk->risk_id }}', '{{ $risk->code }}', '{{ $risk->workspace_id }}')">
                                    <i class="ti ti-arrow-right me-1"></i>Convert to Issue
                                </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('risk.destroy', $risk->risk_id) }}" method="POST" class="delete-risk-form">
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
                        <i class="ti ti-alert-circle ti-lg mb-2"></i>
                        <p class="mb-0">No risks found. Click "Add New Risk" to create one.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
