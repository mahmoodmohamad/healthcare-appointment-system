@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">Edit User</h4>

    <div class="card">
        <h5 class="card-header">User Information</h5>
        <div class="card-body">

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="form-label">New Password (optional)</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}"
                                {{ $user->$role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Activation --}}
                <div class="form-check mb-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="activation"
                        {{ $user->activation ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update User
                </button>

                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>
</div>
@endsection
