@extends('layouts.app')
@section('title', 'Patient Medical History')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Medical History</h2>
            <p class="text-muted mb-0">{{ $patient->user->name }} - {{ $patient->national_id }}</p>
        </div>
        <a href="{{ route('physician.dashboard') }}" class="btn btn-secondary">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Patient Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>National ID:</strong> {{ $patient->national_id }}</p>
                    <p><strong>Phone:</strong> {{ $patient->phone }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Email:</strong> {{ $patient->user->email }}</p>
                    <p><strong>City:</strong> {{ $patient->city->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <h4>{{ $appointments->count() }}</h4>
                    <p class="text-muted mb-0">Total Visits with You</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical History Timeline -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Appointment History</h5>
        </div>
        <div class="card-body">
            @if($appointments->count() > 0)
                @foreach($appointments as $appointment)
                <div class="border-start border-3 border-{{ $appointment->status == 'completed' ? 'success' : 'primary' }} ps-4 pb-4 mb-4">
                    <!-- Appointment Header -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">{{ $appointment->appointment_date->format('F j, Y') }}</h5>
                            <p class="text-muted mb-0">
                                {{ $appointment->appointment_time ?? $appointment->appointment_date->format('g:i A') }}
                            </p>
                        </div>
                        <div>
                            <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                            <a href="{{ route('physician.appointments.show', $appointment) }}" class="btn btn-sm btn-outline-primary ms-2">
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Diagnosis Details -->
                    @if($appointment->diagnosis)
                    <div class="bg-light p-3 rounded mt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong class="text-primary">Symptoms:</strong>
                                <p class="mb-0 mt-1">{{ $appointment->diagnosis->symptoms }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-success">Diagnosis:</strong>
                                <p class="mb-0 mt-1">{{ $appointment->diagnosis->diagnosis }}</p>
                            </div>
                            @if($appointment->diagnosis->prescription)
                            <div class="col-12">
                                <strong class="text-warning">Prescription:</strong>
                                <p class="mb-0 mt-1" style="white-space: pre-line;">{{ $appointment->diagnosis->prescription }}</p>
                            </div>
                            @endif
                            @if($appointment->diagnosis->notes)
                            <div class="col-12 mt-2">
                                <strong class="text-info">Notes:</strong>
                                <p class="mb-0 mt-1">{{ $appointment->diagnosis->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                        @if($appointment->status == 'completed')
                        <p class="text-muted fst-italic mt-2">No diagnosis recorded for this visit</p>
                        @elseif($appointment->status == 'scheduled')
                        <p class="text-info mt-2">
                            <i class="bi bi-clock"></i> Upcoming appointment
                        </p>
                        @endif
                    @endif

                    <!-- Appointment Notes -->
                    @if($appointment->notes)
                    <div class="mt-2">
                        <small class="text-muted"><strong>Appointment Notes:</strong> {{ $appointment->notes }}</small>
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No appointment history with this patient</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection