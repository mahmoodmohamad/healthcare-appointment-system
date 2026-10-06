@extends('patient.layout')

@section('content')
    <section class="patient-grid" aria-label="Appointment summary">
        <div class="patient-card">
            <span class="patient-stat-label">Scheduled appointments</span>
            <strong class="patient-stat-value">{{ $scheduledAppointments }}</strong>
        </div>
        <div class="patient-card">
            <span class="patient-stat-label">Completed appointments</span>
            <strong class="patient-stat-value">{{ $completedAppointments }}</strong>
        </div>
        <div class="patient-card">
            <span class="patient-stat-label">Cancelled appointments</span>
            <strong class="patient-stat-value">{{ $cancelledAppointments }}</strong>
        </div>
        <div class="patient-card" id="profile">
            <span class="patient-stat-label">Care team</span>
            <strong class="patient-stat-value">{{ $appointments->pluck('doctor')->filter()->unique('id')->count() }}</strong>
        </div>
    </section>

    <section class="patient-card" id="appointments" style="margin-bottom: 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:16px;">
            <div>
                <h2 style="margin:0 0 5px;">Upcoming appointments</h2>
                <p style="margin:0; color:var(--patient-muted);">Your next scheduled visits and care team details.</p>
            </div>
            <span class="patient-badge">{{ $upcomingAppointments->count() }} upcoming</span>
        </div>

        @if ($upcomingAppointments->isEmpty())
            <p style="color:var(--patient-muted);">You do not have any upcoming appointments.</p>
        @else
            <div style="overflow-x:auto;">
                <table class="patient-table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Doctor</th>
                        <th>Specialty</th>
                        <th>Status</th>
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
            <h2 style="margin:0 0 5px;">Latest medical summary</h2>
            <p style="margin:0; color:var(--patient-muted);">A quick view of your most recent completed consultation.</p>
        </div>
        @if ($latestDiagnosis)
            <div style="display:grid; gap:12px;">
                <div><strong>Assessment</strong><br>{{ $latestDiagnosis->diagnosis }}</div>
                <div><strong>Symptoms</strong><br>{{ $latestDiagnosis->symptoms }}</div>
                @if ($latestDiagnosis->prescription)
                    <div><strong>Treatment plan</strong><br>{{ $latestDiagnosis->prescription }}</div>
                @endif
            </div>
        @else
            <p style="color:var(--patient-muted);">No medical summary is available yet.</p>
        @endif
    </section>
@endsection
