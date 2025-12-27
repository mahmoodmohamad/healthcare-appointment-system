@extends('layouts.app')
@section('title', 'Physician Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Welcome, Dr. {{ auth()->user()->name }}</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['today_appointments'] }}</h3>
                    <p class="mb-0">Today's Appointments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['today_completed'] }}</h3>
                    <p class="mb-0">Completed Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['today_pending'] }}</h3>
                    <p class="mb-0">Pending Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['total_patients'] }}</h3>
                    <p class="mb-0">Total Patients</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Schedule -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📅 Today's Schedule - {{ now()->format('l, F j, Y') }}</h5>
                </div>
                <div class="card-body">
                    @if($todayAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAppointments as $appointment)
                                    <tr>
                                        <td>
                                            <strong>{{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}</strong>
                                        </td>
                                        <td>
                                            {{ $appointment->patient->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $appointment->patient->national_id }}</small>
                                        </td>
                                        <td>
                                            @if($appointment->status == 'scheduled')
                                                <span class="badge bg-warning">Scheduled</span>
                                            @elseif($appointment->status == 'completed')
                                                <span class="badge bg-success">✓ Completed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($appointment->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('physician.appointments.show', $appointment) }}" 
                                               class="btn btn-sm btn-info">
                                                View
                                            </a>
                                            @if(!$appointment->diagnosis && $appointment->status != 'cancelled')
                                                <a href="{{ route('physician.diagnosis.create', $appointment) }}" 
                                                   class="btn btn-sm btn-success">
                                                    + Diagnosis
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">No appointments scheduled for today</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🗓️ Upcoming (Next 7 Days)</h5>
                </div>
                <div class="card-body">
                    @if($upcomingAppointments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingAppointments as $appointment)
                            <a href="{{ route('physician.appointments.show', $appointment) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $appointment->patient->user->name }}</strong>
                                    <small class="text-muted">{{ $appointment->appointment_date->format('M j') }}</small>
                                </div>
                                <small class="text-muted">
                                    {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                </small>
                            </a>
                            @endforeach
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('physician.appointments.index') }}" class="btn btn-sm btn-outline-primary">
                                View All Appointments →
                            </a>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No upcoming appointments</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection