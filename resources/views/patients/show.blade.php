@extends('layouts.app')
@section('title', 'Patient Details')

@section('content')
<div class="ui-page container-fluid py-4">
    <div class="row">
        <!-- Header -->
        <div class="col-12">
            <div class="ui-page-head d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div><h1 class="ui-title">Patient Profile</h1><p class="ui-sub mb-0">{{ $patient->user->name }}</p></div>
                <div>
                    <a href="{{ route('patients.edit', $patient) }}" class="ui-btn ui-btn-ghost me-2">
                        ✏️ Edit
                    </a>
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="ui-btn ui-btn-primary me-2">
                        📅 Book Appointment
                    </a>
                    <a href="{{ route('patients.index') }}" class="ui-btn ui-btn-ghost">
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
            <div class="ui-card card mb-4">
                <div class="ui-card-head card-header">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <table class="ui-table table-borderless mb-0">
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
                            <td>{{ $patient->created_at->translatedFormat('Y-m-d') }}</td>
                        </tr>
                        @if($patient->receptionist)
                        <tr>
                            <th>Registered By:</th>
                            <td>{{ $patient->receptionist->user->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="ui-card card">
                <div class="ui-card-head card-header">
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
                            <small class="text-muted">@lang('site.upcoming')</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-warning">{{ $stats['completed_appointments'] }}</h3>
                            <small class="text-muted">@lang('site.completed')</small>
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
            <div class="ui-card card mb-4">
                <div class="ui-card-head card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Appointments</h5>
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="ui-btn ui-btn-ghost ui-btn-sm">
                        + New
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingAppointments->count() > 0)
                        <div class="ui-table-wrap">
                            <table class="ui-table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>@lang('site.status')</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingAppointments as $appointment)
                                    <tr>
                                        <td data-label="Date & Time">
                                            {{ $appointment->appointment_date->translatedFormat('Y-m-d') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                            </small>
                                        </td>
                                        <td data-label="Doctor">
                                            Dr. {{ $appointment->doctor->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $appointment->doctor->specialization }}</small>
                                        </td>
                                        <td data-label="Status">
                                            <span class="ui-badge ui-badge-scheduled">{{ ucfirst($appointment->status) }}</span>
                                        </td>
                                        <td data-label="Action">
                                            <a href="{{ route('appointments.show', $appointment) }}" class="ui-btn ui-btn-ghost ui-btn-sm">
                                                @lang('site.view')
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

            
        </div>
    </div>
</div>
@endsection