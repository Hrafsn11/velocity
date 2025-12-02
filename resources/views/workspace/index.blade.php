@extends('layouts.app')

@section('title', 'My Workspaces')

@section('content')
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex flex-column justify-content-center">
            <h4 class="fw-bold mb-0">My Workspaces</h4>
            <p class="text-muted mb-0">Manage your projects and team collaborations</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="input-group input-group-merge" style="width: 250px;">
                <span class="input-group-text" id="workspace-search-addon"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" placeholder="Search workspace..." aria-label="Search..."
                    aria-describedby="workspace-search-addon" />
            </div>
            @unlessrole('Employee')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#workspace-create-modal">
                <i class="ti ti-plus me-1"></i>
                <span class="d-none d-sm-inline-block">New Workspace</span>
            </button>
            @endunlessrole
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse ($workspaces as $workspace)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $workspaceImage = workspace_image_url($workspace->image_path);
                            @endphp
                            <div class="avatar">
                                @if ($workspaceImage)
                                    <img src="{{ $workspaceImage }}" alt="{{ $workspace->title }}"
                                        class="rounded object-fit-cover" style="width: 42px; height: 42px;">
                                @else
                                    <span class="avatar-initial rounded bg-label-primary text-uppercase">
                                        {{ workspace_initials($workspace->title) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $workspace->title }}</h5>
                                <small class="text-muted">
                                    Manager: {{ $workspace->manager->user->name ?? 'Belum ditentukan' }}
                                </small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="workspace-menu-{{ $workspace->workspace_id }}"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti ti-dots-vertical text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="workspace-menu-{{ $workspace->workspace_id }}">
                                @unlessrole('Employee')
                                
                                <button class="dropdown-item" data-bs-toggle="modal"
                                    data-bs-target="#workspace-edit-modal-{{ $workspace->workspace_id }}">
                                    Edit Workspace
                                </button>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('workspaces.destroy', $workspace) }}" method="POST"
                                    onsubmit="return confirm('Arsipkan workspace ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="ti ti-archive me-1"></i>
                                        Archive
                                    </button>
                                </form>
                                @endunlessrole
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="mb-4 text-muted">{{ $workspace->description ?: 'Belum ada deskripsi.' }}</p>

                        <div class="d-flex align-items-center mb-1">
                            <span class="badge {{ $workspace->status->badgeClass() }} me-2 p-1 px-2">
                                {{ $workspace->status->label() }}
                            </span>
                            <small class="ms-auto text-muted">
                                {{ optional($workspace->start_date)->format('d M Y') ?? 'Start TBD' }}
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                            <div class="d-flex align-items-center">
                                <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                    @forelse ($workspace->members->take(4) as $member)
                                        <li class="avatar avatar-xs pull-up" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ $member->user->name ?? 'Member' }}">
                                            <span
                                                class="avatar-initial rounded-circle bg-label-info text-uppercase">{{ workspace_initials($member->user->name ?? 'Member') }}</span>
                                        </li>
                                    @empty
                                        <li class="avatar avatar-xs">
                                            <span class="avatar-initial rounded-circle bg-label-secondary">NA</span>
                                        </li>
                                    @endforelse
                                    @if ($workspace->members->count() > 4)
                                        <li class="avatar avatar-xs">
                                            <span class="avatar-initial rounded-circle">+{{ $workspace->members->count() - 4 }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="text-end">
                                {{-- <small class="text-muted d-block">End</small>
                                <small class="fw-bold">{{ optional($workspace->end_date)->format('d M Y') ?? 'TBD' }}</small> --}}
                                <a class="btn btn-primary btn-sm" href="{{ route('workspaces.show', $workspace) }}">Open Project</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                @role('Employee')
                    <div class="card border border-dashed text-center p-5">
                        <div class="card-body">
                            <h5 class="mb-1">Anda belum memiliki workspace</h5>
                            <p class="text-muted mb-3">Sepertinya Anda belum ditugaskan ke workspace manapun. Mintalah akses kepada manajer atau administrator untuk bergabung dalam project.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">Kembali ke Dashboard</a>
                        </div>
                    </div>
                @else
                    <div class="card border border-dashed text-center p-5">
                        <div class="card-body">
                            <h5 class="mb-1">Belum ada workspace</h5>
                            <p class="text-muted mb-3">Mulai dengan membuat workspace pertama Anda.</p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#workspace-create-modal">
                                Buat Workspace
                            </button>
                        </div>
                    </div>
                @endrole
            </div>
        @endforelse
    </div>

    @unlessrole('Employee')
    @include('workspace.partials._workspace-modal', workspace_modal_context(
        modalId: 'workspace-create-modal',
        title: 'Create Workspace',
        action: route('workspaces.store'),
        method: 'POST',
        workspace: null,
        employees: $employees,
        managers: $managers,
        statuses: $statuses
    ))
    @endunlessrole

    @unlessrole('Employee')
        @foreach ($workspaces as $workspace)
            @include('workspace.partials._workspace-modal', workspace_modal_context(
                modalId: 'workspace-edit-modal-' . $workspace->workspace_id,
                title: 'Edit Workspace',
                action: route('workspaces.update', $workspace),
                method: 'PUT',
                workspace: $workspace,
                employees: $employees,
                managers: $managers,
                statuses: $statuses
            ))
        @endforeach
    @endunlessrole
@endsection

@push('scripts')
    <script>
        (function () {
            const initFlatpickr = () => {
                document.querySelectorAll('.workspace-date-picker').forEach((input) => {
                    if (input._flatpickr) {
                        return;
                    }

                    flatpickr(input, {
                        dateFormat: 'Y-m-d',
                        allowInput: true
                    });
                });
            };

            const initSelect2 = (modal) => {
                const selects = modal.querySelectorAll('.workspace-select');
                selects.forEach((select) => {
                    if ($(select).hasClass('select2-hidden-accessible')) {
                        return;
                    }

                        const formatEmployee = (state) => {
                        if (!state.id) return state.text;
                        const $el = $(state.element);
                        const level = $el.data('level') || '';
                        const role = $el.data('role') || '';
                        const specialization = $el.data('specialization') || '';

                        const levelClass = $el.data('levelClass') || 'bg-label-secondary';
                        const roleClass = $el.data('roleClass') || 'bg-label-primary';
                        const levelBadge = level ? `<span class="badge ${levelClass} me-1" style="font-size:75%">${level}</span>` : '';
                        const roleBadge = role ? `<span class="badge ${roleClass}" style="font-size:75%">${role}</span>` : '';

                        const specHtml = specialization ? `<div class="text-muted" style="font-size:80%">${specialization}</div>` : '';

                        return `<div class="d-flex justify-content-between align-items-start">
                                    <div class="me-2" style="max-width:65%">
                                        <div class="text-truncate">${state.text}</div>
                                        ${specHtml}
                                    </div>
                                    <div class="text-end align-self-start">${levelBadge}${roleBadge}</div>
                                </div>`;
                    };

                    const formatSelection = (state) => {
                        if (!state.id) return state.text;
                        const $el = $(state.element);
                        const level = $el.data('level') || '';
                        const role = $el.data('role') || '';
                        const small = level || role ? ` <small class="text-muted">${level ? level : ''}${level && role ? ' • ' : ''}${role ? role : ''}</small>` : '';
                        return `${state.text}${small}`;
                    };

                    $(select).select2({
                        dropdownParent: $(modal),
                        width: '100%',
                        placeholder: select.dataset.placeholder ?? 'Select option',
                        escapeMarkup: (m) => m,
                        templateResult: formatEmployee,
                        templateSelection: formatSelection,
                    });
                });
            };

            const modals = document.querySelectorAll('.workspace-modal');
            modals.forEach((modal) => {
                modal.addEventListener('shown.bs.modal', () => {
                    initFlatpickr();
                    initSelect2(modal);
                });
            });

            initFlatpickr();
        })();
    </script>
@endpush
