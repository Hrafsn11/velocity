<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login - {{ config('app.name', 'Velocity') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />

        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
        
        @include('layouts.partials.dynamic-theme')
        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
        <script src="{{ asset('assets/js/config.js') }}"></script>

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                /* Gradient Background Modern */
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                padding: 1rem; /* Padding agar tidak nempel pinggir di HP */
            }

            /* Card Styling yang Benar */
            .auth-card-container {
                width: 100%;
                max-width: 400px; /* Lebar maksimal ideal untuk login */
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                padding: 2.5rem;
                position: relative;
                /* PENTING: Height auto agar tidak manjang ke bawah */
                height: auto; 
            }

            /* Fix Double Focus pada Input Group */
            .input-group-merge {
                border: 1px solid #d9dee3;
                border-radius: 0.375rem;
                transition: all 0.2s ease;
                overflow: hidden;
            }
            .input-group-merge:focus-within {
                border-color: {{ app_config('primary_color') }};
                box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.2);
            }
            .input-group-merge .form-control {
                border: none;
                box-shadow: none !important;
            }
            .input-group-merge .input-group-text {
                border: none;
                background: transparent;
            }
        </style>
    </head>
    <body>
        
        <div class="auth-card-container">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded p-2 mb-2 shadow-sm">
                    <i class="ti ti-bolt fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ config('app.name', 'Velocity') }}</h4>
            </div>
            
            {{ $slot }}
        </div>

        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
    </body>
</html>