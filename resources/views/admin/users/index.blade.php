@extends('layouts.app')

@section('title', 'Users Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">User Management /</span> System Access Control</h4>
        <p class="text-muted mt-1">Manage user accounts, roles, permissions and access control</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Total Users</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2">{{ $stats['total'] }}</h4>
                        </div>
                        <p class="mb-0 text-muted">All system users</p>
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
                        <span>Active</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 text-success">{{ $stats['active'] }}</h4>
                        </div>
                        <p class="mb-0 text-muted">Can access system</p>
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
                        <span>Suspended</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 text-danger">{{ $stats['suspended'] }}</h4>
                        </div>
                        <p class="mb-0 text-muted">Access blocked</p>
                    </div>
                    <span class="avatar p-2 rounded bg-label-danger">
                        <i class="ti ti-user-x ti-sm"></i>
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
                        <span>Inactive</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 text-secondary">{{ $stats['inactive'] }}</h4>
                        </div>
                        <p class="mb-0 text-muted">Not yet activated</p>
                    </div>
                    <span class="avatar p-2 rounded bg-label-secondary">
                        <i class="ti ti-user-off ti-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">User Accounts & Access Control</h5>
        <div class="d-flex align-items-center gap-2">
            @can('create users')
            <button class="btn btn-primary" type="button" id="btnAddUser">
                <i class="ti ti-plus me-1"></i> Add User
            </button>
            @endcan
        </div>
    </div>
    <div class="card-datatable table-responsive">
        <table class="table table-hover border-top">
            <thead>
                <tr>
                    <th style="min-width: 250px;">User</th>
                    <th>Email</th>
                    <th>Roles & Permissions</th>
                    <th>Account Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle">
                                @else
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ collect(explode(' ', $user->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                                </span>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $user->name }}</h6>
                                <small class="text-muted">
                                    <i class="ti ti-login ti-xs me-1"></i>{{ $user->login_count ?? 0 }} logins
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-heading">{{ $user->email }}</span>
                        @if($user->email_verified_at)
                        <br><small class="text-success"><i class="ti ti-check ti-xs"></i> Verified</small>
                        @else
                        <br><small class="text-warning"><i class="ti ti-alert-circle ti-xs"></i> Not verified</small>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($user->roles as $role)
                            <span class="badge bg-label-info">{{ $role->name }}</span>
                            @empty
                            <span class="badge bg-label-secondary">No Role</span>
                            @endforelse
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ $user->roles->sum(fn($role) => $role->permissions->count()) }} permissions
                        </small>
                    </td>
                    <td>
                        <span class="badge {{ $user->status_badge }}">
                            {{ ucfirst($user->account_status ?? 'active') }}
                        </span>
                        @if($user->isSuspended() && $user->suspended_reason)
                        <br><small class="text-danger" title="{{ $user->suspended_reason }}">
                            <i class="ti ti-info-circle ti-xs"></i> See reason
                        </small>
                        @endif
                    </td>
                    <td>
                        @if($user->last_login_at)
                        <span class="text-heading">{{ $user->last_login_at->diffForHumans() }}</span>
                        <br><small class="text-muted">{{ $user->last_login_at->format('d M Y, H:i') }}</small>
                        @else
                        <span class="text-muted">Never logged in</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                @can('edit users')
                                <a class="dropdown-item btn-edit-user" href="javascript:void(0);" data-id="{{ $user->user_id }}">
                                    <i class="ti ti-pencil me-1"></i> Edit
                                </a>
                                
                                @if($user->user_id !== auth()->id())
                                    <a class="dropdown-item text-info" href="javascript:void(0);" onclick="resetUserPassword('{{ $user->user_id }}', '{{ $user->name }}')">
                                        <i class="ti ti-key me-1"></i> Reset Password
                                    </a>
                                    
                                    @if($user->isSuspended())
                                    <a class="dropdown-item text-success" href="javascript:void(0);" onclick="activateUser('{{ $user->user_id }}', '{{ $user->name }}')">
                                        <i class="ti ti-user-check me-1"></i> Activate Account
                                    </a>
                                    @else
                                    <a class="dropdown-item text-warning" href="javascript:void(0);" onclick="suspendUser('{{ $user->user_id }}', '{{ $user->name }}')">
                                        <i class="ti ti-user-x me-1"></i> Suspend Account
                                    </a>
                                    @endif
                                @endif
                                @endcan
                                
                                @can('delete users')
                                @if($user->user_id !== auth()->id())
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="ti ti-trash me-1"></i> Delete
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Suspend User Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suspend User Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="suspendForm">
                <input type="hidden" id="suspendUserId">
                <div class="modal-body">
                    <p>You are about to suspend: <strong id="suspendUserName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label" for="suspendReason">Reason for suspension <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="suspendReason" name="reason" rows="3" required placeholder="Enter the reason why this account is being suspended..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <i class="ti ti-alert-triangle me-2"></i>
                        This user will not be able to access the system until the account is reactivated.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-user-x me-1"></i> Suspend Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <input type="hidden" id="userId" name="user_id">
                <input type="hidden" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="userName">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userName" name="name" placeholder="John Doe" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="userEmail">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="userEmail" name="email" placeholder="john@velocity.com" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12" id="passwordRow">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="userPassword">Password <span class="text-danger" id="passwordRequired">*</span></label>
                                    <input type="password" class="form-control" id="userPassword" name="password" placeholder="Min 8 characters">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="userPasswordConfirm">Confirm Password <span class="text-danger" id="passwordConfirmRequired">*</span></label>
                                    <input type="password" class="form-control" id="userPasswordConfirm" name="password_confirmation" placeholder="Re-type password">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Roles <span class="text-muted">(optional)</span></label>
                            <div class="row" id="rolesContainer">
                                @foreach($roles as $role)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input role-checkbox" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role{{ $role->id }}">
                                        <label class="form-check-label" for="role{{ $role->id }}">
                                            <span class="badge bg-label-info">{{ $role->name }}</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitUser">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        <span class="btn-text">Save User</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const UserManager = (() => {
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const ENDPOINTS = {
        list: '/users',
        edit: id => `/users/${id}/edit`,
        update: id => `/users/${id}`,
        delete: id => `/users/${id}`,
        suspend: id => `/users/${id}/suspend`,
        activate: id => `/users/${id}/activate`,
        resetPassword: id => `/users/${id}/reset-password`
    };
    
    const SELECTORS = {
        modal: '#userModal',
        suspendModal: '#suspendModal',
        form: '#userForm',
        suspendForm: '#suspendForm',
        btnAdd: '#btnAddUser',
        btnSubmit: '#btnSubmitUser',
        btnEdit: '.btn-edit-user',
        btnDelete: '.btn-delete-user',
        formInputs: '.form-control, .form-select',
        invalidFeedback: '.invalid-feedback',
        spinner: '.spinner-border',
        passwordFields: '#userPassword, #userPasswordConfirm',
        passwordRow: '#passwordRow',
        passwordLabels: '#passwordRequired, #passwordConfirmRequired',
        roleCheckbox: '.role-checkbox'
    };

    let modal, suspendModal, $form, $suspendForm;

    const ajax = (url, options = {}) => 
        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });

    const clearErrors = () => {
        document.querySelectorAll(SELECTORS.formInputs).forEach(input => {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback?.classList.contains('invalid-feedback')) {
                feedback.textContent = '';
            }
        });
    };

    const showErrors = (errors) => {
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback?.classList.contains('invalid-feedback')) {
                    feedback.textContent = errors[field][0];
                }
            }
        });
    };

    const resetForm = () => {
        $form.reset();
        clearErrors();
        document.querySelectorAll(SELECTORS.roleCheckbox).forEach(cb => cb.checked = false);
    };

    const openAddModal = () => {
        resetForm();
        document.getElementById('userModalTitle').textContent = 'Add New User';
        document.getElementById('userId').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.querySelector(SELECTORS.btnSubmit + ' .btn-text').textContent = 'Save User';
        
        // Show password row for new user
        const passwordRow = document.querySelector(SELECTORS.passwordRow);
        if (passwordRow) passwordRow.classList.remove('d-none');
        
        // Password required for new user
        document.querySelectorAll(SELECTORS.passwordFields).forEach(input => {
            input.required = true;
        });
        document.querySelectorAll(SELECTORS.passwordLabels).forEach(label => {
            label.classList.remove('d-none');
        });
        
        modal.show();
    };

    const openEditModal = async (userId) => {
        try {
            const response = await ajax(ENDPOINTS.edit(userId));
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            const user = result.data;
            
            resetForm();
            document.getElementById('userModalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = user.user_id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            document.querySelector(SELECTORS.btnSubmit + ' .btn-text').textContent = 'Update User';
            
            // Hide password row for edit (use reset password instead)
            const passwordRow = document.querySelector(SELECTORS.passwordRow);
            if (passwordRow) passwordRow.classList.add('d-none');
            
            // Password not editable
            document.querySelectorAll(SELECTORS.passwordFields).forEach(input => {
                input.required = false;
                input.value = '';
            });
            document.querySelectorAll(SELECTORS.passwordLabels).forEach(label => {
                label.classList.add('d-none');
            });
            
            // Check assigned roles
            if (user.roles && user.roles.length > 0) {
                user.roles.forEach(roleName => {
                    const checkbox = document.querySelector(`[name="roles[]"][value="${roleName}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            modal.show();
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Failed to load user data',
            });
        }
    };

    const submitForm = async (e) => {
        e.preventDefault();
        
        const formData = new FormData($form);
        const data = {};
        
        // Convert FormData to object
        formData.forEach((value, key) => {
            if (key === 'roles[]') {
                if (!data.roles) data.roles = [];
                data.roles.push(value);
            } else {
                data[key] = value;
            }
        });
        
        const userId = document.getElementById('userId').value;
        const method = document.getElementById('formMethod').value;
        const url = method === 'POST' ? ENDPOINTS.list : ENDPOINTS.update(userId);
        
        // Show loading
        const btnSubmit = document.querySelector(SELECTORS.btnSubmit);
        const spinner = btnSubmit.querySelector(SELECTORS.spinner);
        const btnText = btnSubmit.querySelector('.btn-text');
        
        btnSubmit.disabled = true;
        spinner.classList.remove('d-none');
        clearErrors();
        
        try {
            const response = await ajax(url, {
                method: method === 'PUT' ? 'PUT' : 'POST',
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (!result.success) {
                if (result.errors) {
                    showErrors(result.errors);
                    throw new Error('Please check the form for errors');
                }
                throw new Error(result.message);
            }
            
            modal.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
            
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'An error occurred',
            });
        } finally {
            btnSubmit.disabled = false;
            spinner.classList.add('d-none');
        }
    };

    const init = () => {
        modal = new bootstrap.Modal(document.querySelector(SELECTORS.modal));
        suspendModal = new bootstrap.Modal(document.querySelector(SELECTORS.suspendModal));
        $form = document.querySelector(SELECTORS.form);
        $suspendForm = document.querySelector(SELECTORS.suspendForm);
        
        // Add user button
        document.querySelector(SELECTORS.btnAdd)?.addEventListener('click', openAddModal);
        
        // Edit user buttons
        document.querySelectorAll(SELECTORS.btnEdit).forEach(btn => {
            btn.addEventListener('click', (e) => {
                const userId = e.currentTarget.dataset.id;
                openEditModal(userId);
            });
        });
        
        // Form submit
        $form.addEventListener('submit', submitForm);
        
        // Suspend form submission (existing code)
        $suspendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const userId = document.getElementById('suspendUserId').value;
            const reason = document.getElementById('suspendReason').value;
            
            fetch(`/users/${userId}/suspend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({ reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    suspendModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to suspend user',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while suspending the user',
                });
            });
        });
    };

    return { init };
})();

document.addEventListener('DOMContentLoaded', UserManager.init);

function suspendUser(userId, userName) {
    document.getElementById('suspendUserId').value = userId;
    document.getElementById('suspendUserName').textContent = userName;
    document.getElementById('suspendReason').value = '';
    const suspendModal = new bootstrap.Modal(document.getElementById('suspendModal'));
    suspendModal.show();
}

function activateUser(userId, userName) {
    Swal.fire({
        title: 'Activate Account?',
        text: `Activate account for ${userName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Activate',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${userId}/activate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Activated!',
                        text: data.message,
                        timer: 2000,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to activate user',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while activating the user',
                });
            });
        }
    });
}

function resetUserPassword(userId, userName) {
    Swal.fire({
        title: 'Reset Password?',
        html: `Reset password for <strong>${userName}</strong>?<br><small class="text-muted">A temporary password will be sent to their email</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Reset Password',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ff9800',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Reset!',
                        html: `
                            <p>${data.message}</p>
                            <div class="alert alert-info mt-3">
                                <i class="ti ti-mail me-2"></i>
                                <strong>Email Sent</strong>
                                <p class="mb-0 mt-2">A temporary password has been sent to the user's email address.</p>
                                <small class="text-muted">For security reasons, the password is not displayed here.</small>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to reset password',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while resetting the password',
                });
            });
        }
    });
}
</script>
@endpush
@endsection
