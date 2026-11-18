@extends('layouts.app')
@section('title', __('site.patient_details'))

@section('content')
<div class="ui-page container-fluid py-4">
    <div class="row">
        <!-- Header -->
        <div class="col-12">
            <div class="ui-page-head d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div><h1 class="ui-title">@lang('site.patient_profile')</h1><p class="ui-sub mb-0">{{ $patient->user->name }}</p></div>
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
                    <h5 class="mb-0">@lang('site.personal_information')</h5>
                </div>
                <div class="card-body">
                    <table class="ui-table table-borderless mb-0">
                        <tr>
                            <th>@lang('site.name_label')</th>
                            <td>{{ $patient->user->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('site.national_id_label')</th>
                            <td>{{ $patient->national_id }}</td>
                        </tr>
                        <tr>
                            <th>@lang('site.phone_label')</th>
                            <td>{{ $patient->phone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('site.email_label')</th>
                            <td>{{ $patient->user->email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('site.city_label')</th>
                            <td>{{ $patient->city->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('site.country_label')</th>
                            <td>{{ $patient->city->country->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('site.registered_label')</th>
                            <td>{{ $patient->created_at->translatedFormat('Y-m-d') }}</td>
                        </tr>
                        @if($patient->receptionist)
                        <tr>
                            <th>@lang('site.registered_by')</th>
                            <td>{{ $patient->receptionist->user->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- @lang('site.statistics') Card -->
            <div class="ui-card card">
                <div class="ui-card-head card-header">
                    <h5 class="mb-0">@lang('site.statistics')</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h3 class="text-primary">{{ $stats['total_appointments'] }}</h3>
                            <small class="text-muted">@lang('site.total_appointments')</small>
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
                            <small class="text-muted">@lang('site.diagnoses')</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- @lang('site.upcoming_appointments') -->
        <div class="col-md-8">
            <div class="ui-card card mb-4">
                <div class="ui-card-head card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@lang('site.upcoming_appointments')</h5>
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
                                        <th>@lang('site.date_time')</th>
                                        <th>@lang('site.doctor')</th>
                                        <th>@lang('site.status')</th>
                                        <th>@lang('site.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingAppointments as $appointment)
                                    <tr>
                                        <td data-label="@lang('site.date_time')">
                                            {{ $appointment->appointment_date->translatedFormat('Y-m-d') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                            </small>
                                        </td>
                                        <td data-label="@lang('site.doctor')">
                                            Dr. {{ $appointment->doctor->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $appointment->doctor->specialization }}</small>
                                        </td>
                                        <td data-label="Status">
                                            <span class="ui-badge ui-badge-scheduled">{{ match ($appointment->status) { 'scheduled' => __('site.scheduled'), 'completed' => __('site.completed'), 'cancelled' => __('site.cancelled'), default => __('site.unknown') } }}</span>
                                        </td>
                                        <td data-label="@lang('site.actions')">
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
                        <p class="text-muted text-center py-3">@lang('site.no_upcoming_appointments')</p>
                    @endif
                </div>
            </div>

            
        </div>
    </div>
</div>
@endsection