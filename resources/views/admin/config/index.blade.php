@extends('layouts.admin')

@section('title', 'App Configuration')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Configuration /</span> App Configuration</h4>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.config.store') }}" method="POST" enctype="multipart/form-data" id="formConfig">
    @csrf
    <div class="row g-4 mb-4">
        <!-- App Logo -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <img src="{{ asset(app_config('app_logo')) }}" alt="App Logo" class="img-fluid mb-3" id="appLogo" style="max-width:150px; max-height:150px;">
                    <h5>App Logo</h5>
                    <p class="text-muted">The logo of the application</p>
                    <div class="button-wrapper">
                        <label for="upload-app-logo" class="btn btn-primary me-2 mb-3" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="ti ti-upload d-block d-sm-none"></i>
                            <input type="file" id="upload-app-logo" class="app-logo-upload" hidden name="app_logo" accept="image/png, image/jpeg, image/svg+xml" />
                        </label>
                        <button type="button" class="btn btn-label-secondary app-logo-reset mb-3">
                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                        </button>
                        <div class="small text-muted">Allowed JPG, SVG or PNG. Max size of 2MB</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Logo -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <img src="{{ asset(app_config('sidebar_logo')) }}" alt="Sidebar Logo" class="img-fluid mb-3" id="sidebarLogo" style="max-width:150px; max-height:150px;">
                    <h5>Sidebar Logo</h5>
                    <p class="text-muted">The logo that appears on the sidebar</p>
                    <div class="button-wrapper">
                        <label for="upload-sidebar-logo" class="btn btn-primary me-2 mb-3" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="ti ti-upload d-block d-sm-none"></i>
                            <input type="file" id="upload-sidebar-logo" class="sidebar-logo-upload" hidden name="sidebar_logo" accept="image/png, image/jpeg, image/svg+xml" />
                        </label>
                        <button type="button" class="btn btn-label-secondary sidebar-logo-reset mb-3">
                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                        </button>
                        <div class="small text-muted">Allowed JPG, SVG or PNG. Max size of 2MB</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- App Name -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <input type="text" class="form-control mb-3" value="{{ app_config('app_name') }}" name="app_name" required>
                    <h5>App Name</h5>
                    <p class="text-muted">The name of the application</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Name -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <input type="text" class="form-control mb-3" value="{{ app_config('sidebar_name') }}" name="sidebar_name" required>
                    <h5>Sidebar Name</h5>
                    <p class="text-muted">The title that appears on the sidebar</p>
                </div>
            </div>
        </div>

        <!-- Primary Color -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <input class="form-control mb-3" type="color" value="{{ app_config('primary_hex') }}" name="primary_hex" id="primaryColorInput" required>
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary me-2">Primary</button>
                        <button type="button" class="btn btn-outline-primary me-2">Outline</button>
                        <span class="badge bg-label-primary">Label</span>
                    </div>
                    <h5>App Color: <span class="badge bg-primary" id="colorBadge">{{ app_config('primary_hex') }}</span></h5>
                    <p class="text-muted">The primary color of the application</p>
                </div>
            </div>
        </div>

        <!-- App Home -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="form-check form-check-inline mt-4">
                        <input class="form-check-input" type="radio" name="app_home" id="radioDashboard" value="Dashboard" {{ app_config('app_home') == 'Dashboard' ? 'checked' : '' }} />
                        <label class="form-check-label" for="radioDashboard">Dashboard</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="app_home" id="radioLanding" value="Landing Page" {{ app_config('app_home') == 'Landing Page' ? 'checked' : '' }} />
                        <label class="form-check-label" for="radioLanding">Landing Page</label>
                    </div>
                    <h5 class="mt-3">App Home</h5>
                    <p class="text-muted">Whether the home page goes to dashboard or landing page</p>
                </div>
            </div>
        </div>

        <!-- Login Background -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <img src="{{ asset(app_config('login_bg')) }}" alt="Login Background" class="img-fluid mb-3" id="loginBg" style="max-width:150px; max-height:150px;">
                    <h5>Login Background</h5>
                    
                    <div class="row g-3 mb-3 justify-content-center">
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="login_bg_style" value="height: auto; width: 100%;" id="fitImg" {{ app_config('login_bg_style') == 'height: auto; width: 100%;' ? 'checked' : '' }} />
                                <label class="form-check-label" for="fitImg">Fit</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="login_bg_style" value="object-fit: fill; height: 100vh; z-index: -1" id="fillImg" {{ app_config('login_bg_style') == 'object-fit: fill; height: 100vh; z-index: -1' ? 'checked' : '' }} />
                                <label class="form-check-label" for="fillImg">Fill</label>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted">The background image of the login page</p>
                    <div class="button-wrapper">
                        <label for="upload-login-bg" class="btn btn-primary me-2 mb-3" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="ti ti-upload d-block d-sm-none"></i>
                            <input type="file" id="upload-login-bg" class="login-bg-upload" hidden name="login_bg" accept="image/png, image/jpeg" />
                        </label>
                        <button type="button" class="btn btn-label-secondary login-bg-reset mb-3">
                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                        </button>
                        <div class="small text-muted">Allowed JPG or PNG. Max size of 2MB</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Show Dummy Masters -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="form-check form-check-inline mt-4">
                        <input class="form-check-input" type="radio" name="show_dummy" id="radioTrue" value="true" {{ app_config('show_dummy') == 'true' ? 'checked' : '' }} />
                        <label class="form-check-label" for="radioTrue">Yes, Show it!</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="show_dummy" id="radioFalse" value="false" {{ app_config('show_dummy') == 'false' ? 'checked' : '' }} />
                        <label class="form-check-label" for="radioFalse">No, Hide it!</label>
                    </div>
                    <h5 class="mt-3">Show Dummy Masters</h5>
                    <p class="text-muted">Whether to show dummy masters or not. <b>Super Admin is not affected by this setting.</b></p>
                </div>
                <hr>
                <div class="card-body d-flex justify-content-center">
                    <button type="button" class="btn btn-danger" id="btnReset">
                        Reset to Default Template Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary me-2">Save Changes</button>
        <button type="button" class="btn btn-label-secondary" onclick="location.reload()">Cancel</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // App Logo Preview
    const appLogo = $('#appLogo');
    const appLogoInput = $('.app-logo-upload');
    const appLogoReset = $('.app-logo-reset');
    const appLogoOriginal = appLogo.attr('src');

    appLogoInput.on('change', function() {
        if (this.files && this.files[0]) {
            if (this.files[0].size > 2048 * 1024) {
                alert('File size is too large. Max size is 2MB.');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                appLogo.attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    appLogoReset.on('click', function() {
        appLogoInput.val('');
        appLogo.attr('src', appLogoOriginal);
    });

    // Sidebar Logo Preview
    const sidebarLogo = $('#sidebarLogo');
    const sidebarLogoInput = $('.sidebar-logo-upload');
    const sidebarLogoReset = $('.sidebar-logo-reset');
    const sidebarLogoOriginal = sidebarLogo.attr('src');

    sidebarLogoInput.on('change', function() {
        if (this.files && this.files[0]) {
            if (this.files[0].size > 2048 * 1024) {
                alert('File size is too large. Max size is 2MB.');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                sidebarLogo.attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    sidebarLogoReset.on('click', function() {
        sidebarLogoInput.val('');
        sidebarLogo.attr('src', sidebarLogoOriginal);
    });

    // Login BG Preview
    const loginBg = $('#loginBg');
    const loginBgInput = $('.login-bg-upload');
    const loginBgReset = $('.login-bg-reset');
    const loginBgOriginal = loginBg.attr('src');

    loginBgInput.on('change', function() {
        if (this.files && this.files[0]) {
            if (this.files[0].size > 2048 * 1024) {
                alert('File size is too large. Max size is 2MB.');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                loginBg.attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    loginBgReset.on('click', function() {
        loginBgInput.val('');
        loginBg.attr('src', loginBgOriginal);
    });

    // Color picker sync
    $('#primaryColorInput').on('input', function() {
        $('#colorBadge').text(this.value.toUpperCase());
    });

    // Reset button
    $('#btnReset').on('click', function() {
        if (confirm('Are you sure you want to reset all configuration to default?')) {
            window.location.href = '{{ route('admin.config.reset') }}';
        }
    });
});
</script>
@endpush
