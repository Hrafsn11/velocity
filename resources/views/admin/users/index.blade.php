@extends('layouts.admin')

@section('title', 'Users Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">User Management /</span> Users
    </h4>
    @can('create users')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add User
    </a>
    @endcan
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
    <div class="card-header">
        <h5 class="card-title mb-0">All Users</h5>
    </div>
    <div class="card-datatable table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle">
                                @else
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $user->name }}</h6>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                        <span class="badge bg-label-info">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->email_verified_at)
                        <span class="badge bg-label-success">Verified</span>
                        @else
                        <span class="badge bg-label-warning">Pending</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                @can('edit users')
                                <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                                    <i class="ti ti-pencil me-1"></i> Edit
                                </a>
                                @endcan
                                @can('delete users')
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                        <i class="ti ti-trash me-1"></i> Delete
                                    </button>
                                </form>
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
@endsection
