@extends('layouts.app')
@section('title', 'Patients')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Patients</h2>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            ➕ Register New Patient
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('patients.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Name, National ID, Phone, Email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">City</label>
                    <select name="city_id" class="form-select">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('patients.index') }}" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="card">
        <div class="card-body">
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                
                                <th>Phone</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                            <tr>
                                <td>{{ $patient->id }}</td>
                                <td>
                                    <strong>{{ $patient->user->name }}</strong>
                                </td>
                                
                                <td>{{ $patient->phone }}</td>
                                <td>{{ $patient->user->email }}</td>
                                <td>{{ $patient->city->name ?? 'N/A' }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $patient->created_at->format('Y-m-d') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('patients.show', $patient) }}" 
                                           class="btn btn-sm btn-info"
                                           title="View Details">
                                            👁️ View
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" 
                                           class="btn btn-sm btn-warning"
                                           title="Edit">
                                            ✏️ Edit
                                        </a> 
                                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" 
                                           class="btn btn-sm btn-success"
                                           title="Book Appointment">
                                            📅 Book
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $patients->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <h4 class="text-muted">No patients found.</h4>
                    @if(request('search') || request('city_id'))
                        <p>Try adjusting your search filters.</p>
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary">Clear Filters</a>
                    @else
                        <a href="{{ route('patients.create') }}" class="btn btn-primary mt-3">
                            Register First Patient
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection