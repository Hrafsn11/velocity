<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - {{ app_config('app_name') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(app_config('app_logo')) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />

    @stack('styles')

    <!-- Dynamic Theme Colors -->
    <style>
        /* Active Menu Item */
        .bg-menu-theme.menu-vertical .menu-item.active>.menu-link:not(.menu-toggle) {
            background: {{ app_config('primary_color') }} !important;
            border: 1px solid {{ app_config('primary_color') }} !important;
            box-shadow: 0 0.125rem 0.375rem {{ app_config('primary_color_shadow') }} !important;
        }

        .text-primary {
            color: {{ app_config('primary_color') }} !important;
        }

        .bg-primary {
            background-color: {{ app_config('primary_color') }} !important;
        }

        .bg-primary-shadow {
            box-shadow: 0 0.125rem 0.375rem {{ app_config('primary_color_shadow') }} !important;
        }

        .bg-label-primary {
            background-color: {{ app_config('primary_color_label') }} !important;
            color: {{ app_config('primary_color') }} !important;
        }

        .btn-primary.btn[class*=btn-]:not([class*=btn-label-]):not([class*=btn-outline-]):not([class*=btn-text-]):not(.btn-icon):not(:disabled):not(.disabled) {
            box-shadow: 0 0.125rem 0.375rem 0 {{ app_config('primary_color_shadow') }};
        }

        .timeline .timeline-item .timeline-point {
            background-color: {{ app_config('primary_color') }} !important;
            color: {{ app_config('primary_color') }} !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: {{ app_config('primary_color') }} !important;
            color: #fff !important;
        }

        .select2-container--default .select2-results__option--highlighted:not([aria-selected=true]) {
            background-color: {{ app_config('primary_color_label') }} !important;
            color: {{ app_config('primary_color') }} !important;
        }

        .dropdown-item.active {
            background-color: {{ app_config('primary_color_label') }} !important;
        }
        .dropdown-item.active>span {
            color: {{ app_config('primary_color') }} !important;
        }
        .dropdown-item.active>i {
            color: {{ app_config('primary_color') }} !important;
        }

        .app-brand-logo.demo {
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-pack: center;
            justify-content: center;
            display: -ms-flexbox;
            display: flex;
            width: 35px;
            height: 40px;
        }
        
        .form-control:focus:not([disabled]) {
            border-color: {{ app_config('primary_color') }} !important;
        }
       
        a:hover {
            color: {{ app_config('primary_color_hover') }} !important;
        }

        /* nav-link.active */
        .nav-link {
            color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;            
        }

        .nav-link:hover {
            color: {{ app_config('primary_color_hover') }} !important;
            background-color: {{ app_config('primary_color_label') }} !important;
        }

        .nav-link.active {
            color: #ffffff !important;
            box-shadow: 0 0.125rem 0.375rem {{ app_config('primary_color_shadow') }} !important;
            background-color: {{ app_config('primary_color') }} !important;
        }
        
        .page-item.active > .page-link {
            color : #fff !important;
            border-color: {{ app_config('primary_color') }} !important;
            background-color: {{ app_config('primary_color') }} !important;
            box-shadow: 0 0.125rem 0.375rem {{ app_config('primary_color_shadow') }} !important;
        }
        
        /* GENERATED BY BOOTSTRAP 5 COLOR THEME GENERATOR */
        .btn-outline-primary {
            color: {{ app_config('primary_color') }} !important;
            background-color: transparent !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:active,
        .btn-outline-primary:focus,
        .btn-outline-primary:focus-visible,
        .btn-outline-primary:not(:disabled):not(.disabled):hover,
        .btn-outline-primary:not(:disabled):not(.disabled):active,
        .btn-outline-primary:not(:disabled):not(.disabled).active {
            color: #fff !important;
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        .form-check-input:focus+.btn-outline-primary,
        .btn-outline-primary:focus {
            box-shadow: 0 0.125rem 0.375rem {{ app_config('primary_color_shadow') }} !important;
        }

        .form-check:not(.form-check-danger) > .form-check-input:checked {
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
            box-shadow: {{ app_config('primary_color_shadow') }} !important;
        }

        .form-check-input:checked+.btn-outline-primary,
        .form-check-input:active+.btn-outline-primary,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .btn-outline-primary.dropdown-toggle.show {
            color: #fff;
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        .form-check-input:checked+.btn-outline-primary:focus,
        .form-check-input:active+.btn-outline-primary:focus,
        .btn-outline-primary:active:focus,
        .btn-outline-primary.active:focus,
        .btn-outline-primary.dropdown-toggle.show:focus {
            box-shadow: 0 0.125rem 0.375rem {{ app_config('primary_color_shadow') }} !important;
        }

        .btn-outline-primary:disabled,
        .btn-outline-primary.disabled {
            color: {{ app_config('primary_color') }} !important;
            background-color: transparent;
        }

        .alert-primary,
        .badge.alert-primary,
        .btn.alert-primary {
            color: {{ app_config('primary_color') }} !important;
            background-color: {{ app_config('primary_color_label') }} !important;
            border-color: {{ app_config('primary_color_label') }} !important;
        }

        .alert-primary .alert-link {
            text-decoration: underline;
        }

        /* Badge styles - no hover needed */
        .badge.bg-label-primary,
        .badge.alert-primary {
            background-color: {{ app_config('primary_color_label') }} !important;
            color: {{ app_config('primary_color') }} !important;
            border: none !important;
        }

        .border-primary {
            border-color: {{ app_config('primary_color') }} !important;
        }

        .link-primary {
            color: {{ app_config('primary_color') }} !important;
        }

        .link-primary:hover,
        .link-primary:focus {
            color: {{ app_config('primary_color') }} !important;
        }

        .btn-primary {
            color: #ffffff !important;
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        .btn-primary:hover,
        .btn-primary:active,
        .btn-primary:focus-visible {
            color: #ffffff !important;
            background-color: {{ app_config('primary_color_hover') }} !important;
            border-color: {{ app_config('primary_color_hover') }} !important;
        }

        .form-check-input:focus+.btn-primary,
        .btn-primary:focus {
            color: #ffffff;
            background-color: {{ app_config('primary_color_hover') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
            box-shadow: 0 0.125rem 0.375rem 0 {{ app_config('primary_color_shadow') }};
        }

        .form-check-input:checked+.btn-primary,
        .form-check-input:active+.btn-primary,
        .btn-primary:active,
        .btn-primary.active,
        .show>.btn-primary.dropdown-toggle {
            color: #ffffff;
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        .form-check-input:checked+.btn-primary:focus,
        .form-check-input:active+.btn-primary:focus,
        .btn-primary:active:focus,
        .btn-primary.active:focus,
        .show>.btn-primary.dropdown-toggle:focus {
            box-shadow: 0 0.125rem 0.375rem {{ app_config('primary_color_shadow') }};
        }

        .btn-primary:disabled,
        .btn-primary.disabled {
            color: #ffffff;
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        /* Wizard Stepper */
        .bs-stepper-circle {
            padding: 24px !important;
            border-radius: 50% !important;
        }

        .bs-stepper .step.active .bs-stepper-circle {
            background-color: {{ app_config('primary_color') }} !important;
            box-shadow: 0 0.125rem 0.375rem 0 {{ app_config('primary_color') }} !important;
        }

        .bs-stepper .step:not(.active) .bs-stepper-circle {
            background-color: rgba(var(--bs-secondary-rgb), 0.6) !important;
            color: #FFF !important;
        }

        .bs-stepper .step .step-trigger[aria-selected="true"] .bs-stepper-label .bs-stepper-title {            
            font-weight: bold;
        }

        .bs-stepper .step.active .bs-stepper-label .bs-stepper-title {
            color: {{ app_config('primary_color') }} !important;
        }

        .bs-stepper .step:not(.active) .bs-stepper-label .bs-stepper-title {
            color: rgba(var(--bs-secondary-rgb), 0.6) !important;            
        }

        .bs-stepper-line:is(.active) {
            background-color: {{ app_config('primary_color') }} !important;
        }

        .bs-stepper-line:not(.active) {
            background-color: rgba(var(--bs-secondary-rgb), 0.4) !important;
        }

        /* Secondary Color */
        .btn-secondary {
            color: #ffffff;
            background-color: {{ app_config('secondary_color') }} !important;
            border-color: {{ app_config('secondary_color') }} !important;
        }

        .btn-secondary:hover,
        .btn-secondary:active,
        .btn-secondary:focus,
        .btn-secondary:focus-visible {
            color: #ffffff !important;
            background-color: {{ app_config('secondary_color_hover') }} !important;
            border-color: {{ app_config('secondary_color_hover') }} !important;
            box-shadow: 0 0.125rem 0.375rem 0 {{ app_config('secondary_color_shadow') }} !important;
        }

        /* FIX ALL BLUE COLORS - Override Bootstrap defaults */
        a:not(.btn):not(.page-link):not(.dropdown-item):hover,
        a:not(.btn):not(.page-link):not(.dropdown-item):focus,
        a:not(.btn):not(.page-link):not(.dropdown-item):active {
            color: {{ app_config('primary_color_hover') }} !important;
        }

        .btn-link:hover,
        .btn-link:focus,
        .btn-link:active {
            color: {{ app_config('primary_color_hover') }} !important;
        }

        /* Force override any blue from Bootstrap or Vuexy */
        .btn-primary:not(:disabled):not(.disabled):hover,
        .btn-primary:not(:disabled):not(.disabled):active,
        .btn-primary:not(:disabled):not(.disabled).active,
        .show > .btn-primary.dropdown-toggle {
            color: #ffffff !important;
            background-color: {{ app_config('primary_color_hover') }} !important;
            border-color: {{ app_config('primary_color_hover') }} !important;
        }

        .form-check-input:checked {
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        .form-check-input:focus {
            border-color: {{ app_config('primary_color') }} !important;
            box-shadow: 0 0 0 0.25rem {{ app_config('primary_color_shadow') }} !important;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: {{ app_config('primary_color') }} !important;
            box-shadow: 0 0 0 0.25rem {{ app_config('primary_color_shadow') }} !important;
        }

        .nav-pills .nav-link.active {
            background-color: {{ app_config('primary_color') }} !important;
        }

        .dropdown-item:active,
        .dropdown-item:focus {
            background-color: {{ app_config('primary_color_label') }} !important;
            color: {{ app_config('primary_color') }} !important;
        }

        .list-group-item.active {
            background-color: {{ app_config('primary_color') }} !important;
            border-color: {{ app_config('primary_color') }} !important;
        }

        .badge.bg-primary {
            background-color: {{ app_config('primary_color') }} !important;
        }

        .progress-bar {
            background-color: {{ app_config('primary_color') }} !important;
        }

        .spinner-border.text-primary {
            color: {{ app_config('primary_color') }} !important;
        }

        /* Override any remaining Bootstrap blue */
        .text-primary,
        .link-primary,
        .link-primary:hover,
        .link-primary:focus {
            color: {{ app_config('primary_color') }} !important;
        }

        .bg-primary,
        .badge.bg-primary {
            background-color: {{ app_config('primary_color') }} !important;
        }

        .border-primary {
            border-color: {{ app_config('primary_color') }} !important;
        }

        /* Card hover effects */
        .card:hover {
            border-color: {{ app_config('primary_color_label') }} !important;
        }

        /* Table active row */
        .table-active,
        .table-active > th,
        .table-active > td {
            background-color: {{ app_config('primary_color_label') }} !important;
        }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.partials.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.partials.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.partials.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    @stack('scripts')
</body>

</html>
