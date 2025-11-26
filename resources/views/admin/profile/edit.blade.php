@extends('layouts.app')

@section('title', 'Profile Settings')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 2rem;
        color: white;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border: 5px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .info-item {
        padding: 1rem 0;
        border-bottom: 1px solid #e7e7e7;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1rem;
        color: #344767;
        font-weight: 500;
    }
    .stat-card {
        border-left: 3px solid #667eea;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Account /</span> My Profile
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <i class="ti ti-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <!-- Profile Header Card -->
        <div class="card mb-4">
            <div class="profile-header text-center">
                <div class="d-flex justify-content-center mb-3">
                    @php
                        $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=200&background=667eea&color=fff&bold=true';
                        $avatarSrc = $user->avatar_url ?? $defaultAvatar;
                    @endphp
                    <img src="{{ $avatarSrc . '?t=' . time() }}" 
                         alt="user-avatar" 
                         class="rounded-circle profile-avatar" 
                         id="uploadedAvatar">
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="mb-0 opacity-75">{{ $user->email }}</p>
                <div class="mt-3 mb-3">
                    {!! $user->user_type_badge !!}
                    @if($user->account_status === 'active')
                    <span class="badge bg-success ms-2">Active</span>
                    @endif
                </div>
                
                <!-- Upload Avatar -->
                <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm" class="mb-3">
                    @csrf
                    <label for="upload" class="btn btn-primary" tabindex="0">
                        <i class="ti ti-upload me-2"></i>Change Profile Picture
                        <input type="file" id="upload" name="avatar" class="d-none" accept="image/png, image/jpeg, image/jpg" onchange="previewAndSubmit(this)">
                    </label>
                    <small class="text-muted d-block mt-2">JPG or PNG. Max size 2MB</small>
                </form>
            </div>

            <div class="card-body">
                <div class="row">
                    @if($user->isEmployee())
                    <div class="col-md-3 col-6 mb-3">
                        <div class="info-label">Role Category</div>
                        <div class="info-value">
                            {!! $user->employeeProfile->role_icon !!}
                            {{ ucfirst($user->employeeProfile->role) }}
                        </div>
                    </div>

                    <div class="col-md-3 col-6 mb-3">
                        <div class="info-label">Specialization</div>
                        <div class="info-value">{{ $user->employeeProfile->specialization }}</div>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <div class="info-label">Level</div>
                        <div class="info-value">
                            <span class="badge {{ $user->employeeProfile->level_color }}">
                                {{ ucfirst($user->employeeProfile->level) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            {!! $user->employeeProfile->status_badge !!}
                        </div>
                    </div>

                    @if($user->employeeProfile->phone)
                    <div class="col-md-2 col-6 mb-3">
                        <div class="info-label">Phone</div>
                        <div class="info-value">
                            <i class="ti ti-phone me-1"></i>{{ $user->employeeProfile->phone }}
                        </div>
                    </div>
                    @endif

                    <div class="col-12 mb-3">
                        <div class="info-label">Skills</div>
                        <div class="info-value">
                            @foreach($user->employeeProfile->skills as $skill)
                            <span class="badge bg-label-secondary me-1 mb-1">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="col-md-4 col-6 mb-3">
                        <div class="info-label">Email Status</div>
                        <div class="info-value">
                            @if($user->email_verified_at)
                            <span class="badge bg-label-success">
                                <i class="ti ti-circle-check me-1"></i>Verified
                            </span>
                            @else
                            <span class="badge bg-label-warning">
                                <i class="ti ti-alert-circle me-1"></i>Not Verified
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 col-6 mb-3">
                        <div class="info-label">Member Since</div>
                        <div class="info-value">
                            <i class="ti ti-calendar me-1"></i>{{ $user->created_at->format('d M Y') }}
                        </div>
                    </div>

                    <div class="col-md-4 col-6 mb-3">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">
                            @if($user->last_login_at)
                            <i class="ti ti-clock me-1"></i>{{ $user->last_login_at->format('d M Y, H:i') }}
                            @else
                            <span class="text-muted">Never logged in</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Update Profile Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-user-edit me-2"></i>Update Profile Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @else
                            <small class="text-muted">Your email address is your username</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Save Changes
                        </button>
                        <button type="reset" class="btn btn-label-secondary">
                            <i class="ti ti-x me-1"></i>Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-lock me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="ti ti-info-circle me-2 fs-4"></i>
                    <div>
                        <strong>Security Tip:</strong> Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Enter current password" required>
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('current_password')">
                                    <i class="ti ti-eye-off" id="current_password_icon"></i>
                                </span>
                            </div>
                            @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter new password" required>
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
                                    <i class="ti ti-eye-off" id="password_icon"></i>
                                </span>
                            </div>
                            @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @else
                            <small class="text-muted">Minimum 8 characters</small>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('password_confirmation')">
                                    <i class="ti ti-eye-off" id="password_confirmation_icon"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-key me-1"></i>Update Password
                        </button>
                        <button type="reset" class="btn btn-label-secondary">
                            <i class="ti ti-x me-1"></i>Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewAndSubmit(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (file.size > maxSize) {
                alert('File size exceeds 2MB limit');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('uploadedAvatar').src = e.target.result;
            }
            reader.readAsDataURL(file);
            
            // Submit form after preview
            setTimeout(function() {
                document.getElementById('avatarForm').submit();
            }, 500);
        }
    }

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        } else {
            field.type = 'password';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        }
    }
</script>
@endpush
