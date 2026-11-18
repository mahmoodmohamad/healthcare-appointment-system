@extends('layouts.app')

@section('title', 'Receptionist Dashboard')

@section('content')


<div class="rd container-fluid py-4">

    {{-- Header --}}
    <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="rd-title">{{ $greeting }}, {{ $receptionistName }}</h1>
            <p class="rd-meta mb-0 mt-1">
                @if($todayAppointments > 0)
                    {{ $todayAppointments }} {{ \Illuminate\Support\Str::plural('appointment', $todayAppointments) }} scheduled for today.
                @else
                    No appointments scheduled for today.
                @endif
            </p>
        </div>

        <div class="rd-date">
            <i class="bi bi-calendar3" aria-hidden="true"></i>
            <time datetime="{{ now()->toDateString() }}">{{ now()->format('l, j F Y') }}</time>
        </div>
    </header>

    {{-- Primary actions --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('appointments.create') }}" class="rd-btn rd-btn-primary">
            <i class="bi bi-calendar-plus" aria-hidden="true"></i>
            Book appointment
        </a>
        <a href="{{ route('patients.create') }}" class="rd-btn rd-btn-ghost">
            <i class="bi bi-person-plus" aria-hidden="true"></i>
            Register patient
        </a>
    </div>

    {{-- Statistics --}}
    <section class="rd-stats mb-4" aria-label="Key numbers">
        <a href="{{ route('patients.index') }}" class="rd-card rd-stat">
            <span class="rd-stat-icon tone-blue"><i class="bi bi-people" aria-hidden="true"></i></span>
            <span>
                <span class="rd-stat-number d-block">{{ number_format($totalPatients) }}</span>
                <span class="rd-stat-label d-block">Registered patients</span>
                <span class="rd-stat-note">Registered by you</span>
            </span>
        </a>

        <a href="{{ route('appointments.index') }}" class="rd-card rd-stat">
            <span class="rd-stat-icon tone-green"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
            <span>
                <span class="rd-stat-number d-block">{{ number_format($totalAppointments) }}</span>
                <span class="rd-stat-label d-block">Total appointments</span>
                <span class="rd-stat-note">Created by you</span>
            </span>
        </a>

        <div class="rd-card rd-stat">
            <span class="rd-stat-icon tone-orange"><i class="bi bi-calendar-day" aria-hidden="true"></i></span>
            <span>
                <span class="rd-stat-number d-block">{{ number_format($todayBookings) }}</span>
                <span class="rd-stat-label d-block">Booked today</span>
                <span class="rd-stat-note">Bookings made today</span>
            </span>
        </div>

        <a href="{{ route('appointments.calendar') }}" class="rd-card rd-stat">
            <span class="rd-stat-icon tone-purple"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
            <span>
                <span class="rd-stat-number d-block">{{ number_format($upcomingAppointments) }}</span>
                <span class="rd-stat-label d-block">Upcoming</span>
                <span class="rd-stat-note">Today or later</span>
            </span>
        </a>
    </section>

    <div class="rd-grid">

        {{-- Main column --}}
        <div class="rd-stack">

            {{-- Upcoming appointments --}}
            <section class="rd-card" aria-labelledby="rd-upcoming">
                <div class="rd-panel-head">
                    <div>
                        <h2 class="rd-panel-title" id="rd-upcoming">Upcoming appointments</h2>
                        <p class="rd-meta mb-0">Next scheduled patient visits</p>
                    </div>
                    <a href="{{ route('appointments.index') }}" class="rd-link">
                        View all <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if($upcomingAppointmentsList->isNotEmpty())
                    @foreach($upcomingAppointmentsList as $appointment)
                        @php $isToday = (bool) $appointment->appointment_date?->isToday(); @endphp
                        <a href="{{ route('appointments.show', $appointment) }}" class="rd-row"
                           aria-label="View appointment for {{ $appointment->patient_name }}">
                            <div class="rd-date-block {{ $isToday ? 'is-today' : '' }}">
                                <strong>{{ $appointment->appointment_date?->format('d') ?? '—' }}</strong>
                                <span>{{ $appointment->appointment_date?->format('M') ?? '' }}</span>
                            </div>

                            <div class="rd-row-main">
                                <strong>
                                    {{ $appointment->patient_name }}
                                    @if($isToday)<span class="rd-badge">Today</span>@endif
                                </strong>
                                <div class="rd-row-meta">
                                    <span><i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $appointment->time_label }}</span>
                                    <span><i class="bi bi-person-badge me-1" aria-hidden="true"></i>Dr. {{ $appointment->doctor_name }}</span>
                                </div>
                            </div>

                            <i class="bi bi-chevron-right rd-chevron" aria-hidden="true"></i>
                        </a>
                    @endforeach
                @else
                    <div class="rd-empty">
                        <div class="rd-empty-icon tone-teal"><i class="bi bi-calendar2-check" aria-hidden="true"></i></div>
                        <strong>No upcoming appointments</strong>
                        <p>Booked visits will show up here, soonest first.</p>
                        <a href="{{ route('appointments.create') }}" class="rd-btn rd-btn-primary">Book appointment</a>
                    </div>
                @endif

                <div class="rd-foot">
                    <span><i class="bi bi-check-circle me-1" aria-hidden="true"></i>{{ $todayAppointments }} scheduled today</span>
                    <span>{{ $completedAppointments }} completed</span>
                </div>
            </section>

            {{-- Recent patients --}}
            <section class="rd-card" aria-labelledby="rd-recent">
                <div class="rd-panel-head">
                    <div>
                        <h2 class="rd-panel-title" id="rd-recent">Recently registered patients</h2>
                        <p class="rd-meta mb-0">Latest patients you added</p>
                    </div>
                    <a href="{{ route('patients.index') }}" class="rd-link">
                        View all <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if($recentPatients->isNotEmpty())
                    @foreach($recentPatients as $patient)
                        <a href="{{ route('patients.show', $patient) }}" class="rd-row">
                            <span class="rd-avatar" aria-hidden="true">{{ $patient->initials ?: 'P' }}</span>
                            <div class="rd-row-main">
                                <strong>{{ $patient->display_name }}</strong>
                                <div class="rd-row-meta">
                                    <span>Registered {{ $patient->registered_label }}</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right rd-chevron" aria-hidden="true"></i>
                        </a>
                    @endforeach
                @else
                    <div class="rd-empty">
                        <div class="rd-empty-icon tone-blue"><i class="bi bi-people" aria-hidden="true"></i></div>
                        <strong>No patients registered yet</strong>
                        <p>Patients you register will appear here.</p>
                        <a href="{{ route('patients.create') }}" class="rd-btn rd-btn-primary">Register first patient</a>
                    </div>
                @endif
            </section>
        </div>

        {{-- Side column --}}
        <aside class="rd-stack">

            <section class="rd-card" aria-labelledby="rd-actions">
                <div class="rd-panel-head">
                    <div>
                        <h2 class="rd-panel-title" id="rd-actions">Quick actions</h2>
                        <p class="rd-meta mb-0">Common reception tasks</p>
                    </div>
                </div>

                <a href="{{ route('patients.create') }}" class="rd-row">
                    <span class="rd-qa-icon tone-blue"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
                    <div class="rd-row-main"><strong>Register patient</strong><span class="rd-qa-sub">Add a new patient record</span></div>
                    <i class="bi bi-chevron-right rd-chevron" aria-hidden="true"></i>
                </a>

                <a href="{{ route('appointments.create') }}" class="rd-row">
                    <span class="rd-qa-icon tone-green"><i class="bi bi-calendar-plus" aria-hidden="true"></i></span>
                    <div class="rd-row-main"><strong>Book appointment</strong><span class="rd-qa-sub">Schedule a patient visit</span></div>
                    <i class="bi bi-chevron-right rd-chevron" aria-hidden="true"></i>
                </a>

                <a href="{{ route('patients.index') }}" class="rd-row">
                    <span class="rd-qa-icon tone-purple"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <div class="rd-row-main"><strong>Find a patient</strong><span class="rd-qa-sub">Search the patient directory</span></div>
                    <i class="bi bi-chevron-right rd-chevron" aria-hidden="true"></i>
                </a>

                <a href="{{ route('appointments.calendar') }}" class="rd-row">
                    <span class="rd-qa-icon tone-orange"><i class="bi bi-calendar3" aria-hidden="true"></i></span>
                    <div class="rd-row-main"><strong>Calendar</strong><span class="rd-qa-sub">See the monthly schedule</span></div>
                    <i class="bi bi-chevron-right rd-chevron" aria-hidden="true"></i>
                </a>
            </section>

            <section class="rd-card" aria-labelledby="rd-overview">
                <div class="rd-panel-head">
                    <div>
                        <h2 class="rd-panel-title" id="rd-overview">Today at a glance</h2>
                        <p class="rd-meta mb-0">Your appointment activity</p>
                    </div>
                </div>

                <div class="rd-overview">
                    <span><i class="bi bi-calendar-event me-2" aria-hidden="true"></i>Appointments today</span>
                    <strong>{{ $todayAppointments }}</strong>
                </div>
                <div class="rd-overview">
                    <span><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>Booked today</span>
                    <strong>{{ $todayBookings }}</strong>
                </div>
                <div class="rd-overview">
                    <span><i class="bi bi-check-circle me-2" aria-hidden="true"></i>Completed</span>
                    <strong>{{ $completedAppointments }}</strong>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection