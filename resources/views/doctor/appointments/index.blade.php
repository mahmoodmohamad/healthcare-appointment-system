@extends('layouts.app')

@section('title', __('My appointments'))

@section('content')
@php
    $activeFilter = $filters['filter'] ?? '';

    $tabs = [
        ''         => __('All'),
        'today'    => __('Today'),
        'upcoming' => __('Upcoming'),
        'past'     => __('Past'),
    ];

    $hasFilters = collect($filters)->filter()->isNotEmpty();
    $total      = $appointments->total();
@endphp

<div class="container-fluid ui-page">

    {{-- Header --}}
    <header class="ui-page-head">
        <div>
            <h1 class="ui-title">{{ __('My appointments') }}</h1>
            <p class="ui-sub">{{ __('Review your schedule and record diagnoses.') }}</p>
        </div>
        <a class="ui-btn ui-btn-ghost" href="{{ route('doctor.dashboard') }}">
            <i class="bi bi-speedometer2" aria-hidden="true"></i> {{ __('Dashboard') }}
        </a>
    </header>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Quick filter tabs --}}
    <nav class="ui-tabs" aria-label="{{ __('Filter by period') }}">
        @foreach ($tabs as $key => $label)
            @php
                $params = request()->except(['page', 'filter']);
                if ($key !== '') {
                    $params['filter'] = $key;
                }
            @endphp
            <a class="ui-tab {{ $activeFilter === $key ? 'is-active' : '' }}"
               href="{{ route('doctor.appointments.index', $params) }}"
               @if ($activeFilter === $key) aria-current="page" @endif>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    {{-- Filters --}}
    <section class="ui-card mb-4" aria-label="{{ __('Filters') }}">
        <form method="GET" action="{{ route('doctor.appointments.index') }}" class="ui-filters">
            @if ($activeFilter !== '')
                <input type="hidden" name="filter" value="{{ $activeFilter }}">
            @endif

            <div class="ui-field">
                <label for="status">{{ __('Status') }}</label>
                <select id="status" name="status" class="ui-input">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach (['scheduled', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ __(ucfirst($status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="ui-field">
                <label for="date">{{ __('Date') }}</label>
                <input id="date" type="date" name="date" class="ui-input"
                       value="{{ $filters['date'] ?? '' }}">
            </div>

            <div class="ui-filter-actions">
                <button type="submit" class="ui-btn ui-btn-primary">
                    <i class="bi bi-funnel" aria-hidden="true"></i> {{ __('Apply') }}
                </button>
                @if ($hasFilters)
                    <a class="ui-btn ui-btn-ghost" href="{{ route('doctor.appointments.index') }}">
                        {{ __('Clear') }}
                    </a>
                @endif
            </div>
        </form>
    </section>

    {{-- List --}}
    <section class="ui-card" aria-labelledby="list-title">
        <div class="ui-card-head">
            <div>
                <h2 class="ui-card-title" id="list-title">{{ __('Appointments') }}</h2>
                <p class="ui-card-sub">
                    {{ trans_choice(':count appointment|:count appointments', $total, ['count' => $total]) }}
                </p>
            </div>
        </div>

        @if ($appointments->isNotEmpty())
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <caption class="visually-hidden">{{ __('Your appointments') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Date & time') }}</th>
                            <th scope="col">{{ __('Patient') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Diagnosis') }}</th>
                            <th scope="col" class="ui-col-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            @php
                                $patientName = $appointment->patient?->user?->name ?? __('Unknown patient');
                                $isCancelled = $appointment->status === 'cancelled';
                                $initials    = collect(preg_split('/\s+/', trim($patientName)))
                                    ->filter()->take(2)
                                    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('');
                            @endphp
                            <tr>
                                <td data-label="{{ __('Date & time') }}">
                                    <strong>{{ $appointment->appointment_date?->translatedFormat('D, d M Y') }}</strong>
                                    <div class="ui-meta">
                                        <i class="bi bi-clock" aria-hidden="true"></i>
                                        {{ $appointment->formatted_time }}
                                    </div>
                                </td>

                                <td data-label="{{ __('Patient') }}">
                                    <div class="ui-cell-person">
                                        <span class="ui-avatar" aria-hidden="true">{{ $initials ?: '?' }}</span>
                                        <div>
                                            <a href="{{ route('doctor.appointments.show', $appointment) }}">
                                                <strong>{{ $patientName }}</strong>
                                            </a>
                                            <div class="ui-meta">{{ $appointment->patient?->national_id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="{{ __('Status') }}">
                                    <span class="ui-badge ui-badge-{{ $appointment->status }}">
                                        {{ __(ucfirst($appointment->status)) }}
                                    </span>
                                </td>

                                <td data-label="{{ __('Diagnosis') }}">
                                    @if ($isCancelled)
                                        <span class="ui-meta">—</span>
                                    @elseif ($appointment->diagnosis_exists)
                                        <span class="ui-badge ui-badge-completed">{{ __('Recorded') }}</span>
                                    @else
                                        <span class="ui-badge ui-badge-pending">{{ __('Pending') }}</span>
                                    @endif
                                </td>

                                <td class="ui-col-end">
                                    <div class="ui-row-actions">
                                        <a class="ui-btn ui-btn-ghost ui-btn-sm"
                                           href="{{ route('doctor.appointments.show', $appointment) }}"
                                           aria-label="{{ __('View appointment for :name', ['name' => $patientName]) }}">
                                            <i class="bi bi-eye" aria-hidden="true"></i> {{ __('View') }}
                                        </a>

                                        @if (! $isCancelled && ! $appointment->diagnosis_exists)
                                            <a class="ui-btn ui-btn-primary ui-btn-sm"
                                               href="{{ route('doctor.diagnosis.create', $appointment) }}"
                                               aria-label="{{ __('Add diagnosis for :name', ['name' => $patientName]) }}">
                                                <i class="bi bi-plus-lg" aria-hidden="true"></i> {{ __('Add diagnosis') }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ui-card-foot ui-pagination">
                <span>
                    {{ __('Showing :from–:to of :total', [
                        'from'  => $appointments->firstItem(),
                        'to'    => $appointments->lastItem(),
                        'total' => $total,
                    ]) }}
                </span>
                @if ($appointments->hasPages())
                    <nav aria-label="{{ __('Pagination') }}">{{ $appointments->links() }}</nav>
                @endif
            </div>
        @else
            <div class="ui-empty">
                <span class="ui-empty-icon ui-tone-teal"><i class="bi bi-calendar-x" aria-hidden="true"></i></span>
                @if ($hasFilters)
                    <strong>{{ __('No appointments match your filters') }}</strong>
                    <p>{{ __('Try a different period, status or date.') }}</p>
                    <a class="ui-btn ui-btn-ghost" href="{{ route('doctor.appointments.index') }}">
                        {{ __('Clear filters') }}
                    </a>
                @else
                    <strong>{{ __('No appointments yet') }}</strong>
                    <p>{{ __('Appointments booked for you will show up here.') }}</p>
                    <a class="ui-btn ui-btn-ghost" href="{{ route('doctor.dashboard') }}">
                        {{ __('Back to dashboard') }}
                    </a>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection