@extends('layouts.admin')

@section('title', 'View User')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">User Management / Users /</span> View
</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">User Details</h5>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th width="200">Name</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Roles</th>
                        <td>
                            @foreach($user->roles as $role)
                            <span class="badge bg-label-info">{{ $role->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->email_verified_at)
                            <span class="badge bg-label-success">Verified</span>
                            @else
                            <span class="badge bg-label-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Joined Date</th>
                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
