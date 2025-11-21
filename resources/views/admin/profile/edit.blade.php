@extends('layouts.app')

@section('title', 'Profile Settings')

@push('styles')
<style>
    .avatar-upload {
        position: relative;
        max-width: 205px;
        margin: 20px auto;
    }
    .avatar-upload .avatar-preview {
        width: 192px;
        height: 192px;
        position: relative;
        border-radius: 100%;
        border: 6px solid #F8F8F8;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
    }
    .avatar-upload .avatar-preview > div {
        width: 100%;
        height: 100%;
        border-radius: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
</style>
@endpush

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Account Settings /</span> Profile
</h4>

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

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills flex-column flex-md-row mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0);">
                    <i class="ti ti-user-check me-1"></i> Account
                </a>
            </li>
        </ul>

        <!-- Profile Information -->
        <div class="card mb-4">
            <h5 class="card-header">Profile Details</h5>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="name">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <button type="reset" class="btn btn-label-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Avatar -->
        <div class="card mb-4">
            <h5 class="card-header">Profile Picture</h5>
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) . '?t=' . time() : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=100' }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar">
                    <div class="button-wrapper">
                        <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                            @csrf
                            <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="ti ti-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" name="avatar" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg, image/gif" onchange="previewAndSubmit(this)">
                            </label>
                        </form>
                        <div class="text-muted small">Allowed JPG, GIF or PNG. Max size of 2MB</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mb-4">
            <h5 class="card-header">Change Password</h5>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="password">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="password_confirmation">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Change Password</button>
                        <button type="reset" class="btn btn-label-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card">
            <h5 class="card-header">Account Information</h5>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Assigned Roles</label>
                    <div>
                        @foreach($user->roles as $role)
                        <span class="badge bg-label-primary me-2">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Account Status</label>
                    <div>
                        @if($user->email_verified_at)
                        <span class="badge bg-label-success">Verified</span>
                        @else
                        <span class="badge bg-label-warning">Not Verified</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="form-label">Member Since</label>
                    <p class="mb-0">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewAndSubmit(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('uploadedAvatar').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            
            // Submit form after preview
            setTimeout(function() {
                document.getElementById('avatarForm').submit();
            }, 500);
        }
    }
</script>
@endpush
