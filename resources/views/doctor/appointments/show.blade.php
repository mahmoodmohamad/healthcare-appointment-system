@extends('layouts.app')
@section('title', '@lang('site.appointment_details')')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>@lang('site.appointment_details')</h2>
                <a href="{{ route('doctor.appointments.index') }}" class="btn btn-secondary">
                    @lang('site.back')
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
                    <h5 class="mb-0">@lang('site.appointment_information')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>@lang('site.date_label')</strong> {{ $appointment->appointment_date->format('l, F j, Y') }}</p>
                            <p><strong>@lang('site.time_label')</strong> {{ $appointment->appointment_time ?? $appointment->appointment_date->format('g:i A') }}</p>
                            <p><strong>@lang('site.status_label')</strong> 
                                <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            @if($appointment->notes)
                            <p><strong>@lang('site.notes_label')</strong> {{ $appointment->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">@lang('site.patient_information')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>@lang('site.name_label')</strong> {{ $appointment->patient->user->name }}</p>
                            <p><strong>@lang('site.national_id_label')</strong> {{ $appointment->patient->national_id }}</p>
                            <p><strong>@lang('site.phone_label')</strong> {{ $appointment->patient->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>@lang('site.email_label')</strong> {{ $appointment->patient->user->email }}</p>
                            <p><strong>@lang('site.city_label')</strong> {{ $appointment->patient->city->name ?? 'N/A' }}</p>
                            <a href="{{ route('doctor.patients.history', $appointment->patient) }}" class="btn btn-sm btn-outline-primary">
                                @lang('site.view_medical_history') →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis -->
            @if($appointment->diagnosis)
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@lang('site.diagnosis')</h5>
                    <a href="{{ route('doctor.diagnosis.edit', $appointment) }}" class="btn btn-sm btn-light">
                        ✏️ @lang('site.edit')
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>@lang('site.symptoms_label')</strong>
                        <p>{{ $appointment->diagnosis->symptoms }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>@lang('site.diagnosis_label')</strong>
                        <p>{{ $appointment->diagnosis->diagnosis }}</p>
                    </div>
                    @if($appointment->diagnosis->prescription)
                    <div class="mb-3">
                        <strong>@lang('site.prescription_label')</strong>
                        <p style="white-space: pre-line;">{{ $appointment->diagnosis->prescription }}</p>
                    </div>
                    @endif
                    @if($appointment->diagnosis->notes)
                    <div class="mb-3">
                        <strong>@lang('site.notes_label')</strong>
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
                        <h5>@lang('site.no_diagnosis_recorded_title')</h5>
                        <p class="text-muted">@lang('site.appointment_no_diagnosis')</p>
                        <a href="{{ route('doctor.diagnosis.create', $appointment) }}" class="btn btn-success btn-lg">
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