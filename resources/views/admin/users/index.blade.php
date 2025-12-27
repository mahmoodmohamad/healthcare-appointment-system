@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            ➕ Add New User
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Name, Email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="physician" {{ request('role') == 'physician' ? 'selected' : '' }}>Physician</option>
                        <option value="secretary" {{ request('role') == 'secretary' ? 'selected' : '' }}>Secretary</option>
                        <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleBadge = [
                                            'Admin' => 'danger',
                                            'Physician' => 'primary',
                                            'Secretary' => 'info',
                                            'Patient' => 'success',
                                        ];
                                        $role = $user->getRoleName();
                                        $badge = $roleBadge[$role] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $role }}</span>
                                </td>
                                <td>
                                    @if($user->activation)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $user->created_at->format('Y-m-d') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn btn-sm btn-info"
                                           title="View">
                                            👁️
                                        </a>
                                        
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-activation', $user) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-{{ $user->activation ? 'warning' : 'success' }}"
                                                        title="{{ $user->activation ? 'Deactivate' : 'Activate' }}">
                                                    {{ $user->activation ? '🔒' : '✅' }}
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    🗑️
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary">You</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No users found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection