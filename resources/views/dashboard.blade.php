@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Welcome {{ auth()->user()->name }}! 🎉</h5>
                            <p class="mb-4">
                                You have <span class="fw-medium">{{ $totalUsers }}</span> total users registered. 
                                Check out your dashboard statistics below.
                            </p>

                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">View Profile</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/girl-doing-yoga-light.png') }}"
                                height="140"
                                alt="View Badge User"
                                data-app-dark-img="illustrations/girl-doing-yoga-dark.png"
                                data-app-light-img="illustrations/girl-doing-yoga-light.png" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-4 col-md-4 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-users ti-sm"></i>
                            </span>
                        </div>
                    </div>
                    <span class="fw-medium d-block mb-1">Total Users</span>
                    <h3 class="card-title mb-2">{{ $totalUsers }}</h3>
                    @can('view users')
                    <small class="text-success fw-medium">
                        <a href="{{ route('users.index') }}">View All</a>
                    </small>
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-shield ti-sm"></i>
                            </span>
                        </div>
                    </div>
                    <span class="fw-medium d-block mb-1">Total Roles</span>
                    <h3 class="card-title mb-2">{{ $totalRoles }}</h3>
                    @can('view roles')
                    <small class="text-success fw-medium">
                        <a href="{{ route('roles.index') }}">View All</a>
                    </small>
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti ti-lock ti-sm"></i>
                            </span>
                        </div>
                    </div>
                    <span class="fw-medium d-block mb-1">Total Permissions</span>
                    <h3 class="card-title mb-2">{{ $totalPermissions }}</h3>
                    @can('view permissions')
                    <small class="text-success fw-medium">
                        <a href="{{ route('permissions.index') }}">View All</a>
                    </small>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Recent Users Table -->
        @can('view users')
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="avatar me-2">
                                                @if($user->avatar_url)
                                                <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle">
                                                @else
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ collect(explode(' ', $user->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $user->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                        <span class="badge bg-label-info">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($user->email_verified_at)
                                        <span class="badge bg-label-success">Verified</span>
                                        @else
                                        <span class="badge bg-label-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <!-- User Roles Info -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your Roles & Permissions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Your Roles:</strong>
                        @foreach(auth()->user()->roles as $role)
                        <span class="badge bg-primary me-1">{{ $role->name }}</span>
                        @endforeach
                    </div>
                    <div>
                        <strong>Your Permissions:</strong>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach(auth()->user()->getAllPermissions()->take(10) as $permission)
                            <span class="badge bg-label-secondary">{{ $permission->name }}</span>
                            @endforeach
                            @if(auth()->user()->getAllPermissions()->count() > 10)
                            <span class="badge bg-label-info">+{{ auth()->user()->getAllPermissions()->count() - 10 }} more</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
