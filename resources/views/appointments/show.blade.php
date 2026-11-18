
@extends('layouts.app')

@section('title', __('site.appointment_details'))

@section('content')
@php
    $user = auth()->user();

    $isReceptionist = $user?->isReceptionist() ?? false;

    $canViewMedicalData = $user !== null && (
        $user->isAdmin()
        || $user->isDoctor()
        || $user->isPatient()
    );

    $status = $appointment->status;

    $statusConfig = match ($status) {
        'scheduled' => [
            'label' => __('site.scheduled'),
            'class' => 'ui-badge-scheduled status-scheduled',
            'icon' => 'bi-calendar-check',
        ],
        'completed' => [
            'label' => __('site.completed'),
            'class' => 'ui-badge-completed status-completed',
            'icon' => 'bi-check-circle',
        ],
        'cancelled' => [
            'label' => __('site.cancelled'),
            'class' => 'ui-badge-cancelled status-cancelled',
            'icon' => 'bi-x-circle',
        ],
        default => [
            'label' => __('site.unknown'),
            'class' => 'ui-badge-pending status-unknown',
            'icon' => 'bi-info-circle',
        ],
    };

    $appointmentDate = $appointment->appointment_date;
    $appointmentTime = $appointment->appointment_time;

    $patient = $appointment->patient;
    $doctor = $appointment->doctor;
    $diagnosis = $appointment->diagnosis;

    $canReschedule = $status === 'scheduled'
        && $user?->isReceptionist()
        && $user->receptionist !== null
        && (int) $appointment->receptionist_id === (int) $user->receptionist->id;

    $canCancel = $canReschedule;
@endphp

