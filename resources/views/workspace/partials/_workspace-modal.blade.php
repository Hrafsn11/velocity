<div class="modal fade workspace-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="workspace-title-{{ $modalId }}" class="form-label">Judul Project</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                            id="workspace-title-{{ $modalId }}" name="title" placeholder="Contoh: Velocity Commerce"
                            value="{{ old('title', optional($workspace)->title ?? '') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="workspace-description-{{ $modalId }}" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="workspace-description-{{ $modalId }}"
                            name="description" rows="3" placeholder="Ringkasan singkat proyek">{{ old('description', optional($workspace)->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="workspace-start-date-{{ $modalId }}" class="form-label">Start Date</label>
                            <input type="text" class="form-control workspace-date-picker @error('start_date') is-invalid @enderror"
                                id="workspace-start-date-{{ $modalId }}" name="start_date" placeholder="YYYY-MM-DD"
                                value="{{ old('start_date', optional($workspace?->start_date)->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="workspace-end-date-{{ $modalId }}" class="form-label">End Date</label>
                            <input type="text" class="form-control workspace-date-picker @error('end_date') is-invalid @enderror"
                                id="workspace-end-date-{{ $modalId }}" name="end_date" placeholder="YYYY-MM-DD"
                                value="{{ old('end_date', optional($workspace?->end_date)->format('Y-m-d')) }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label for="workspace-manager-{{ $modalId }}" class="form-label">Manager Project</label>
                            <select class="form-select workspace-select @error('manager_id') is-invalid @enderror"
                                id="workspace-manager-{{ $modalId }}" name="manager_id" data-placeholder="Pilih manager" required>
                                <option value="" disabled {{ $managerValue ? '' : 'selected' }}>Pilih manager</option>
                                @foreach (($managers ?? $employees) as $employee)
                                    <option value="{{ $employee->employee_id }}"
                                        data-level="{{ $employee->level ?? '' }}"
                                        data-role="{{ $employee->role ?? '' }}"
                                        data-specialization="{{ $employee->specialization ?? '' }}"
                                        data-level-class="{{ $employee->level_badge ?? ($employee->level ? 'bg-label-secondary' : '') }}"
                                        data-role-class="{{ $employee->role_badge ?? ($employee->role ? 'bg-label-secondary' : '') }}"
                                        {{ $managerValue === $employee->employee_id ? 'selected' : '' }}>
                                        {{ $employee->user->name ?? 'Employee' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="workspace-status-{{ $modalId }}" class="form-label">Status</label>
                            <select class="form-select workspace-select @error('status') is-invalid @enderror"
                                id="workspace-status-{{ $modalId }}" name="status" data-placeholder="Pilih status" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ $statusValue === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="workspace-members-{{ $modalId }}" class="form-label">Member</label>
                        <select class="form-select workspace-select @error('members') is-invalid @enderror" multiple
                            id="workspace-members-{{ $modalId }}" name="members[]" data-placeholder="Tambah anggota">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->employee_id }}"
                                    data-level="{{ $employee->level ?? '' }}"
                                    data-role="{{ $employee->role ?? '' }}"
                                    data-specialization="{{ $employee->specialization ?? '' }}"
                                    data-level-class="{{ $employee->level_badge ?? ($employee->level ? 'bg-label-secondary' : '') }}"
                                    data-role-class="{{ $employee->role_badge ?? ($employee->role ? 'bg-label-secondary' : '') }}"
                                    {{ in_array($employee->employee_id, $memberValues, true) ? 'selected' : '' }}>
                                    {{ $employee->user->name ?? 'Employee' }}
                                </option>
                            @endforeach
                        </select>
                        @error('members')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('members.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label for="workspace-image-{{ $modalId }}" class="form-label">Gambar</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                            id="workspace-image-{{ $modalId }}" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($workspace?->image_path)
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $workspace ? 'Simpan Perubahan' : 'Buat Workspace' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

