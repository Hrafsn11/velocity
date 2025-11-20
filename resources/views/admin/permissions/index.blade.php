@extends('layouts.admin')

@section('title', 'Permissions Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Roles & Permissions /</span> Permissions
    </h4>
    @can('create permissions')
    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Permission
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
        <h5 class="card-title mb-0">All Permissions</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Permission Name</th>
                    <th>Assigned to Roles</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                <tr>
                    <td><strong>{{ $permission->name }}</strong></td>
                    <td>
                        @foreach($permission->roles as $role)
                        <span class="badge bg-label-info me-1">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $permission->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                @can('edit permissions')
                                <a class="dropdown-item" href="{{ route('permissions.edit', $permission) }}">
                                    <i class="ti ti-pencil me-1"></i> Edit
                                </a>
                                @endcan
                                @can('delete permissions')
                                <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline">
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
                    <td colspan="4" class="text-center py-4">No permissions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