<div class="ui-page appointment-details container-fluid py-4">

    {{-- Page Header --}}
    <div class="ui-page-head page-header">
        <div>
            <h1 class="ui-title page-title">@lang('site.appointment_details')</h1>
            <p class="ui-sub page-subtitle">
                View appointment information and related records.
            </p>
        </div>

        <a href="{{ route('appointments.index') }}" class="back-btn">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            Back to Appointments
        </a>
    </div>

    {{-- @lang('site.appointment_overview') --}}
    <section class="ui-card panel hero-card">

        <div class="hero-top">
            <div>
                <div class="appointment-reference">
                    APPOINTMENT REFERENCE
                    <strong>#{{ $appointment->id }}</strong>
                </div>

                <h2 class="hero-title">@lang('site.appointment_overview')</h2>

                <p class="hero-description">
                    Review the current appointment schedule and status.
                </p>
            </div>

            <span class="ui-badge {{ $statusConfig['class'] }}">
                <i class="bi {{ $statusConfig['icon'] }}" aria-hidden="true"></i>
                {{ $statusConfig['label'] }}
            </span>
        </div>

        <div class="hero-stats">

            <div class="hero-stat">
                <div class="stat-label">@lang('site.appointment_date')</div>
                <div class="stat-value">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    {{ $appointmentDate?->translatedFormat('D, M d, Y') ?? 'Not set' }}
                </div>
            </div>

            <div class="hero-stat">
                <div class="stat-label">@lang('site.appointment_time')</div>
                <div class="stat-value">
                    <i class="bi bi-clock" aria-hidden="true"></i>

                    @if($appointmentTime)
                        {{ \Illuminate\Support\Carbon::parse($appointmentTime)->translatedFormat('h:i A') }}
                    @else
                        {{ $appointmentDate?->format('h:i A') ?? 'Not set' }}
                    @endif
                </div>
            </div>

            <div class="hero-stat">
                <div class="stat-label">@lang('site.booking_created')</div>
                <div class="stat-value">
                    <i class="bi bi-calendar-plus" aria-hidden="true"></i>
                    {{ $appointment->created_at?->translatedFormat('M d, Y') ?? '—' }}
                </div>
            </div>

        </div>
    </section>

    {{-- Patient and Doctor --}}
    <div class="row g-3 mb-1">

        {{-- Patient --}}
        <div class="col-12 col-lg-6">
            <section class="ui-card panel section-card">

                <div class="section-header">
                    <div class="section-icon">
                        <i class="bi bi-person" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h3>@lang('site.patient_information')</h3>
                        <p>@lang('site.patient_contact_description')</p>
                    </div>
                </div>

                <div class="section-body">

                    <div class="person-profile">
                        <div class="avatar">
                            <i class="bi bi-person-fill" aria-hidden="true"></i>
                        </div>

                        <div>
                            <div class="person-name">
                                {{ $patient?->user?->name ?? 'Unknown patient' }}
                            </div>

                            <div class="person-role">@lang('site.registered_patient')</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.patient_id')</span>
                        <span class="detail-value">
                            #{{ $appointment->patient_id }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.national_id')</span>
                        <span class="detail-value">
                            {{ $patient?->national_id ?? 'Not available' }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.phone')</span>
                        <span class="detail-value">
                            {{ $patient?->phone ?? 'Not available' }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.email')</span>
                        <span class="detail-value">
                            {{ $patient?->user?->email ?? 'Not available' }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.city')</span>
                        <span class="detail-value">
                            {{ $patient?->city?->name ?? 'Not available' }}
                        </span>
                    </div>

                </div>
            </section>
        </div>

        {{-- Doctor --}}
        <div class="col-12 col-lg-6">
            <section class="ui-card panel section-card">

                <div class="section-header">
                    <div class="section-icon">
                        <i class="bi bi-person-badge" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h3>@lang('site.doctor_information')</h3>
                        <p>@lang('site.assigned_healthcare_professional')</p>
                    </div>
                </div>

                <div class="section-body">

                    <div class="person-profile">
                        <div class="avatar doctor">
                            <i class="bi bi-heart-pulse-fill" aria-hidden="true"></i>
                        </div>

                        <div>
                            <div class="person-name">
                                Dr. {{ $doctor?->user?->name ?? 'Unknown doctor' }}
                            </div>

                            <div class="person-role">
                                {{ $doctor?->specialization ?? __('site.not_available') }}
                            </div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.doctor_id')</span>
                        <span class="detail-value">
                            #{{ $appointment->doctor_id }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.specialty')</span>
                        <span class="detail-value">
                            {{ $doctor?->specialization ?? 'Not available' }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.phone')</span>
                        <span class="detail-value">
                            {{ $doctor?->phone ?? 'Not available' }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.city')</span>
                        <span class="detail-value">
                            {{ $doctor?->city?->name ?? 'Not available' }}
                        </span>
                    </div>

                </div>
            </section>
        </div>

    </div>

    {{-- @lang('site.booking_information') --}}
    <section class="ui-card panel section-card">

        <div class="section-header">
            <div class="section-icon">
                <i class="bi bi-clipboard-check" aria-hidden="true"></i>
            </div>

            <div>
                <h3>@lang('site.booking_information')</h3>
                <p>@lang('site.booking_admin_description')</p>
            </div>
        </div>

        <div class="section-body">

            <div class="row g-3">

                <div class="col-12 col-md-6">
                    <div class="detail-row">
                        <span class="detail-label">@lang('site.appointment_id')</span>
                        <span class="detail-value">#{{ $appointment->id }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.booked_by')</span>
                        <span class="detail-value">
                            {{ $appointment->receptionist?->user?->name ?? 'System' }}
                        </span>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="detail-row">
                        <span class="detail-label">@lang('site.created_at')</span>
                        <span class="detail-value">
                            {{ $appointment->created_at?->translatedFormat('M d, Y - h:i A') ?? '—' }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">@lang('site.updated_at')</span>
                        <span class="detail-value">
                            {{ $appointment->updated_at?->translatedFormat('M d, Y - h:i A') ?? '—' }}
                        </span>
                    </div>
                </div>

            </div>

            @if(! $isReceptionist && $appointment->notes)
                <div class="mt-4">
                    <div class="detail-label fw-bold mb-2">
                        @lang('site.appointment_notes')
                    </div>

                    <div class="notes-content">{{ $appointment->notes }}</div>
                </div>
            @endif

        </div>
    </section>

    {{-- Medical Information: Authorized Roles Only --}}
    @if($canViewMedicalData)

        @if($diagnosis)
            <section class="ui-card panel section-card medical-card">

                <div class="section-header">
                    <div class="section-icon">
                        <i class="bi bi-clipboard2-pulse" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h3>@lang('site.medical_assessment')</h3>
                        <p>@lang('site.clinical_information')</p>
                    </div>
                </div>

                <div class="section-body">

                    <div class="medical-field">
                        <div class="medical-field-label">
                            <i class="bi bi-activity" aria-hidden="true"></i>
                            @lang('site.reported_symptoms')
                        </div>

                        <div class="medical-field-content">
                            {{ $diagnosis->symptoms ?: __('site.no_symptoms_recorded') }}
                        </div>
                    </div>

                    <div class="medical-field">
                        <div class="medical-field-label">
                            <i class="bi bi-clipboard2-check" aria-hidden="true"></i>
                            @lang('site.diagnosis')
                        </div>

                        <div class="medical-field-content">
                            {{ $diagnosis->diagnosis ?: __('site.no_diagnosis_recorded') }}
                        </div>
                    </div>

                    @if($diagnosis->prescription)
                        <div class="medical-field">
                            <div class="medical-field-label">
                                <i class="bi bi-prescription2" aria-hidden="true"></i>
                                @lang('site.prescription')
                            </div>

                            <div class="medical-field-content">
                                {{ $diagnosis->prescription }}
                            </div>
                        </div>
                    @endif

                    <div class="metadata">
                        <i class="bi bi-clock-history me-1" aria-hidden="true"></i>
                        @lang('site.recorded_on')
                        {{ $diagnosis->created_at?->translatedFormat('M d, Y - h:i A') ?? 'Unknown date' }}
                    </div>

                </div>
            </section>

        @elseif($status === 'completed')

            <div class="empty-medical mb-4">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                <div>
                    <strong>@lang('site.no_medical_assessment')</strong>
                    <p>
                        This appointment is marked as completed,
                        but no diagnosis has been recorded yet.
                    </p>
                </div>
            </div>

        @endif

    @endif

    {{-- @lang('site.appointment_actions') --}}
    @if($canCancel || $canReschedule)

        <section class="ui-card panel actions-card">

            <h3 class="actions-title">@lang('site.appointment_actions')</h3>

            <p class="actions-description">
                Manage this appointment. Cancellation cannot be treated as a normal edit.
            </p>

            @if($canReschedule)
                <a href="{{ route('appointments.edit', $appointment) }}" class="ui-btn ui-btn-primary mb-3">
                    <i class="bi bi-calendar2-week me-2" aria-hidden="true"></i>
                    @lang('site.reschedule_appointment')
                </a>
            @endif

            @if($canCancel)
            <form action="{{ route('appointments.destroy', $appointment) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to cancel appointment #{{ $appointment->id }}? This action cannot be undone.')">

                @csrf
                @method('DELETE')

                <button type="submit" class="ui-btn btn btn-danger">
                    <i class="bi bi-x-circle me-2" aria-hidden="true"></i>
                    @lang('site.cancel_appointment')
                </button>

            </form>
            @endif

            <div class="metadata">
                <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                @lang('site.authorized_users_only')
            </div>

        </section>

    @endif

</div>
@endsection
