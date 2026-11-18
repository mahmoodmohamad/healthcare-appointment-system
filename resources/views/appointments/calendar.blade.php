
@extends('layouts.app')

@section('title', 'Appointment Calendar')

@section('content')

@php
    $calendarStart = $date->copy()
        ->startOfMonth()
        ->startOfWeek(\Carbon\Carbon::SUNDAY);

    $calendarEnd = $date->copy()
        ->endOfMonth()
        ->endOfWeek(\Carbon\Carbon::SATURDAY);

    $currentDate = $calendarStart->copy();

    $today = now()->toDateString();

    $totalAppointments = collect($appointments)->flatten()->count();

    $scheduledCount = collect($appointments)->flatten()
        ->where('status', 'scheduled')->count();

    $completedCount = collect($appointments)->flatten()
        ->where('status', 'completed')->count();

    $cancelledCount = collect($appointments)->flatten()
        ->where('status', 'cancelled')->count();
@endphp


<div class="calendar-page container-fluid py-4">

    {{-- Page Header --}}
    <div class="calendar-header">
        <div>
            <h1 class="page-title">Appointment Calendar</h1>
            <p class="page-subtitle">
                View and manage scheduled appointments across the month.
            </p>
        </div>

        <div class="header-actions">
            <a href="{{ route('appointments.index') }}" class="btn btn-light">
                <i class="bi bi-list-ul me-1" aria-hidden="true"></i>
                List View
            </a>

            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
                New Appointment
            </a>
        </div>
    </div>

    {{-- Overview --}}
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-icon total">
                <i class="bi bi-calendar3" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-label">Total Appointments</div>
                <div class="stat-value">{{ $totalAppointments }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon scheduled">
                <i class="bi bi-clock-history" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-label">Scheduled</div>
                <div class="stat-value">{{ $scheduledCount }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="bi bi-check-circle" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $completedCount }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon cancelled">
                <i class="bi bi-x-circle" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-label">Cancelled</div>
                <div class="stat-value">{{ $cancelledCount }}</div>
            </div>
        </div>

    </div>

    {{-- Doctor Filter --}}
    <section class="panel filter-panel">
        <form method="GET"
              action="{{ route('appointments.calendar') }}"
              class="row align-items-end g-3">

            <input type="hidden" name="month" value="{{ $date->month }}">
            <input type="hidden" name="year" value="{{ $date->year }}">

            <div class="col-12 col-md-5 col-lg-4">
                <label for="doctorFilter" class="filter-label">
                    <i class="bi bi-person-badge me-1" aria-hidden="true"></i>
                    Filter by Doctor
                </label>

                <select name="doctor_id"
                        id="doctorFilter"
                        class="form-select">
                    <option value="">All Doctors</option>

                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}"
                            @selected((string) request('doctor_id') === (string) $doctor->id)>
                            Dr. {{ $doctor->user?->name ?? 'Unknown doctor' }}
                            — {{ $doctor->specialization }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-auto">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                    Apply Filter
                </button>
            </div>

            <div class="col-6 col-md-auto">
                <a href="{{ route('appointments.calendar', [
                    'month' => $date->month,
                    'year' => $date->year,
                ]) }}" class="btn btn-light w-100">
                    Reset
                </a>
            </div>

        </form>
    </section>

    {{-- Calendar --}}
    <section class="panel calendar-panel">

        <div class="calendar-toolbar">
            <div>
                <h2 class="month-title">
                    {{ $date->format('F Y') }}
                </h2>

                <div class="month-subtitle">
                    Monthly appointment overview
                </div>
            </div>

            <div class="calendar-navigation">
                <button type="button"
                        class="nav-button"
                        onclick="changeMonth(-1)">
                    <i class="bi bi-chevron-left" aria-hidden="true"></i>
                    Previous
                </button>

                <button type="button"
                        class="nav-button today-button"
                        onclick="changeMonth(0)">
                    Today
                </button>

                <button type="button"
                        class="nav-button"
                        onclick="changeMonth(1)">
                    Next
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="calendar-scroll">
            <div class="calendar-grid">

                {{-- Weekday Headers --}}
                <div class="calendar-weekdays">
                    @foreach([
                        'Sunday',
                        'Monday',
                        'Tuesday',
                        'Wednesday',
                        'Thursday',
                        'Friday',
                        'Saturday'
                    ] as $weekday)
                        <div class="weekday">
                            {{ $weekday }}
                        </div>
                    @endforeach
                </div>

                {{-- Calendar Days --}}
                <div class="calendar-days">

                    @while($currentDate <= $calendarEnd)

                        @php
                            $dateKey = $currentDate->format('Y-m-d');

                            $isToday = $dateKey === $today;

                            $isCurrentMonth =
                                $currentDate->month === $date->month
                                && $currentDate->year === $date->year;

                            $dayAppointments = $appointments[$dateKey] ?? collect();

                            $visibleAppointments = collect($dayAppointments)->take(3);

                            $remainingAppointments =
                                max(0, collect($dayAppointments)->count() - 3);
                        @endphp

                        <div class="calendar-day
                            {{ !$isCurrentMonth ? 'other-month' : '' }}
                            {{ $isToday ? 'today' : '' }}">

                            <div class="day-header">
                                <span class="day-number">
                                    {{ $currentDate->day }}
                                </span>

                                @if($isToday)
                                    <span class="today-label">TODAY</span>
                                @endif
                            </div>

                            <div class="appointment-list">

                                @foreach($visibleAppointments as $appointment)

                                    @php
                                        $statusClass = match($appointment->status) {
                                            'scheduled' => 'scheduled',
                                            'completed' => 'completed',
                                            'cancelled' => 'cancelled',
                                            default => 'unknown',
                                        };

                                        $timeValue = $appointment->appointment_time
                                            ?? $appointment->appointment_date?->format('H:i');

                                        $timeLabel = $timeValue
                                            ? \Illuminate\Support\Carbon::parse($timeValue)->format('h:i A')
                                            : 'Time not set';

                                        $patientName = $appointment->patient?->user?->name
                                            ?? 'Unknown patient';

                                        $doctorName = $appointment->doctor?->user?->name
                                            ?? 'Unknown doctor';
                                    @endphp

                                    <a href="{{ route('appointments.show', $appointment) }}"
                                       class="appointment-event {{ $statusClass }}"
                                       title="{{ $patientName }} — Dr. {{ $doctorName }} — {{ $timeLabel }}"
                                       aria-label="View appointment {{ $appointment->id }} for {{ $patientName }} at {{ $timeLabel }}">

                                        <span class="event-time">
                                            {{ $timeLabel }}
                                        </span>

                                        <span class="event-patient">
                                            {{ $patientName }}
                                        </span>

                                        <span class="event-doctor">
                                            Dr. {{ $doctorName }}
                                        </span>

                                    </a>

                                @endforeach

                                @if($remainingAppointments > 0)
                                    <span class="more-events">
                                        +{{ $remainingAppointments }} more
                                    </span>
                                @elseif(collect($dayAppointments)->isEmpty() && $isCurrentMonth)
                                    <span class="empty-day">
                                        No appointments
                                    </span>
                                @endif

                            </div>
                        </div>

                        @php
                            $currentDate->addDay();
                        @endphp

                    @endwhile

                </div>
            </div>
        </div>

        {{-- Calendar Legend --}}
        <div class="calendar-footer">

            <div class="legend">
                <div class="legend-item">
                    <span class="legend-dot scheduled"></span>
                    Scheduled
                </div>

                <div class="legend-item">
                    <span class="legend-dot completed"></span>
                    Completed
                </div>

                <div class="legend-item">
                    <span class="legend-dot cancelled"></span>
                    Cancelled
                </div>
            </div>

            <span class="scroll-hint">
                <i class="bi bi-arrows me-1" aria-hidden="true"></i>
                Scroll horizontally to view the full calendar
            </span>

        </div>

    </section>

</div>

<script>
    function changeMonth(delta) {
        const url = new URL(window.location.href);

        // Use the displayed calendar month as the navigation base.
        let month = {{ (int) $date->month }};
        let year = {{ (int) $date->year }};

        if (delta === 0) {
            const today = new Date();

            month = today.getMonth() + 1;
            year = today.getFullYear();
        } else {
            const target = new Date(year, month - 1 + delta, 1);

            month = target.getMonth() + 1;
            year = target.getFullYear();
        }

        url.searchParams.set('month', month);
        url.searchParams.set('year', year);

        // Preserve the selected doctor and other existing filters.
        window.location.href = url.toString();
    }
</script>

@endsection
