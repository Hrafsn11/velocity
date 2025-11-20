@extends('layouts.admin')

@section('title', 'Roles Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Roles & Permissions /</span> Roles
    </h4>
    @can('create roles')
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Role
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

<div class="row">
    @foreach($roles as $role)
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{ $role->name }}</h5>
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu">
                            @can('edit roles')
                            <a class="dropdown-item" href="{{ route('roles.edit', $role) }}">
                                <i class="ti ti-pencil me-1"></i> Edit
                            </a>
                            @endcan
                            @can('delete roles')
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                    <i class="ti ti-trash me-1"></i> Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-label-primary rounded-pill me-2">{{ $role->users_count }} Users</span>
                    <span class="badge bg-label-info rounded-pill">{{ $role->permissions_count }} Permissions</span>
                </div>

                <div>
                    <small class="text-muted">Permissions:</small>
                    <div class="mt-2">
                        @forelse($role->permissions->take(5) as $permission)
                        <span class="badge bg-label-secondary me-1 mb-1">{{ $permission->name }}</span>
                        @empty
                        <span class="text-muted">No permissions</span>
                        @endforelse
                        @if($role->permissions->count() > 5)
                        <span class="badge bg-label-secondary">+{{ $role->permissions->count() - 5 }} more</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
