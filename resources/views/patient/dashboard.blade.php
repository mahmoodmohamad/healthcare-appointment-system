@extends('patient.layout')

@section('content')
    <section class="patient-grid" aria-label="Appointment summary">
        <div class="patient-card">
            <span class="patient-stat-label">@lang('site.scheduled_appointments')</span>
            <strong class="patient-stat-value">{{ $scheduledAppointments }}</strong>
        </div>
        <div class="patient-card">
            <span class="patient-stat-label">@lang('site.completed_appointments')</span>
            <strong class="patient-stat-value">{{ $completedAppointments }}</strong>
        </div>
        <div class="patient-card">
            <span class="patient-stat-label">@lang('site.cancelled_appointments')</span>
            <strong class="patient-stat-value">{{ $cancelledAppointments }}</strong>
        </div>
        <div class="patient-card" id="profile">
            <span class="patient-stat-label">@lang('site.care_team')</span>
            <strong class="patient-stat-value">{{ $appointments->pluck('doctor')->filter()->unique('id')->count() }}</strong>
        </div>
    </section>

    <section class="patient-card" id="appointments" style="margin-bottom: 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:16px;">
            <div>
                <h2 style="margin:0 0 5px;">@lang('site.upcoming_appointments')</h2>
                <p style="margin:0; color:var(--patient-muted);">@lang('site.upcoming_visits_description')</p>
            </div>
            <span class="patient-badge">{{ $upcomingAppointments->count() }} upcoming</span>
        </div>

        @if ($upcomingAppointments->isEmpty())
            <p style="color:var(--patient-muted);">@lang('site.no_upcoming_appointments')</p>
        @else
            <div style="overflow-x:auto;">
                <table class="patient-table">
                    <thead>
                    <tr>
                        <th>@lang('site.date')</th>
                        <th>@lang('site.time')</th>
                        <th>@lang('site.doctor')</th>
                        <th>@lang('site.specialty')</th>
                        <th>@lang('site.status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($upcomingAppointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_date?->format('M j, Y') }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>
                            <td>{{ $appointment->doctor?->user?->name ?? 'Assigned doctor' }}</td>
                            <td>{{ $appointment->doctor?->specialization ?? 'General care' }}</td>
                            <td><span class="patient-badge">{{ ucfirst($appointment->status) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="patient-card" id="medical-history">
        <div style="margin-bottom:16px;">
            <h2 style="margin:0 0 5px;">@lang('site.latest_medical_summary')</h2>
            <p style="margin:0; color:var(--patient-muted);">@lang('site.latest_consultation_description')</p>
        </div>
        @if ($latestDiagnosis)
            <div style="display:grid; gap:12px;">
                <div><strong>@lang('site.assessment')</strong><br>{{ $latestDiagnosis->diagnosis }}</div>
                <div><strong>@lang('site.symptoms')</strong><br>{{ $latestDiagnosis->symptoms }}</div>
                @if ($latestDiagnosis->prescription)
                    <div><strong>@lang('site.treatment_plan')</strong><br>{{ $latestDiagnosis->prescription }}</div>
                @endif
            </div>
        @else
            <p style="color:var(--patient-muted);">@lang('site.no_medical_summary')</p>
        @endif
    </section>
@endsection
