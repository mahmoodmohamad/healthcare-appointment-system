@extends('layouts.app')
@section('title', 'All Receptionists')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Receptionists</h2>
            <p class="text-muted mb-0">All registered front-desk staff.</p>
        </div>
        <a href="{{ route('admin.receptionists.create') }}" class="btn btn-primary">
            ➕ Add Receptionist
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.receptionists.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Name, email, phone..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">City</label>
                    <select name="city_id" class="form-select">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ ($filters['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('admin.receptionists.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($receptionists->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Appointments</th>
                                <th>Patients</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receptionists as $r)
                                <tr>
                                    <td>#{{ $r->id }}</td>
                                    <td><strong>{{ $r->user->name }}</strong></td>
                                    <td>{{ $r->user->email }}</td>
                                    <td>{{ $r->phone }}</td>
                                    <td>{{ $r->city->name ?? '—' }}</td>
                                    <td><span class="badge bg-primary">{{ $r->appointments_count }}</span></td>
                                    <td><span class="badge bg-success">{{ $r->patients_count }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.receptionists.show', $r) }}"
                                           class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $receptionists->links() }}
                </div>
            @else
                <p class="text-center text-muted py-5 mb-0">No receptionists found.</p>
            @endif
        </div>
    </div>
</div>
@endsection