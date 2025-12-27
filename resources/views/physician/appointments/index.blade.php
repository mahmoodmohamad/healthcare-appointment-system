@extends('layouts.app')
@section('title', 'My Appointments')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">My Appointments</h2>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('physician.appointments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="">All Appointments</option>
                        <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="upcoming" {{ request('filter') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="past" {{ request('filter') == 'past' ? 'selected' : '' }}>Past</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('physician.appointments.index') }}" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="card-body">
            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th>Diagnosis</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                            <tr>
                                <td>
                                    <strong>{{ $appointment->appointment_date->format('Y-m-d') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                    </small>
                                </td>
                                <td>
                                    {{ $appointment->patient->user->name }}
                                    <br>
                                    <small class="text-muted">{{ $appointment->patient->national_id }}</small>
                                </td>
                                <td>
                                    @if($appointment->status == 'scheduled')
                                        <span class="badge bg-primary">Scheduled</span>
                                    @elseif($appointment->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appointment->diagnosis)
                                        <span class="badge bg-success">✓ Done</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('physician.appointments.show', $appointment) }}" class="btn btn-sm btn-info">
                                        View
                                    </a>
                                    @if(!$appointment->diagnosis && $appointment->status != 'cancelled')
                                        <a href="{{ route('physician.diagnosis.create', $appointment) }}" class="btn btn-sm btn-success">
                                            + Diagnosis
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $appointments->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No appointments found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection