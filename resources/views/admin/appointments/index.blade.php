@extends('layouts.app')
@section('title', '{{ __('site.all_appointments') }}')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ __('site.all_appointments') }}</h2>
            <p class="text-muted mb-0">{{ __('site.appointments_system_description') }}</p>
        </div>
        <a href="{{ route('admin.appointments.calendar') }}" class="btn btn-info">
            📅 Calendar {{ __('site.view') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- {{ __('site.filter') }}s -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.appointments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('site.search') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('site.patient') }}, doctor, national {{ __('site.id') }}..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('site.status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('site.all') }} {{ __('site.status') }}</option>
                        @foreach(['scheduled','completed','cancelled'] as $s)
                            <option value="{{ $s }}"
                                {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('site.doctor') }}</label>
                    <select name="doctor_id" class="form-select">
                        <option value="">{{ __('site.all') }} {{ __('site.doctor') }}s</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}"
                                {{ ($filters['doctor_id'] ?? '') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->name }}
                            </option> 
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('site.receptionist') }}</label>
                    <select name="receptionist_id" class="form-select">
                        <option value="">{{ __('site.all') }} {{ __('site.receptionist') }}s</option>
                        @foreach($receptionists as $r)
                            <option value="{{ $r->id }}"
                                {{ ($filters['receptionist_id'] ?? '') == $r->id ? 'selected' : '' }}>
                                {{ $r->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('site.from') }}</label>
                    <input type="date" name="date_from" class="form-control"
                           value="{{ $filters['date_from'] ?? '' }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary w-100">{{ __('site.filter') }}</button>
                </div>

                <div class="col-12">
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-outline-secondary">
                        {{ __('site.clear') }} filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('site.id') }}</th>
                                <th>{{ __('site.date_time') }}</th>
                                <th>{{ __('site.patient') }}</th>
                                <th>{{ __('site.doctor') }}</th>
                                <th>{{ __('site.receptionist') }}</th>
                                <th>{{ __('site.status') }}</th>
                                <th>{{ __('site.diagnosis') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>#{{ $appointment->id }}</td>
                                    <td>
                                        {{ $appointment->appointment_date->format('Y-m-d') }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $appointment->patient->user->name ?? '—' }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $appointment->patient->national_id ?? '' }}
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
                                        {{ $appointment->receptionist->user->name ?? 'System' }}
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
                                            <span class="badge bg-success">✓</span>
                                        @else
                                            <span class="badge bg-secondary">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.appointments.show', $appointment) }}"
                                           class="btn btn-sm btn-info">{{ __('site.view') }}</a>
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
                <p class="text-center text-muted py-5 mb-0">{{ __('site.no_appointments_filter') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection