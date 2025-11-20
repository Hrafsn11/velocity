@extends('layouts.admin')

@section('title', 'Edit Permission')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Roles & Permissions / Permissions /</span> Edit
</h4>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Permission: {{ $permission->name }}</h5>
                <a href="{{ route('permissions.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label" for="name">Permission Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name) }}" required>
                        <small class="text-muted">Use lowercase with spaces (e.g., view users, edit posts)</small>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        This permission is currently assigned to <strong>{{ $permission->roles->count() }}</strong> role(s).
                    </div>

                    <button type="submit" class="btn btn-primary">Update Permission</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
