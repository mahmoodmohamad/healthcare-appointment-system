@extends('layouts.app')
@section('title', $patient->user->name)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ $patient->user->name }}</h2>
            <p class="text-muted mb-0">Patient #{{ $patient->id }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">
                ← Back to Patients
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['total_appointments'] }}</h3>
                    <small>@lang('site.appointments')</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                    <small>@lang('site.completed')</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['scheduled'] }}</h3>
                    <small>@lang('site.scheduled')</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['cancelled'] }}</h3>
                    <small>@lang('site.cancelled')</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['total_diagnoses'] }}</h3>
                    <small>@lang('site.diagnoses')</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['distinct_doctors'] }}</h3>
                    <small>@lang('site.doctors')</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- @lang('site.profile') -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">@lang('site.personal_information')</h5></div>
                <div class="card-body">
                    <p><strong>@lang('site.name_label')</strong> {{ $patient->user->name }}</p>
                    <p><strong>@lang('site.email_label')</strong> {{ $patient->user->email }}</p>
                    <p><strong>@lang('site.national_id_label')</strong> {{ $patient->national_id }}</p>
                    <p><strong>@lang('site.phone_label')</strong> {{ $patient->phone }}</p>
                    <p><strong>@lang('site.gender_label')</strong> {{ $patient->gender ? ucfirst($patient->gender) : '—' }}</p>
                    <p><strong>@lang('site.birth_date_label')</strong>
                        {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('M j, Y') : '—' }}
                    </p>
                    <p><strong>@lang('site.city_label')</strong> {{ $patient->city->name ?? '—' }}</p>
                    <p><strong>@lang('site.country_label')</strong> {{ $patient->city->country->name ?? '—' }}</p>
                    <p><strong>@lang('site.registered_by')</strong>
                        {{ $patient->receptionist->user->name ?? 'Self / System' }}
                    </p>
                    <p class="mb-0"><strong>@lang('site.account_label')</strong>
                        @if($patient->user->activation)
                            <span class="badge bg-success">@lang('site.active')</span>
                        @else
                            <span class="badge bg-secondary">@lang('site.inactive')</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Appointment history -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">@lang('site.appointment_history')</h5>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>@lang('site.date')</th>
                                        <th>@lang('site.doctor')</th>
                                        <th>@lang('site.status')</th>
                                        <th>@lang('site.diagnosis')</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>
                                                {{ $appointment->appointment_date->format('Y-m-d') }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ $appointment->doctor->user->name ?? '—' }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $appointment->doctor->specialization ?? '' }}
                                                </small>
                                            </td>
                                            <td>
                                                @php
                                                    $badge = match($appointment->status) {
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger',
                                                        default     => 'primary',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badge }}">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($appointment->diagnosis)
                                                    <span class="badge bg-success">@lang('site.recorded')</span>
                                                @else
                                                    <span class="badge bg-secondary">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.appointments.show', $appointment) }}"
                                                   class="btn btn-sm btn-outline-primary">@lang('site.open')</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $appointments->links() }}
                        </div>
                    @else
                        <p class="text-center text-muted py-4 mb-0">@lang('site.no_appointments_yet')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection