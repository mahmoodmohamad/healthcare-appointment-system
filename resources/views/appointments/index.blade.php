
@extends('layouts.app')

@section('title', 'Appointments')

@section('content')

<div class="ui-page appointments-page container-fluid py-4">

    {{-- Page Header --}}
    <div class="ui-page-head page-header">
        <div>
            <h1 class="ui-title page-title">Appointments</h1>
            <p class="ui-sub page-subtitle">
                Manage patient appointments and track their status.
            </p>
        </div>

        <div class="header-actions d-flex align-items-center gap-2">
            <a href="{{ route('appointments.calendar') }}"
               class="ui-btn ui-btn-ghost btn btn-light">
                <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                Calendar View
            </a>

            <a href="{{ route('appointments.create') }}"
               class="ui-btn ui-btn-primary btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
                New Appointment
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4"
             role="alert">
            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation / Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <strong>Please review the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filters --}}
    <section class="ui-card panel filter-panel" aria-labelledby="filters-heading">
        <h2 class="section-heading" id="filters-heading">
            <i class="bi bi-funnel me-2 text-primary" aria-hidden="true"></i>
            Filter Appointments
        </h2>

        <form method="GET" action="{{ route('appointments.index') }}">
            <div class="row g-3 align-items-end">

                <div class="col-12 col-md-4 col-lg-3">
                    <label for="status" class="form-label">Appointment Status</label>

                    <select name="status" id="status" class="ui-input form-select">
                        <option value="">All statuses</option>

                        <option value="scheduled"
                            @selected(request('status') === 'scheduled')>
                            Scheduled
                        </option>

                        <option value="completed"
                            @selected(request('status') === 'completed')>
                            Completed
                        </option>

                        <option value="cancelled"
                            @selected(request('status') === 'cancelled')>
                            Cancelled
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <label for="date" class="form-label">Appointment Date</label>

                    <input type="date"
                           name="date"
                           id="date"
                           class="ui-input form-control"
                           value="{{ request('date') }}">
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Apply
                    </button>
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <a href="{{ route('appointments.index') }}"
                       class="btn btn-light w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"
                           aria-hidden="true"></i>
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </section>

    {{-- Appointments Table --}}
    <section class="ui-card panel table-panel" aria-labelledby="appointments-heading">

        <div class="table-heading">
            <div>
                <h2 id="appointments-heading">Appointment Records</h2>
                <p class="page-subtitle mt-1">
                    Review and manage your appointment records.
                </p>
            </div>

            <span class="results-count">
                {{ $appointments->total() }}
                {{ \Illuminate\Support\Str::plural('appointment', $appointments->total()) }}
            </span>
        </div>

        @if($appointments->count() > 0)

            <div class="ui-table-wrap table-responsive">
                <table class="ui-table table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Date & Time</th>
                            <th scope="col">Patient</th>
                            <th scope="col">Doctor</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($appointments as $appointment)

                            @php
                                $status = $appointment->status;

                                $statusClass = match ($status) {
                                    'scheduled' => 'status-scheduled',
                                    'completed' => 'status-completed',
                                    'cancelled' => 'status-cancelled',
                                    default => 'status-unknown',
                                };

                                $statusLabel = match ($status) {
                                    'scheduled' => 'Scheduled',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                    default => ucfirst($status ?? 'Unknown'),
                                };
                            @endphp

                            <tr>
                                <td data-label="{{ __('ID') }}">
                                    <span class="appointment-id">
                                        #{{ $appointment->id }}
                                    </span>
                                </td>

                                <td data-label="{{ __('Date & Time') }}">
                                    <div class="date-main">
                                        {{ $appointment->appointment_date?->translatedFormat('M d, Y') ?? '—' }}
                                    </div>

                                    <div class="date-meta">
                                        <i class="bi bi-clock me-1" aria-hidden="true"></i>

                                        {{ $appointment->appointment_time
                                            ? \Illuminate\Support\Carbon::parse($appointment->appointment_time)->format('h:i A')
                                            : ($appointment->appointment_date?->format('h:i A') ?? '—') }}
                                    </div>
                                </td>

                                <td data-label="{{ __('Patient') }}">
                                    <div class="person-name">
                                        {{ $appointment->patient?->user?->name ?? 'Unknown patient' }}
                                    </div>

                                    <div class="person-meta">
                                        Patient ID: {{ $appointment->patient_id }}
                                    </div>
                                </td>

                                <td data-label="{{ __('Doctor') }}">
                                    <div class="person-name">
                                        {{ $appointment->doctor?->user?->name
                                            ? 'Dr. ' . $appointment->doctor->user->name
                                            : 'Unknown doctor' }}
                                    </div>

                                    <div class="person-meta">
                                        {{ $appointment->doctor?->specialization ?? 'No specialization' }}
                                    </div>
                                </td>

                                <td data-label="{{ __('Status') }}">
                                    <span class="ui-badge ui-badge-{{ $status }} status-badge {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td data-label="{{ __('Actions') }}">
                                    <div class="action-group">
                                        <a href="{{ route('appointments.show', $appointment) }}"
                                           class="action-btn action-view"
                                           aria-label="View appointment {{ $appointment->id }}">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>
                                            View
                                        </a>

                                        @if($appointment->status === 'scheduled')
                                            <form action="{{ route('appointments.destroy', $appointment) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to cancel appointment #{{ $appointment->id }}?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="action-btn action-cancel"
                                                        aria-label="Cancel appointment {{ $appointment->id }}">
                                                    <i class="bi bi-x-circle me-1"
                                                       aria-hidden="true"></i>
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="ui-pagination pagination-wrapper">
                {{ $appointments->withQueryString()->links() }}
            </div>

        @else

            {{-- Empty State --}}
            <div class="ui-empty empty-state">
                <div class="empty-icon">
                    <i class="bi bi-calendar2-x" aria-hidden="true"></i>
                </div>

                <h4>No appointments found</h4>

                @if(request()->hasAny(['status', 'date']))
                    <p>
                        No appointments match your current filters.
                        Try changing your search criteria.
                    </p>

                    <a href="{{ route('appointments.index') }}"
                       class="ui-btn ui-btn-ghost btn btn-light">
                        <i class="bi bi-arrow-counterclockwise me-1"
                           aria-hidden="true"></i>
                        Clear Filters
                    </a>
                @else
                    <p>
                        There are no appointment records yet.
                        Create your first appointment to get started.
                    </p>

                    <a href="{{ route('appointments.create') }}"
                       class="ui-btn ui-btn-primary btn btn-primary">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
                        Create Appointment
                    </a>
                @endif
            </div>

        @endif
    </section>

</div>
@endsection
