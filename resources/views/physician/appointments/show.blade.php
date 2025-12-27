@extends('layouts.app')
@section('title', 'Appointment Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Appointment Details</h2>
                <a href="{{ route('physician.appointments.index') }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Appointment Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Appointment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date:</strong> {{ $appointment->appointment_date->format('l, F j, Y') }}</p>
                            <p><strong>Time:</strong> {{ $appointment->appointment_time ?? $appointment->appointment_date->format('g:i A') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            @if($appointment->notes)
                            <p><strong>Notes:</strong> {{ $appointment->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $appointment->patient->user->name }}</p>
                            <p><strong>National ID:</strong> {{ $appointment->patient->national_id }}</p>
                            <p><strong>Phone:</strong> {{ $appointment->patient->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $appointment->patient->user->email }}</p>
                            <p><strong>City:</strong> {{ $appointment->patient->city->name ?? 'N/A' }}</p>
                            <a href="{{ route('physician.patients.history', $appointment->patient) }}" class="btn btn-sm btn-outline-primary">
                                View Medical History →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis -->
            @if($appointment->diagnosis)
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Diagnosis</h5>
                    <a href="{{ route('physician.diagnosis.edit', $appointment) }}" class="btn btn-sm btn-light">
                        ✏️ Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Symptoms:</strong>
                        <p>{{ $appointment->diagnosis->symptoms }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Diagnosis:</strong>
                        <p>{{ $appointment->diagnosis->diagnosis }}</p>
                    </div>
                    @if($appointment->diagnosis->prescription)
                    <div class="mb-3">
                        <strong>Prescription:</strong>
                        <p style="white-space: pre-line;">{{ $appointment->diagnosis->prescription }}</p>
                    </div>
                    @endif
                    @if($appointment->diagnosis->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong>
                        <p>{{ $appointment->diagnosis->notes }}</p>
                    </div>
                    @endif
                    <small class="text-muted">
                        Recorded on: {{ $appointment->diagnosis->created_at->format('F j, Y g:i A') }}
                    </small>
                </div>
            </div>
            @else
                @if($appointment->status != 'cancelled')
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5>No Diagnosis Recorded</h5>
                        <p class="text-muted">This appointment doesn't have a diagnosis yet.</p>
                        <a href="{{ route('physician.diagnosis.create', $appointment) }}" class="btn btn-success btn-lg">
                            + Add Diagnosis
                        </a>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection