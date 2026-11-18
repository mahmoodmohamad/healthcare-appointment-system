@extends('layouts.app')

@section('title', __('site.receptionist_dashboard'))

@section('content')


<div class="ui-page container-fluid">

    {{-- Header --}}
    <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="ui-title">{{ $greeting }}, {{ $receptionistName }}</h1>
            <p class="ui-meta mb-0 mt-1">
                @if($todayAppointments > 0)
                    {{ $todayAppointments }} {{ \Illuminate\Support\Str::plural('appointment', $todayAppointments) }} scheduled for today.
                @else
                    No appointments scheduled for today.
                @endif
            </p>
        </div>

        <div class="ui-chip">
            <i class="bi bi-calendar3" aria-hidden="true"></i>
            <time datetime="{{ now()->toDateString() }}">{{ now()->translatedFormat('l, j F Y') }}</time>
        </div>
    </header>

    {{-- Primary actions --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('appointments.create') }}" class="ui-btn ui-btn ui-btn-primary">
            <i class="bi bi-calendar-plus" aria-hidden="true"></i>
            @lang('site.book_appointment')
        </a>
        <a href="{{ route('patients.create') }}" class="ui-btn ui-btn ui-btn-ghost">
            <i class="bi bi-person-plus" aria-hidden="true"></i>
            @lang('site.register_patient')
        </a>
    </div>

    {{-- Statistics --}}
    <section class="ui-stats mb-4" aria-label="@lang('site.key_numbers')">
        <a href="{{ route('patients.index') }}" class="ui-card ui-stat">
            <span class="ui-stat-icon ui-tone-blue"><i class="bi bi-people" aria-hidden="true"></i></span>
            <span>
                <span class="ui-stat-number d-block">{{ number_format($totalPatients) }}</span>
                <span class="ui-stat-label d-block">@lang('site.registered_patients')</span>
                <span class="ui-stat-note">@lang('site.registered_by_you')</span>
            </span>
        </a>

        <a href="{{ route('appointments.index') }}" class="ui-card ui-stat">
            <span class="ui-stat-icon ui-tone-green"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
            <span>
                <span class="ui-stat-number d-block">{{ number_format($totalAppointments) }}</span>
                <span class="ui-stat-label d-block">@lang('site.total_appointments')</span>
                <span class="ui-stat-note">@lang('site.created_by_you')</span>
            </span>
        </a>

        <div class="ui-card ui-stat">
            <span class="ui-stat-icon ui-tone-orange"><i class="bi bi-calendar-day" aria-hidden="true"></i></span>
            <span>
                <span class="ui-stat-number d-block">{{ number_format($todayBookings) }}</span>
                <span class="ui-stat-label d-block">@lang('site.booked_today')</span>
                <span class="ui-stat-note">@lang('site.bookings_made_today')</span>
            </span>
        </div>

        <a href="{{ route('appointments.calendar') }}" class="ui-card ui-stat">
            <span class="ui-stat-icon ui-tone-teal"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
            <span>
                <span class="ui-stat-number d-block">{{ number_format($upcomingAppointments) }}</span>
                <span class="ui-stat-label d-block">@lang('site.upcoming')</span>
                <span class="ui-stat-note">@lang('site.today_or_later')</span>
            </span>
        </a>
    </section>

    <div class="ui-grid">

        {{-- Main column --}}
        <div class="ui-stack">

            {{-- @lang('site.upcoming_appointments') --}}
            <section class="ui-card" aria-labelledby="rd-upcoming">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="rd-upcoming">@lang('site.upcoming_appointments')</h2>
                        <p class="ui-meta mb-0">@lang('site.next_scheduled_patient_visits')</p>
                    </div>
                    <a href="{{ route('appointments.index') }}" class="ui-link">
                        View all <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if($upcomingAppointmentsList->isNotEmpty())
                    @foreach($upcomingAppointmentsList as $appointment)
                        @php $isToday = (bool) $appointment->appointment_date?->isToday(); @endphp
                        <a href="{{ route('appointments.show', $appointment) }}" class="ui-row"
                           aria-label="@lang('site.view_appointment_for', ['name' => $appointment->patient_name])">
                            <div class="ui-chip-block {{ $isToday ? 'is-today' : '' }}">
                                <strong>{{ $appointment->appointment_date?->translatedFormat('d') ?? '—' }}</strong>
                                <span>{{ $appointment->appointment_date?->translatedFormat('M') ?? '' }}</span>
                            </div>

                            <div class="ui-row-main">
                                <strong>
                                    {{ $appointment->patient_name }}
                                    @if($isToday)<span class="ui-badge ui-badge-scheduled">@lang('site.today')</span>@endif
                                </strong>
                                <div class="ui-meta">
                                    <span><i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $appointment->time_label }}</span>
                                    <span><i class="bi bi-person-badge me-1" aria-hidden="true"></i>@lang('site.dr_prefix') {{ $appointment->doctor_name }}</span>
                                </div>
                            </div>

                            <i class="bi bi-chevron-right ui-meta" aria-hidden="true"></i>
                        </a>
                    @endforeach
                @else
                    <div class="ui-empty">
                        <div class="ui-empty-icon ui-tone-teal"><i class="bi bi-calendar2-check" aria-hidden="true"></i></div>
                        <strong>@lang('site.no_upcoming_appointments')</strong>
                        <p>@lang('site.booked_visits_appear_here')</p>
                        <a href="{{ route('appointments.create') }}" class="ui-btn ui-btn ui-btn-primary">@lang('site.book_appointment')</a>
                    </div>
                @endif

                <div class="ui-card-foot d-flex flex-wrap justify-content-between gap-2">
                    <span><i class="bi bi-check-circle me-1" aria-hidden="true"></i>{{ $todayAppointments }} @lang('site.scheduled_today')</span>
                    <span>{{ $completedAppointments }} completed</span>
                </div>
            </section>

            {{-- Recent patients --}}
            <section class="ui-card" aria-labelledby="rd-recent">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="rd-recent">@lang('site.recently_registered_patients')</h2>
                        <p class="ui-meta mb-0">@lang('site.latest_patients_added')</p>
                    </div>
                    <a href="{{ route('patients.index') }}" class="ui-link">
                        View all <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if($recentPatients->isNotEmpty())
                    @foreach($recentPatients as $patient)
                        <a href="{{ route('patients.show', $patient) }}" class="ui-row">
                            <span class="ui-avatar" aria-hidden="true">{{ $patient->initials ?: 'P' }}</span>
                            <div class="ui-row-main">
                                <strong>{{ $patient->display_name }}</strong>
                                <div class="ui-meta">
                                    <span>@lang('site.registered') {{ $patient->registered_label }}</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right ui-meta" aria-hidden="true"></i>
                        </a>
                    @endforeach
                @else
                    <div class="ui-empty">
                        <div class="ui-empty-icon ui-tone-blue"><i class="bi bi-people" aria-hidden="true"></i></div>
                        <strong>@lang('site.no_patients_registered_yet')</strong>
                        <p>@lang('site.patients_you_register_appear_here')</p>
                        <a href="{{ route('patients.create') }}" class="ui-btn ui-btn ui-btn-primary">@lang('site.register_first_patient')</a>
                    </div>
                @endif
            </section>
        </div>

        {{-- Side column --}}
        <aside class="ui-stack">

            <section class="ui-card" aria-labelledby="rd-actions">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="rd-actions">@lang('site.quick_actions')</h2>
                        <p class="ui-meta mb-0">@lang('site.common_reception_tasks')</p>
                    </div>
                </div>

                <a href="{{ route('patients.create') }}" class="ui-row">
                    <span class="ui-stat-icon ui-tone-blue"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
                    <div class="ui-row-main"><strong>@lang('site.register_patient')</strong><span class="ui-meta">@lang('site.add_new_patient_record')</span></div>
                    <i class="bi bi-chevron-right ui-meta" aria-hidden="true"></i>
                </a>

                <a href="{{ route('appointments.create') }}" class="ui-row">
                    <span class="ui-stat-icon ui-tone-green"><i class="bi bi-calendar-plus" aria-hidden="true"></i></span>
                    <div class="ui-row-main"><strong>@lang('site.book_appointment')</strong><span class="ui-meta">@lang('site.schedule_patient_visit')</span></div>
                    <i class="bi bi-chevron-right ui-meta" aria-hidden="true"></i>
                </a>

                <a href="{{ route('patients.index') }}" class="ui-row">
                    <span class="ui-stat-icon ui-tone-teal"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <div class="ui-row-main"><strong>@lang('site.find_patient')</strong><span class="ui-meta">@lang('site.search_patient_directory')</span></div>
                    <i class="bi bi-chevron-right ui-meta" aria-hidden="true"></i>
                </a>

                <a href="{{ route('appointments.calendar') }}" class="ui-row">
                    <span class="ui-stat-icon ui-tone-orange"><i class="bi bi-calendar3" aria-hidden="true"></i></span>
                    <div class="ui-row-main"><strong>@lang('site.calendar')</strong><span class="ui-meta">@lang('site.see_monthly_schedule')</span></div>
                    <i class="bi bi-chevron-right ui-meta" aria-hidden="true"></i>
                </a>
            </section>

            <section class="ui-card" aria-labelledby="ui-row">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="rd-overview">@lang('site.today_at_a_glance')</h2>
                        <p class="ui-meta mb-0">@lang('site.your_appointment_activity')</p>
                    </div>
                </div>

                <div class="ui-row">
                    <span><i class="bi bi-calendar-event me-2" aria-hidden="true"></i>@lang('site.appointments_today')</span>
                    <strong>{{ $todayAppointments }}</strong>
                </div>
                <div class="ui-row">
                    <span><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>@lang('site.booked_today')</span>
                    <strong>{{ $todayBookings }}</strong>
                </div>
                <div class="ui-row">
                    <span><i class="bi bi-check-circle me-2" aria-hidden="true"></i>@lang('site.completed')</span>
                    <strong>{{ $completedAppointments }}</strong>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection