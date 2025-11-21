@extends('layouts.app')

@section('title', 'Create Permission')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Roles & Permissions / Permissions /</span> Create
</h4>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Create New Permission</h5>
                <a href="{{ route('permissions.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label" for="name">Permission Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. view users" required>
                        <small class="text-muted">Use lowercase with spaces (e.g., view users, edit posts)</small>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
