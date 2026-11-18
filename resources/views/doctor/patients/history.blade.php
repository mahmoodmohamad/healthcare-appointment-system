@extends('layouts.app')
@section('title', 'Patient @lang('site.medical_history')')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>@lang('site.medical_history')</h2>
            <p class="text-muted mb-0">{{ $patient->user->name }} - {{ $patient->national_id }}</p>
        </div>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Patient Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>@lang('site.national_id_label')</strong> {{ $patient->national_id }}</p>
                    <p><strong>@lang('site.phone_label')</strong> {{ $patient->phone }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>@lang('site.email_label')</strong> {{ $patient->user->email }}</p>
                    <p><strong>@lang('site.city_label')</strong> {{ $patient->city->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <h4>{{ $appointments->count() }}</h4>
                    <p class="text-muted mb-0">@lang('site.total_visits_with_you')</p>
                </div>
            </div>
        </div>
    </div>

    <!-- @lang('site.medical_history') Timeline -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('site.appointment_history')</h5>
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
                            <a href="{{ route('doctor.appointments.show', $appointment) }}" class="btn btn-sm btn-outline-primary ms-2">
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Diagnosis Details -->
                    @if($appointment->diagnosis)
                    <div class="bg-light p-3 rounded mt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong class="text-primary">@lang('site.symptoms_label')</strong>
                                <p class="mb-0 mt-1">{{ $appointment->diagnosis->symptoms }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-success">@lang('site.diagnosis_label')</strong>
                                <p class="mb-0 mt-1">{{ $appointment->diagnosis->diagnosis }}</p>
                            </div>
                            @if($appointment->diagnosis->prescription)
                            <div class="col-12">
                                <strong class="text-warning">@lang('site.prescription_label')</strong>
                                <p class="mb-0 mt-1" style="white-space: pre-line;">{{ $appointment->diagnosis->prescription }}</p>
                            </div>
                            @endif
                            @if($appointment->diagnosis->notes)
                            <div class="col-12 mt-2">
                                <strong class="text-info">@lang('site.notes_label')</strong>
                                <p class="mb-0 mt-1">{{ $appointment->diagnosis->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                        @if($appointment->status == 'completed')
                        <p class="text-muted fst-italic mt-2">@lang('site.no_diagnosis_for_visit')</p>
                        @elseif($appointment->status == 'scheduled')
                        <p class="text-info mt-2">
                            <i class="bi bi-clock"></i> Upcoming appointment
                        </p>
                        @endif
                    @endif

                    <!-- Appointment Notes -->
                    @if($appointment->notes)
                    <div class="mt-2">
                        <small class="text-muted"><strong>Appointment @lang('site.notes_label')</strong> {{ $appointment->notes }}</small>
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <p class="text-muted">@lang('site.no_patient_appointment_history')</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection