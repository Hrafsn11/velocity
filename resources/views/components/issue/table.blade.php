@props(['issues'])

<div class="card">
    <div class="card-datatable table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Workspace</th>
                    <th>From Risk</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assignee</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($issues as $issue)
                <tr class="{{ $issue->isOverdue() ? 'table-danger' : '' }}">
                    <td>
                        <strong class="text-warning">#{{ $issue->code }}</strong>
                    </td>
                    <td>
                        <div style="max-width: 250px;">
                            <strong>{{ Str::limit($issue->title, 60) }}</strong>
                            @if($issue->description)
                                <br><small class="text-muted">{{ Str::limit($issue->description, 50) }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-label-primary">
                            {{ strtoupper(substr($issue->workspace->title, 0, 2)) }}
                        </span>
                        <br><small class="text-muted">{{ $issue->workspace->title }}</small>
                    </td>
                    <td>
                        @if($issue->isFromRisk() && $issue->risk && $issue->risk->workspace)
                            <span class="badge bg-label-primary">
                                {{ strtoupper(substr($issue->risk->workspace->title, 0, 2)) }}
                            </span>
                            <strong class="text-primary">#{{ $issue->risk->code }}</strong>
                        @else
                            <span class="badge bg-label-secondary">Direct</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $issue->priority_badge }}">
                            Priority {{ $issue->priority }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $issue->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                        </span>
                    </td>
                    <td>
                        @if($issue->assignee)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ strtoupper(substr($issue->assignee->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <small>{{ $issue->assignee->user->name }}</small>
                            </div>
                        @else
                            <span class="badge bg-label-secondary">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @if($issue->deadline)
                            <small class="{{ $issue->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                <i class="ti ti-calendar me-1"></i>{{ $issue->deadline->format('d M Y') }}
                                @if($issue->isOverdue())
                                    <br><span class="badge bg-danger">OVERDUE</span>
                                @endif
                            </small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="viewIssueDetail('{{ $issue->issue_id }}')">
                                        <i class="ti ti-eye me-1"></i>View Details & Comments
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @if($issue->canStartWorking())
                                <li>
                                    <form action="{{ route('risk.issues.status', $issue->issue_id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="dropdown-item">
                                            <i class="ti ti-player-play me-1"></i>Start Working
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if(!$issue->isResolved())
                                <li>
                                    <a class="dropdown-item text-success" href="javascript:void(0)" onclick="openResolveModal('{{ $issue->issue_id }}', '{{ $issue->code }}')">
                                        <i class="ti ti-check me-1"></i>Resolve Issue
                                    </a>
                                </li>
                                @endif
                                @if($issue->canCreateChangeRequest())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-info" href="javascript:void(0)" onclick="createCR('{{ $issue->issue_id }}', '{{ $issue->code }}')">
                                    <i class="ti ti-arrow-right-circle me-1"></i>Request Timeline Extension
                                </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('risk.issues.destroy', $issue->issue_id) }}" method="POST" onsubmit="return confirm('Delete this issue?')">
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
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="ti ti-bug-off ti-lg mb-2"></i>
                        <p class="mb-0">No issues found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
