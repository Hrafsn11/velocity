<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">

            @if (app_config('sidebar_logo') && file_exists(public_path(app_config('sidebar_logo'))))
                <span class="app-brand-logo demo">
                    <img src="{{ asset(app_config('sidebar_logo')) }}" alt="Logo"
                        style="max-height: 22px; width: auto;">
                </span>
            @else
                <span class="app-brand-logo demo">
                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                            fill="{{ app_config('primary_hex') }}" />
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                            d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                            fill="{{ app_config('primary_hex') }}" />
                    </svg>
                </span>
            @endif

            <span class="app-brand-text demo menu-text fw-bold">{{ app_config('sidebar_name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <li @class(['menu-item', 'active' => request()->routeIs('dashboard')])>
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        @role('Super Admin')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">PMO & Workspaces</span>
            </li>

            <li @class(['menu-item', 'active' => request()->routeIs('workspaces')])>
                <a href="{{ route('workspaces.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-layout"></i>
                    <div data-i18n="Workspaces">Workspaces</div>
                </a>
            </li>


            {{-- <li @class(['menu-item', 'active' => request()->routeIs('workspaces.*')])>
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-briefcase"></i>
                    <div data-i18n="Workspaces">Workspaces</div>
                </a>
                <ul class="menu-sub">
                    <li @class([
                        'menu-item',
                        'active' => request()->routeIs('workspaces.index'),
                    ])>
                        <a href="{{ route('workspaces.index') }}" class="menu-link">
                            <div data-i18n="All Workspaces">All Workspaces</div>
                        </a>
                    </li>
                </ul>
            </li> --}}

            <li @class(['menu-item', 'active' => request()->routeIs('timeline.*')])>
                <a href="{{ route('timeline.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-timeline"></i>
                    <div data-i18n="Global Timeline">Global Timeline</div>
                </a>
            </li>

            <li @class([
                'menu-item',
                'active open' => request()->routeIs('risk.*'),
            ])>
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-alert-triangle"></i>
                    <div data-i18n="Risk Management">Risk Management</div>
                </a>
                <ul class="menu-sub">
                    <li @class([
                        'menu-item',
                        'active' => request()->routeIs('risk.dashboard'),
                    ])>
                        <a href="{{ route('risk.dashboard') }}" class="menu-link">
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>
                    <li @class(['menu-item', 'active' => request()->routeIs('risk.index')])>
                        <a href="{{ route('risk.index') }}" class="menu-link">
                            <div data-i18n="Risk List">Risk List</div>
                        </a>
                    </li>
                    <li @class(['menu-item', 'active' => request()->routeIs('risk.issues')])>
                        <a href="{{ route('risk.issues') }}" class="menu-link">
                            <div data-i18n="Issue Tracker">Issue Tracker</div>
                        </a>
                    </li>
                    <li @class([
                        'menu-item',
                        'active' => request()->routeIs('risk.change-requests'),
                    ])>
                        <a href="{{ route('risk.change-requests') }}" class="menu-link">
                            <div data-i18n="Change Requests">Change Requests</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endrole

        @can('view employees')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">HR Management</span>
            </li>
            <li @class(['menu-item', 'active' => request()->routeIs('employees.*')])>
                <a href="{{ route('employees.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-users-group"></i>
                    <div data-i18n="Employees">Employees</div>
                </a>
            </li>
        @endcan

        @role('Employee')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Workspace</span>
            </li>

            <li @class([
                'menu-item',
                'active' => request()->routeIs('workspaces.index'),
            ])>
                <a href="{{ route('workspaces.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-briefcase"></i>
                    <div data-i18n="My Workspace">My Workspace</div>
                </a>
            </li>
        @endrole



        @canany(['view users'])
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">User Management</span>
            </li>

            @can('view users')
                <li @class(['menu-item', 'active' => request()->routeIs('users.*')])>
                    <a href="{{ route('users.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-users"></i>
                        <div data-i18n="Users">Users</div>
                    </a>
                </li>
            @endcan
        @endcanany

        @canany(['view roles', 'view permissions'])
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Security & Access</span>
            </li>

            @can('view roles')
                <li @class(['menu-item', 'active' => request()->routeIs('roles.*')])>
                    <a href="{{ route('roles.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-settings"></i>
                        <div data-i18n="Roles">Roles</div>
                    </a>
                </li>
            @endcan

            @can('view permissions')
                <li @class(['menu-item', 'active' => request()->routeIs('permissions.*')])>
                    <a href="{{ route('permissions.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-lock"></i>
                        <div data-i18n="Permissions">Permissions</div>
                    </a>
                </li>
            @endcan
        @endcanany

        @can('manage settings')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Apps & Pages</span>
            </li>

            <li @class([
                'menu-item',
                'active' => request()->routeIs('admin.config.*'),
            ])>
                <a href="{{ route('admin.config.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-adjustments"></i>
                    <div data-i18n="Configuration">Configuration</div>
                </a>
            </li>
        @endcan
    </ul>
</aside>
