@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
@php
    $user     = auth()->user();
    $hour     = now()->hour;
    $greeting = $hour < 12 ? __('Good morning') : ($hour < 18 ? __('Good afternoon') : __('Good evening'));
    $total    = $stats['today_appointments'];
    $percent  = $total > 0 ? round($stats['today_completed'] / $total * 100) : 0;
@endphp

<div class="container ui-page">

    {{-- Header --}}
    <header class="ui-page-head">
        <div>
            <h1 class="ui-title">{{ $greeting }}, {{ $user->name }}</h1>
            <p class="ui-sub">{{ __('Here is your schedule and patient overview for today.') }}</p>
        </div>
        <span class="ui-chip">
            <i class="bi bi-calendar3" aria-hidden="true"></i>
            <time datetime="{{ today()->toDateString() }}">{{ now()->translatedFormat('l, d M Y') }}</time>
        </span>
    </header>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <section class="ui-stats" aria-label="{{ __('Today at a glance') }}">
        <a class="ui-card ui-stat" href="{{ route('doctor.appointments.index', ['filter' => 'today']) }}">
            <span class="ui-stat-icon ui-tone-teal"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
            <div>
                <div class="ui-stat-number">{{ $stats['today_appointments'] }}</div>
                <div class="ui-stat-label">{{ __("Today's appointments") }}</div>
            </div>
        </a>

        <a class="ui-card ui-stat" href="{{ route('doctor.appointments.index', ['filter' => 'today', 'status' => 'scheduled']) }}">
            <span class="ui-stat-icon ui-tone-orange"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
            <div>
                <div class="ui-stat-number">{{ $stats['today_pending'] }}</div>
                <div class="ui-stat-label">{{ __('Waiting') }}</div>
            </div>
        </a>

        <a class="ui-card ui-stat" href="{{ route('doctor.appointments.index', ['filter' => 'today', 'status' => 'completed']) }}">
            <span class="ui-stat-icon ui-tone-green"><i class="bi bi-check2-circle" aria-hidden="true"></i></span>
            <div>
                <div class="ui-stat-number">{{ $stats['today_completed'] }}</div>
                <div class="ui-stat-label">{{ __('Completed today') }}</div>
            </div>
        </a>

        <div class="ui-card ui-stat">
            <span class="ui-stat-icon ui-tone-blue"><i class="bi bi-people" aria-hidden="true"></i></span>
            <div>
                <div class="ui-stat-number">{{ $stats['total_patients'] }}</div>
                <div class="ui-stat-label">{{ __('Total patients') }}</div>
            </div>
        </div>
    </section>

    <div class="ui-grid">

        {{-- Today's schedule --}}
        <section class="ui-card" aria-labelledby="today-title">
            <div class="ui-card-head">
                <div>
                    <h2 class="ui-card-title" id="today-title">{{ __("Today's schedule") }}</h2>
                    <p class="ui-card-sub">{{ trans_choice(':count appointment|:count appointments', $total, ['count' => $total]) }}</p>
                </div>
                <a class="ui-link" href="{{ route('doctor.appointments.index') }}">{{ __('View all') }}</a>
            </div>

            @if ($total > 0)
                <div class="px-4 pb-3">
                    <div class="d-flex justify-content-between ui-meta mb-1">
                        <span>{{ __('Progress') }}</span>
                        <span>{{ $stats['today_completed'] }}/{{ $total }}</span>
                    </div>
                    <div class="ui-progress" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"
                         aria-label="{{ __('Completed appointments today') }}">
                        <span style="width: {{ $percent }}%"></span>
                    </div>
                </div>
            @endif

            @forelse ($todayAppointments as $appointment)
                @php
                    $patientName = $appointment->patient?->user?->name ?? __('Unknown patient');
                    $initials    = collect(preg_split('/\s+/', trim($patientName)))->filter()->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
                @endphp
                <div class="ui-row">
                    <span class="ui-time">{{ $appointment->formatted_time }}</span>
                    <span class="ui-avatar" aria-hidden="true">{{ $initials }}</span>

                    <div class="ui-row-main">
                        <a href="{{ route('doctor.appointments.show', $appointment) }}"><strong>{{ $patientName }}</strong></a>
                        <span class="ui-badge ui-badge-{{ $appointment->status }} mt-1">{{ __(ucfirst($appointment->status)) }}</span>
                    </div>

                    <div class="ui-row-actions">
                        @if ($appointment->status !== 'cancelled')
                            @if ($appointment->diagnosis)
                                <a class="ui-btn ui-btn-ghost ui-btn-sm" href="{{ route('doctor.appointments.show', $appointment) }}">
                                    <i class="bi bi-file-medical" aria-hidden="true"></i> {{ __('View diagnosis') }}
                                </a>
                            @else
                                <a class="ui-btn ui-btn-primary ui-btn-sm" href="{{ route('doctor.diagnosis.create', $appointment) }}">
                                    <i class="bi bi-plus-lg" aria-hidden="true"></i> {{ __('Add diagnosis') }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="ui-empty">
                    <span class="ui-empty-icon ui-tone-teal"><i class="bi bi-calendar-x" aria-hidden="true"></i></span>
                    <strong>{{ __('No appointments today') }}</strong>
                    <p>{{ __('Enjoy the free day, or check your upcoming appointments.') }}</p>
                    <a class="ui-btn ui-btn-ghost" href="{{ route('doctor.appointments.index', ['filter' => 'upcoming']) }}">
                        {{ __('View upcoming') }}
                    </a>
                </div>
            @endforelse
        </section>

        {{-- Upcoming (next 7 days) --}}
        <div class="ui-stack">
            <section class="ui-card" aria-labelledby="upcoming-title">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="upcoming-title">{{ __('Next 7 days') }}</h2>
                        <p class="ui-card-sub">{{ __('Upcoming scheduled appointments') }}</p>
                    </div>
                </div>

                @forelse ($upcomingAppointments as $appointment)
                    <a class="ui-row" href="{{ route('doctor.appointments.show', $appointment) }}">
                        <span class="ui-date">
                            <strong>{{ $appointment->appointment_date->format('d') }}</strong>
                            <span>{{ $appointment->appointment_date->translatedFormat('M') }}</span>
                        </span>
                        <div class="ui-row-main">
                            <strong>{{ $appointment->patient?->user?->name ?? __('Unknown patient') }}</strong>
                            <span class="ui-meta">
                                <i class="bi bi-clock" aria-hidden="true"></i> {{ $appointment->formatted_time }}
                            </span>
                        </div>
                        <i class="bi bi-chevron-right text-muted" aria-hidden="true"></i>
                    </a>
                @empty
                    <div class="ui-empty">
                        <span class="ui-empty-icon ui-tone-blue"><i class="bi bi-calendar-event" aria-hidden="true"></i></span>
                        <strong>{{ __('Nothing scheduled') }}</strong>
                        <p>{{ __('No appointments in the next 7 days.') }}</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</div>
@endsection