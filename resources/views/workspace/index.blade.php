@extends('layouts.app')

@section('title', 'Sprint Board - ' . $sprintInfo['name'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jkanban/jkanban.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-kanban.css') }}" />

    <style>
        /* 1. Rapikan Header Sprint */
        .sprint-header {
            background: #fff;
            border-left: 5px solid var(--bs-primary);
            /* Aksen warna di kiri */
        }

        .sprint-meta-item {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: #6f6b7d;
            background: #f8f7fa;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
        }

        /* 2. Rapikan Filter Bar */
        .filter-bar {
            background: transparent;
            box-shadow: none;
            border-bottom: 1px solid #dbdade;
            border-radius: 0;
            padding-left: 0;
            padding-right: 0;
        }

        /* 3. Kanban Column Styling */
        .kanban-board-header {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            border-radius: 8px 8px 0 0;
        }

        .kanban-title-board {
            font-weight: 600;
            font-size: 1rem;
        }
        /* --- Toolbar improvements --- */
        .board-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e9e9ef;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1 1 auto;
            min-width: 0; /* allow children to shrink */
        }

        .search-input { max-width: 360px; width: 100%; }

        .filter-group { display: flex; align-items: center; gap: 0.6rem; }

        .avatar-group { display: flex; align-items: center; gap: 0.35rem; }
        .avatar-group .avatar { border: 2px solid #fff; box-shadow: 0 1px 2px rgba(16,24,40,0.04); }

        .action-group { display: flex; align-items: center; gap: 0.5rem; }

        .only-my-issues { padding: .35rem .6rem; border-radius: 8px; }

        @media (max-width: 576px) {
            .board-toolbar { gap: 0.5rem; }
            .search-input { max-width: 100%; }
            .toolbar-left { width: 100%; }
            .action-group { width: 100%; justify-content: flex-start; }
        }
    </style>
@endpush

@section('content')

    <div class="card mb-4 border-0 shadow-sm sprint-header">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4">

                <div class="d-flex gap-3 align-items-start">
                    <div
                        class="avatar avatar-lg rounded bg-label-primary flex-shrink-0 d-flex align-items-center justify-content-center">
                        <i class="ti ti-rocket ti-md"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h4 class="fw-bold mb-0 text-heading">{{ $sprintInfo['name'] }}</h4>
                            <span class="badge bg-label-success">Active</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <div class="sprint-meta-item">
                                <i class="ti ti-calendar me-1"></i> {{ $sprintInfo['start_date'] }} -
                                {{ $sprintInfo['end_date'] }}
                            </div>
                            <div class="sprint-meta-item text-primary fw-medium">
                                <i class="ti ti-clock me-1"></i> {{ $sprintInfo['days_left'] }} Days Remaining
                            </div>
                        </div>

                        <div class="text-muted small">
                            <i class="ti ti-target me-1"></i> <strong>Goal:</strong> {{ $sprintInfo['goal'] }}
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column align-items-end gap-3 w-100 w-lg-auto" style="min-width: 300px;">
                    <div class="w-100 bg-lighter rounded p-3 border border-dashed">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-uppercase small fw-bold text-muted">Sprint Progress</span>
                            <span class="fw-bold text-heading">{{ $sprintInfo['completed_points'] }} /
                                {{ $sprintInfo['total_points'] }} pts</span>
                        </div>
                        @php $percent = ($sprintInfo['completed_points'] / $sprintInfo['total_points']) * 100; @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 w-100">
                        <a href="#" class="btn btn-label-secondary flex-grow-1">
                            <i class="ti ti-chart-bar me-1"></i> Reports
                        </a>
                        <button class="btn btn-primary flex-grow-1">
                            <i class="ti ti-check me-1"></i> Complete Sprint
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="board-toolbar">
        <div class="toolbar-left">
            <div class="search-input input-group input-group-merge bg-white shadow-sm" style="border-radius: 6px;">
                <span class="input-group-text border-0 ps-3"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control border-0" placeholder="Search task..." id="kanban-search" aria-label="Search tasks">
            </div>

            <div class="vr d-none d-md-block" style="height: 30px;"></div>

            <div class="filter-group ms-0 ms-md-2">
                <span class="text-muted small text-uppercase fw-bold me-2 d-none d-sm-inline">Filter:</span>
                <div class="avatar-group" role="list">
                    <div class="avatar avatar-sm pull-up cursor-pointer" title="Jordan" data-bs-toggle="tooltip">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                    </div>
                    <div class="avatar avatar-sm pull-up cursor-pointer" title="Viola" data-bs-toggle="tooltip">
                        <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle">
                    </div>
                    <div class="avatar avatar-sm pull-up cursor-pointer" title="Unassigned" data-bs-toggle="tooltip">
                        <span class="avatar-initial rounded-circle bg-label-secondary"><i class="ti ti-help"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-group">
            <label class="only-my-issues btn btn-white shadow-sm btn-sm text-nowrap border-0 mb-0">
                <input class="form-check-input me-1" type="checkbox"> Only My Issues
            </label>
            <button class="btn btn-icon btn-label-primary rounded-pill" title="Refresh Board">
                <i class="ti ti-refresh"></i>
            </button>
        </div>
    </div>

    <div class="app-kanban">
        <div class="kanban-wrapper"></div>
    </div>

    <div class="offcanvas offcanvas-end kanban-update-item-sidebar" tabindex="-1" id="kanban-update-item-sidebar">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Task Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body pt-0">
            <div class="nav-align-top">
                <ul class="nav nav-tabs mb-4 rounded-0 border-bottom">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-update">
                            <i class="ti ti-edit me-1"></i> Edit
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
                            <i class="ti ti-list-details me-1"></i> Activity
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                    <form>
                        <div class="mb-3">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" id="title" class="form-control fw-bold"
                                placeholder="Task Title" />
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label" for="label">Label</label>
                                <select class="select2 select2-label form-select" id="label">
                                    <option data-color="bg-label-success" value="DevOps">DevOps</option>
                                    <option data-color="bg-label-warning" value="Frontend">Frontend</option>
                                    <option data-color="bg-label-info" value="Backend">Backend</option>
                                    <option data-color="bg-label-danger" value="Bug">Bug</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="due-date">Due Date</label>
                                <input type="text" id="due-date" class="form-control flatpickr-date"
                                    placeholder="YYYY-MM-DD" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assigned To</label>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="avatar avatar-xs" title="Jordan" data-bs-toggle="tooltip">
                                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar"
                                        class="rounded-circle">
                                </div>
                                <a href="javascript:void(0);"
                                    class="btn btn-icon btn-sm btn-label-secondary rounded-circle">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <div id="editor-container" class="comment-editor border-bottom-0" style="min-height: 100px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-label-danger"
                                data-bs-dismiss="offcanvas">Delete</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="offcanvas">Update
                                Task</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="tab-activity" role="tabpanel">
                    <div class="timeline timeline-dashed">
                        <div class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Moved to In Progress</h6>
                                    <small class="text-muted">Today 10am</small>
                                </div>
                                <p class="mb-0">By <strong>Viola Amherd</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/jkanban/jkanban.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const boards = @json($boards);

            const kanban = new jKanban({
                element: '.kanban-wrapper',
                gutter: '15px',
                widthBoard: '280px',
                dragItems: true,
                boards: boards,
                dragBoards: true,
                addItemButton: true, // Tombol + Add Item di bawah
                buttonContent: '+ Add Item', // Teks tombol

                // Config agar tombol Add Item berfungsi dan terlihat bagus
                itemAddOptions: {
                    enabled: true,
                    content: '+ Add New Task',
                    class: 'kanban-title-button btn btn-default btn-xs shadow-none text-muted',
                    footer: true // Letakkan di footer kolom
                },

                click: function(el) {
                    // Buka Offcanvas Edit Task
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById(
                        'kanban-update-item-sidebar'));
                    offcanvas.show();
                    // Populate Title (Demo)
                    const title = el.getAttribute('data-eid') ? el.querySelector('.kanban-text')
                        .textContent : el.textContent;
                    document.getElementById('title').value = title;
                },

                // Render Kartu Kustom
                itemBuilder: function(title, item) {
                    let badgeClass = 'bg-label-' + (item.badge_color || 'primary');
                    let badge = item.badge ?
                        `<span class="badge ${badgeClass} mb-2">${item.badge}</span>` : '';
                    let imgHtml = item.image ?
                        `<img src="${item.image}" class="img-fluid rounded mb-2 d-block w-100" alt="Task Image">` :
                        '';

                    let footer = `
                        <div class="d-flex justify-content-between align-items-center flex-wrap mt-2">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-paperclip me-1 text-muted fs-6"></i> 
                                <span class="text-muted fs-tiny me-3">${item.attachments || 0}</span>
                                <i class="ti ti-message-dots me-1 text-muted fs-6"></i> 
                                <span class="text-muted fs-tiny">${item.comments || 0}</span>
                            </div>
                            <div class="avatar-group">
                                ${item.assignees ? item.assignees.map(img => `
                                        <div class="avatar avatar-xs pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="User">
                                            <img src="assets/img/avatars/${img}" alt="Avatar" class="rounded-circle">
                                        </div>
                                    `).join('') : ''}
                            </div>
                        </div>
                    `;

                    return `
                        <div class="kanban-item shadow-sm border-0" data-eid="${item.id}">
                            ${imgHtml}
                            ${badge}
                            <span class="kanban-text fw-medium d-block text-heading">${item.title}</span>
                            ${footer}
                        </div>
                    `;
                }
            });

            // Init Quill
            new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'link']
                    ]
                }
            });

            // Init Tooltip & Datepicker
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d"
            });
            $('.select2').select2();

            // Re-init bootstrap tooltip for dynamic elements
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
