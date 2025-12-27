@extends('layouts.app')
@section('title', 'Medical History')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Medical History</h2>
            <p class="text-muted mb-0">{{ $patient->user->name }} - {{ $patient->national_id }}</p>
        </div>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary">
            ← Back to Profile
        </a>
    </div>

    <!-- Timeline -->
    <div class="card">
        <div class="card-body">
            @if($appointments->count() > 0)
                @foreach($appointments as $appointment)
                <div class="border-start border-3 border-primary ps-3 pb-4 mb-4">
                    <!-- Appointment Date -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">{{ $appointment->appointment_date->format('F j, Y') }}</h5>
                            <p class="text-muted mb-0">
                                {{ $appointment->appointment_time ?? $appointment->appointment_date->format('g:i A') }} • 
                                Dr. {{ $appointment->physician->user->name }} ({{ $appointment->physician->specialization }})
                            </p>
                        </div>
                        <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>

                    <!-- Diagnosis -->
                    @if($appointment->diagnosis)
                    <div class="bg-light p-3 rounded mt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong class="text-primary">Symptoms:</strong>
                                <p class="mb-0">{{ $appointment->diagnosis->symptoms }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-success">Diagnosis:</strong>
                                <p class="mb-0">{{ $appointment->diagnosis->diagnosis }}</p>
                            </div>
                            @if($appointment->diagnosis->prescription)
                            <div class="col-12">
                                <strong class="text-warning">Prescription:</strong>
                                <p class="mb-0">{{ $appointment->diagnosis->prescription }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                        @if($appointment->status == 'completed')
                        <p class="text-muted fst-italic mt-2">No diagnosis recorded</p>
                        @endif
                    @endif

                    <!-- Notes -->
                    @if($appointment->notes)
                    <div class="mt-2">
                        <small class="text-muted"><strong>Notes:</strong> {{ $appointment->notes }}</small>
                    </div>
                    @endif
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $appointments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No medical history available</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection