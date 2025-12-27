@extends('layouts.app')
@section('title', 'Patient Details')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Header -->
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Patient Profile</h2>
                <div>
                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning me-2">
                        ✏️ Edit
                    </a>
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-success me-2">
                        📅 Book Appointment
                    </a>
                    <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                        ← Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <!-- Patient Info Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $patient->user->name }}</td>
                        </tr>
                        <tr>
                            <th>National ID:</th>
                            <td>{{ $patient->national_id }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $patient->phone }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $patient->user->email }}</td>
                        </tr>
                        <tr>
                            <th>City:</th>
                            <td>{{ $patient->city->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Country:</th>
                            <td>{{ $patient->city->country->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Registered:</th>
                            <td>{{ $patient->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @if($patient->secretary)
                        <tr>
                            <th>Registered By:</th>
                            <td>{{ $patient->secretary->user->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h3 class="text-primary">{{ $stats['total_appointments'] }}</h3>
                            <small class="text-muted">Total Appointments</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-success">{{ $stats['upcoming_appointments'] }}</h3>
                            <small class="text-muted">Upcoming</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-warning">{{ $stats['completed_appointments'] }}</h3>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-danger">{{ $stats['total_diagnoses'] }}</h3>
                            <small class="text-muted">Diagnoses</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Appointments</h5>
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-light btn-sm">
                        + New
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Physician</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingAppointments as $appointment)
                                    <tr>
                                        <td>
                                            {{ $appointment->appointment_date->format('Y-m-d') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            Dr. {{ $appointment->physician->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $appointment->physician->specialization }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst($appointment->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-info">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No upcoming appointments</p>
                    @endif
                </div>
            </div>

            <!-- Recent Medical History -->
            <div class="card">
                <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Medical History</h5>
                    <a href="{{ route('patients.medical-history', $patient) }}" class="btn btn-light btn-sm">
                        View All →
                    </a>
                </div>
                <div class="card-body">
                    @if($recentDiagnoses->count() > 0)
                        @foreach($recentDiagnoses as $diagnosis)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $diagnosis->created_at->format('Y-m-d') }}</strong>
                                <small class="text-muted">Dr. {{ $diagnosis->physician->user->name }}</small>
                            </div>
                            <p class="mb-1"><strong>Symptoms:</strong> {{ Str::limit($diagnosis->symptoms, 80) }}</p>
                            <p class="mb-0"><strong>Diagnosis:</strong> {{ Str::limit($diagnosis->diagnosis, 80) }}</p>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-3">No medical history yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection