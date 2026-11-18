@extends('layouts.app')
@section('title', $doctor->user->name)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ $doctor->user->name }}</h2>
            <p class="text-muted mb-0">{{ $doctor->specialization }}</p>
        </div>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">{{ __('site.back') }} to {{ __('site.doctor') }}s</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['total_appointments'] }}</h3>
                    <small>Total {{ __('site.appointment') }}s</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                    <small>{{ __('site.completed') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['scheduled'] }}</h3>
                    <small>{{ __('site.scheduled') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $stats['total_patients'] }}</h3>
                    <small>Distinct {{ __('site.patient') }}s</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">{{ __('site.profile') }}</h5></div>
                <div class="card-body">
                    <p><strong>{{ __('site.name_label') }}</strong> {{ $doctor->user->name }}</p>
                    <p><strong>{{ __('site.email_label') }}</strong> {{ $doctor->user->email }}</p>
                    <p><strong>{{ __('site.specialization_label') }}</strong> {{ $doctor->specialization }}</p>
                    <p><strong>{{ __('site.phone_label') }}</strong> {{ $doctor->phone }}</p>
                    <p><strong>{{ __('site.city') }}:</strong> {{ $doctor->city->name ?? '—' }}</p>
                    <p class="mb-0"><strong>{{ __('site.account_label') }}</strong>
                        @if($doctor->user->activation)
                            <span class="badge bg-success">{{ __('site.active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('site.inactive') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">{{ __('site.appointment') }} History</h5>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('site.date') }}</th>
                                        <th>{{ __('site.patient') }}</th>
                                        <th>{{ __('site.status') }}</th>
                                        <th>{{ __('site.diagnosis') }}</th>
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
                                            <td>{{ $appointment->patient->user->name ?? '—' }}</td>
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
                                                   class="btn btn-sm btn-outline-primary">{{ __('site.open') }}</a>
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
                        <p class="text-center text-muted py-4 mb-0">{{ __('site.no_appointments_yet') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection