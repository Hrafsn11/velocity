@extends('layouts.app')

@section('title', 'Manage Employees')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">HR Management /</span> Manage Employees</h4>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Total Employees</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2" id="totalEmployees">{{ $stats['total'] }}</h4>
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
                        <span>Available</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 text-success" id="availableEmployees">{{ $stats['available'] }}</h4>
                        </div>
                        <p class="mb-0 text-muted">Ready to work</p>
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
                        <span>Unavailable</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 text-secondary" id="unavailableEmployees">{{ $stats['offline'] }}</h4>
                        </div>
                        <p class="mb-0 text-muted">On leave / offline</p>
                    </div>
                    <span class="avatar p-2 rounded bg-label-secondary">
                        <i class="ti ti-user-off ti-sm"></i>
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
                        <span>Projects</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 text-info">N/A</h4>
                        </div>
                        <p class="mb-0 text-muted">Coming soon</p>
                    </div>
                    <span class="avatar p-2 rounded bg-label-info">
                        <i class="ti ti-briefcase ti-sm"></i>
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
            @can('create employees')
            <button class="btn btn-primary" type="button" id="btnAddEmployee">
                <i class="ti ti-plus me-1"></i> Add Employee
            </button>
            @endcan
        </div>
    </div>

    <div class="card-datatable table-responsive">
        <table class="table table-hover border-top" id="employeesTable">
            <thead>
                <tr>
                    <th style="min-width: 250px;">Employee</th>
                    <th style="min-width: 200px;">Role & Specialization</th>
                    <th style="min-width: 200px;">Main Skills</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                @foreach ($employees as $employee)
                <tr data-employee-id="{{ $employee['id'] }}">
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <img src="{{ asset($employee['avatar']) }}" alt="Avatar" class="rounded-circle object-fit-cover">
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $employee['name'] }}</span>
                                <small class="text-muted">{{ $employee['email'] }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar-initial rounded bg-label-secondary me-2 p-1 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                                <i class="ti {{ $employee['role_icon'] }}"></i>
                            </span>
                            <div class="d-flex flex-column">
                                <span class="text-heading fw-medium">{{ $employee['specialization'] }}</span>
                                <small class="{{ $employee['level_color'] }}">{{ ucfirst($employee['level']) }} • {{ ucfirst($employee['role']) }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            @foreach ($employee['skills'] as $skill)
                            <span class="badge rounded-pill bg-label-secondary" style="font-size: 0.7rem;">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $employee['status_badge'] }}">{{ ucfirst($employee['status']) }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @can('edit employees')
                            <a href="javascript:void(0);" class="text-body me-2 btn-edit-employee" data-id="{{ $employee['id'] }}">
                                <i class="ti ti-edit ti-sm"></i>
                            </a>
                            @endcan
                            @can('delete employees')
                            <a href="javascript:void(0);" class="text-body btn-delete-employee" data-id="{{ $employee['id'] }}">
                                <i class="ti ti-trash ti-sm text-danger"></i>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalTitle">Add New Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="employeeForm">
                <input type="hidden" id="employeeId" name="employee_id">
                <input type="hidden" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="employeeName">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="employeeName" name="name" placeholder="John Doe" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeeEmail">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="employeeEmail" name="email" placeholder="john@velocity.com" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeePassword">Password <span class="text-danger" id="passwordRequired">*</span></label>
                            <input type="password" class="form-control" id="employeePassword" name="password" placeholder="Min 8 characters">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeePasswordConfirm">Confirm Password <span class="text-danger" id="passwordConfirmRequired">*</span></label>
                            <input type="password" class="form-control" id="employeePasswordConfirm" name="password_confirmation" placeholder="Re-type password">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeeRole">Role Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="employeeRole" name="role" required>
                                <option value="">Select Role</option>
                                <option value="programmer">Programmer</option>
                                <option value="designer">UI/UX Designer</option>
                                <option value="analyst">System Analyst</option>
                                <option value="qa">QA Engineer</option>
                                <option value="manager">Project Manager</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeeSpecialization">Specialization <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="employeeSpecialization" name="specialization" placeholder="e.g. Frontend Developer" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeeLevel">Seniority Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="employeeLevel" name="level" required>
                                <option value="">Select Level</option>
                                <option value="intern">Intern</option>
                                <option value="junior">Junior</option>
                                <option value="middle">Middle</option>
                                <option value="senior">Senior</option>
                                <option value="lead">Lead</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeeStatus">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="employeeStatus" name="status" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeeSkills">Main Skills <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="employeeSkills" name="skills" placeholder="e.g. Laravel, React, Vue" required>
                            <small class="text-muted">Separate with comma</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employeePhone">Phone Number</label>
                            <input type="text" class="form-control" id="employeePhone" name="phone" placeholder="+62 812-3456-7890">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEmployee">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        <span class="btn-text">Save Employee</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const EmployeeManager = (() => {
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const ENDPOINTS = {
        list: '/employees',
        edit: id => `/employees/${id}/edit`,
        update: id => `/employees/${id}`,
        delete: id => `/employees/${id}`
    };
    
    const SELECTORS = {
        modal: '#employeeModal',
        form: '#employeeForm',
        btnAdd: '#btnAddEmployee',
        btnSubmit: '#btnSubmitEmployee',
        btnEdit: '.btn-edit-employee',
        btnDelete: '.btn-delete-employee',
        formInputs: '.form-control, .form-select',
        invalidFeedback: '.invalid-feedback',
        spinner: '.spinner-border',
        passwordFields: '#employeePassword, #employeePasswordConfirm',
        passwordLabels: '#passwordRequired, #passwordConfirmRequired'
    };

    let modal, $form;

    const ajax = (url, options = {}) => 
        $.ajax({url, ...options, headers: {'X-CSRF-TOKEN': CSRF_TOKEN}});

    const createFormData = (form, method, id) => {
        const formData = new FormData(form);
        formData.append('_token', CSRF_TOKEN);
        if (method === 'PUT') formData.append('_method', 'PUT');
        return formData;
    };

    const showAlert = (icon, text, timer = 2000) => 
        Swal.fire({
            icon, 
            title: icon === 'success' ? 'Success!' : 'Error!', 
            text, 
            timer, 
            showConfirmButton: false
        });

    const reloadPage = (delay = 2000) => setTimeout(() => location.reload(), delay);

    const resetForm = () => {
        $form[0].reset();
        $('#employeeRole, #employeeLevel, #employeeStatus').val('').trigger('change');
        clearValidationErrors();
    };

    const clearValidationErrors = () => {
        $(SELECTORS.formInputs).removeClass('is-invalid');
        $(SELECTORS.invalidFeedback).text('').hide();
    };

    const togglePasswordRequired = required => {
        $(SELECTORS.passwordFields).attr('required', required);
        $(SELECTORS.passwordLabels).toggle(required);
    };

    const toggleButton = loading => 
        $(SELECTORS.btnSubmit).prop('disabled', loading).find(SELECTORS.spinner).toggleClass('d-none', !loading);

    const populateForm = data => 
        Object.entries(data).forEach(([key, val]) => 
            $(`#employee${key.charAt(0).toUpperCase() + key.slice(1)}`).val(val || '').trigger('change')
        );

    const handleValidationErrors = errors => 
        Object.entries(errors).forEach(([field, [msg]]) => 
            $(`[name="${field}"]`).addClass('is-invalid').siblings(SELECTORS.invalidFeedback).text(msg).show()
        );

    const showAddModal = () => {
        resetForm();
        $('#employeeId, #formMethod, #employeeModalTitle').val(['', 'POST', '']).last().text('Add New Employee');
        togglePasswordRequired(true);
        modal.show();
    };

    const showEditModal = async id => {
        resetForm();
        $('#employeeId, #formMethod, #employeeModalTitle').val([id, 'PUT', '']).last().text('Edit Employee');
        togglePasswordRequired(false);

        try {
            const {success, data} = await ajax(ENDPOINTS.edit(id));
            if (success) {
                populateForm(data);
                modal.show();
            }
        } catch {
            showAlert('error', 'Failed to fetch employee data');
        }
    };

    const handleSubmit = async e => {
        e.preventDefault();
        clearValidationErrors();
        toggleButton(true);

        const method = $('#formMethod').val();
        const id = $('#employeeId').val();
        const url = method === 'PUT' ? ENDPOINTS.update(id) : ENDPOINTS.list;

        try {
            const {success, message} = await ajax(url, {
                method: 'POST',
                data: createFormData($form[0], method, id),
                processData: false,
                contentType: false
            });

            if (success) {
                modal.hide();
                showAlert('success', message);
                reloadPage();
            }
        } catch (xhr) {
            if (xhr.status === 422) {
                handleValidationErrors(xhr.responseJSON.errors);
            } else {
                showAlert('error', xhr.responseJSON?.message || 'Something went wrong');
            }
        } finally {
            toggleButton(false);
        }
    };

    const confirmDelete = async $btn => {
        const id = $btn.data('id');
        const name = $btn.closest('tr').find('.fw-semibold').text().trim();

        const {isConfirmed} = await Swal.fire({
            title: 'Are you sure?',
            html: `Delete employee <strong>"${name}"</strong>?<br>This will also delete their user account!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (!isConfirmed) return;

        try {
            const {success, message} = await ajax(ENDPOINTS.delete(id), {
                method: 'POST',
                data: {_token: CSRF_TOKEN, _method: 'DELETE'}
            });

            if (success) {
                showAlert('success', message);
                reloadPage();
            }
        } catch (xhr) {
            showAlert('error', xhr.responseJSON?.message || 'Failed to delete');
        }
    };

    const bindEvents = () => {
        $(SELECTORS.btnAdd).on('click', showAddModal);
        $(document).on('click', SELECTORS.btnEdit, e => showEditModal($(e.currentTarget).data('id')));
        $(document).on('click', SELECTORS.btnDelete, e => confirmDelete($(e.currentTarget)));
        $form.on('submit', handleSubmit);
    };

    return {
        init() {
            modal = new bootstrap.Modal(SELECTORS.modal);
            $form = $(SELECTORS.form);
            bindEvents();
            
            $('#employeeRole, #employeeLevel, #employeeStatus').select2({
                dropdownParent: $('#employeeModal'),
                placeholder: 'Select an option',
                width: '100%'
            });
        }
    };
})();

$(document).ready(() => EmployeeManager.init());
</script>
@endpush
