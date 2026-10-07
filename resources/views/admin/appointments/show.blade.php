@extends('layouts.app')
@section('title', 'Appointment #' . $appointment->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Appointment #{{ $appointment->id }}</h2>
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="alert alert-info">
        Read-only view. To change this appointment, contact the receptionist who booked it
        or the assigned doctor.
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Appointment</h5></div>
        <div class="card-body">
            <p><strong>Date:</strong> {{ $appointment->appointment_date->format('l, F j, Y') }}</p>
            <p><strong>Time:</strong> {{ $appointment->appointment_time ?? $appointment->appointment_date->format('g:i A') }}</p>
            <p><strong>Status:</strong> <span class="badge bg-primary">{{ ucfirst($appointment->status) }}</span></p>
            @if($appointment->notes)
                <p><strong>Notes:</strong> {{ $appointment->notes }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0">Patient</h5></div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $appointment->patient->user->name }}</p>
                    <p><strong>National ID:</strong> {{ $appointment->patient->national_id }}</p>
                    <p><strong>Phone:</strong> {{ $appointment->patient->phone }}</p>
                    <p><strong>Email:</strong> {{ $appointment->patient->user->email }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0">Doctor</h5></div>
                <div class="card-body">
                    <p><strong>Name:</strong> Dr. {{ $appointment->doctor->user->name }}</p>
                    <p><strong>Specialization:</strong> {{ $appointment->doctor->specialization }}</p>
                    <p><strong>Phone:</strong> {{ $appointment->doctor->phone }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white"><h5 class="mb-0">Booked By</h5></div>
        <div class="card-body">
            @if($appointment->receptionist)
                <p class="mb-0">{{ $appointment->receptionist->user->name }}
                    <small class="text-muted">(Receptionist)</small>
                </p>
            @else
                <p class="mb-0 text-muted">System / direct booking</p>
            @endif
        </div>
    </div>

    @if($appointment->diagnosis)
        <div class="card">
            <div class="card-header bg-warning"><h5 class="mb-0">Diagnosis</h5></div>
            <div class="card-body">
                <p><strong>Symptoms:</strong> {{ $appointment->diagnosis->symptoms }}</p>
                <p><strong>Diagnosis:</strong> {{ $appointment->diagnosis->diagnosis }}</p>
                @if($appointment->diagnosis->prescription)
                    <p><strong>Prescription:</strong> {{ $appointment->diagnosis->prescription }}</p>
                @endif
                @if($appointment->diagnosis->notes)
                    <p><strong>Notes:</strong> {{ $appointment->diagnosis->notes }}</p>
                @endif
                <small class="text-muted">
                    Recorded {{ $appointment->diagnosis->created_at->format('Y-m-d H:i') }}
                </small>
            </div>
        </div>
    @endif
</div>
@endsection