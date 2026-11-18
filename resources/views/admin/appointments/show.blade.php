@extends('layouts.app')
@section('title', 'Appointment #' . $appointment->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Appointment #{{ $appointment->id }}</h2>
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">@lang('site.back')</a>
    </div>

    <div class="alert alert-info">
        @lang('site.read_only_view_notice')
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">@lang('site.appointment')</h5></div>
        <div class="card-body">
            <p><strong>@lang('site.date_label')</strong> {{ $appointment->appointment_date->format('l, F j, Y') }}</p>
            <p><strong>@lang('site.time_label')</strong> {{ $appointment->appointment_time ?? $appointment->appointment_date->format('g:i A') }}</p>
            <p><strong>@lang('site.status_label')</strong> <span class="badge bg-primary">{{ match ($appointment->status) { 'scheduled' => __('site.scheduled'), 'completed' => __('site.completed'), 'cancelled' => __('site.cancelled'), default => __('site.unknown') } }}</span></p>
            @if($appointment->notes)
                <p><strong>@lang('site.notes_label')</strong> {{ $appointment->notes }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0">@lang('site.patient')</h5></div>
                <div class="card-body">
                    <p><strong>@lang('site.name_label')</strong> {{ $appointment->patient->user->name }}</p>
                    <p><strong>@lang('site.national_id_label')</strong> {{ $appointment->patient->national_id }}</p>
                    <p><strong>@lang('site.phone_label')</strong> {{ $appointment->patient->phone }}</p>
                    <p><strong>@lang('site.email_label')</strong> {{ $appointment->patient->user->email }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0">@lang('site.doctor')</h5></div>
                <div class="card-body">
                    <p><strong>@lang('site.name_label')</strong> {{ $appointment->doctor->user->name }}</p>
                    <p><strong>@lang('site.specialization_label')</strong> {{ $appointment->doctor->specialization }}</p>
                    <p><strong>@lang('site.phone_label')</strong> {{ $appointment->doctor->phone }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white"><h5 class="mb-0">@lang('site.booked_by')</h5></div>
        <div class="card-body">
            @if($appointment->receptionist)
                <p class="mb-0">{{ $appointment->receptionist->user->name }}
                    <small class="text-muted">@lang('site.receptionist_parenthetical')</small>
                </p>
            @else
                <p class="mb-0 text-muted">@lang('site.system_direct_booking')</p>
            @endif
        </div>
    </div>

    @if($appointment->diagnosis)
        <div class="card">
            <div class="card-header bg-warning"><h5 class="mb-0">@lang('site.diagnosis')</h5></div>
            <div class="card-body">
                <p><strong>@lang('site.symptoms_label')</strong> {{ $appointment->diagnosis->symptoms }}</p>
                <p><strong>@lang('site.diagnosis_label')</strong> {{ $appointment->diagnosis->diagnosis }}</p>
                @if($appointment->diagnosis->prescription)
                    <p><strong>@lang('site.prescription_label')</strong> {{ $appointment->diagnosis->prescription }}</p>
                @endif
                @if($appointment->diagnosis->notes)
                    <p><strong>@lang('site.notes_label')</strong> {{ $appointment->diagnosis->notes }}</p>
                @endif
                <small class="text-muted">
                    Recorded {{ $appointment->diagnosis->created_at->format('Y-m-d H:i') }}
                </small>
            </div>
        </div>
    @endif
</div>
@endsection