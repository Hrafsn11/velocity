@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Roles & Permissions / Roles /</span> Create
</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Create New Role</h5>
                <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label" for="name">Role Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Assign Permissions</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm{{ $permission->id }}">
                                    <label class="form-check-label" for="perm{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Role</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
