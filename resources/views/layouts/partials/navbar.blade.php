<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2"></i>
                    <span class="d-none d-md-inline-block text-muted fw-normal">Search (Ctrl+K)</span>
                </a>
            </div>
        </div>
        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow d-flex align-items-center justify-content-center"
                    href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="ti ti-moon-stars ti-md"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                            <span class="align-middle"><i class="ti ti-sun me-2"></i>Light</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                            <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                            <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-0">
                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false">
                    <span class="position-relative">
                        <i class="ti ti-bell ti-md"></i>
                        <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">Notification</h5>
                            <a href="javascript:void(0)" class="dropdown-notifications-all text-body"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i
                                    class="ti ti-mail-opened fs-4"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>

            <li class="nav-item navbar-dropdown dropdown-user dropdown ms-2"> <a
                    class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if($url = auth()->user()->avatar_url)
                        <img src="{{ $url . '?t=' . time() }}" alt class="h-auto rounded-circle object-fit-cover" />
                        @else
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            {{ collect(explode(' ', auth()->user()->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                        </span>
                        @endif
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item mt-0" href="#">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        @if($url = auth()->user()->avatar_url)
                                        <img src="{{ $url . '?t=' . time() }}" alt class="h-auto rounded-circle object-fit-cover" />
                                        @else
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ collect(explode(' ', auth()->user()->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                    <small
                                        class="text-muted">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    @can('manage settings')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.config.index') }}">
                                <i class="ti ti-settings me-2 ti-sm"></i>
                                <span class="align-middle">Configuration</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ti ti-logout me-2 ti-sm"></i>
                                <span class="align-middle">Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="navbar-search-wrapper search-input-wrapper d-none">
        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..."
            aria-label="Search..." />
        <i class="ti ti-x search-toggler cursor-pointer"></i>
    </div>

</nav>
