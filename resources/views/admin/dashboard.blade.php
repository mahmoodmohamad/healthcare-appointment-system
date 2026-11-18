@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- ============================================================
         Page header
         ============================================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-0">Admin Dashboard</h2>
            <p class="text-muted mb-0">System overview as of {{ now()->format('l, F j, Y — g:i A') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-primary">
                📅 Appointments
            </a>
            <a href="{{ route('admin.statistics') }}" class="btn btn-outline-secondary">
                📊 Full Statistics
            </a>
        </div>
    </div>

    {{-- ============================================================
         Row 1 — Primary totals
         ============================================================ --}}
    <div class="row g-3 mb-3">
        @php
            $primaryTiles = [
                [
                    'label' => 'Users',
                    'value' => $stats['total_users'],
                    'icon'  => '👥',
                    'color' => 'primary',
                    'route' => route('admin.users.index'),
                ],
                [
                    'label' => 'Patients',
                    'value' => $stats['total_patients'],
                    'icon'  => '🏥',
                    'color' => 'success',
                    'route' => route('admin.patients.index'),
                ],
                [
                    'label' => 'Doctors',
                    'value' => $stats['total_doctors'],
                    'icon'  => '👨‍⚕️',
                    'color' => 'info',
                    'route' => route('admin.doctors.index'),
                ],
                [
                    'label' => 'Receptionists',
                    'value' => $stats['total_receptionists'],
                    'icon'  => '👩‍💼',
                    'color' => 'secondary',
                    'route' => route('admin.receptionists.index'),
                ],
                [
                    'label' => 'Appointments',
                    'value' => $stats['total_appointments'],
                    'icon'  => '📅',
                    'color' => 'warning',
                    'route' => route('admin.appointments.index'),
                ],
                [
                    'label' => 'Diagnoses',
                    'value' => $stats['total_diagnoses'],
                    'icon'  => '🩺',
                    'color' => 'dark',
                    'route' => null,
                ],
            ];
        @endphp

        @foreach($primaryTiles as $tile)
            <div class="col-xl-2 col-lg-4 col-md-4 col-6">
                @if($tile['route'])
                    <a href="{{ $tile['route'] }}" class="text-decoration-none">
                @endif
                    <div class="card bg-{{ $tile['color'] }} text-white h-100 tile-hover">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small text-uppercase">{{ $tile['label'] }}</h6>
                                <h3 class="mb-0">{{ number_format($tile['value']) }}</h3>
                            </div>
                            <div class="fs-2 opacity-75">{{ $tile['icon'] }}</div>
                        </div>
                    </div>
                @if($tile['route'])
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ============================================================
         Row 2 — Activity snapshot
         ============================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1 small text-uppercase">Today's Appointments</h6>
                    <h3 class="text-primary mb-0">{{ $stats['today_appointments'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1 small text-uppercase">Scheduled</h6>
                    <h3 class="text-info mb-0">{{ $stats['scheduled'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1 small text-uppercase">Completed</h6>
                    <h3 class="text-success mb-0">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1 small text-uppercase">This Month</h6>
                    <h3 class="text-warning mb-0">{{ $stats['month_appointments'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         Row 3 — Charts + side panels
         ============================================================ --}}
    <div class="row g-3 mb-4">

        {{-- Charts --}}
        <div class="col-xl-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Appointments by Status</h5>
                    <span class="badge bg-light text-dark">{{ $stats['total_appointments'] }} total</span>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="90"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Appointments Trend — Last 6 Months</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="90"></canvas>
                </div>
            </div>
        </div>

        {{-- Side panels --}}
        <div class="col-xl-4">

            {{-- Top Doctors --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🏆 Top Doctors</h5>
                    <a href="{{ route('admin.doctors.index') }}"
                       class="text-white small text-decoration-none">View all →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($topDoctors as $doctor)
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <div class="text-truncate">
                                <strong>Dr. {{ $doctor->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $doctor->specialization }}</small>
                            </div>
                            <span class="badge bg-primary">{{ $doctor->appointments_count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0 small">No doctors yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top Receptionists --}}
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🎯 Top Receptionists</h5>
                    <a href="{{ route('admin.receptionists.index') }}"
                       class="text-white small text-decoration-none">View all →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($topReceptionists as $r)
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <div class="text-truncate">
                                <strong>{{ $r->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $r->phone }}</small>
                            </div>
                            <span class="badge bg-secondary">{{ $r->appointments_count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0 small">No receptionists yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Patients --}}
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">👤 Recent Patients</h5>
                    <a href="{{ route('admin.patients.index') }}"
                       class="text-white small text-decoration-none">View all →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentPatients as $patient)
                        <a href="{{ route('admin.patients.show', $patient) }}"
                           class="d-block px-3 py-2 border-bottom text-decoration-none text-dark">
                            <strong>{{ $patient->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $patient->created_at->diffForHumans() }}</small>
                        </a>
                    @empty
                        <p class="text-muted text-center py-3 mb-0 small">No patients yet.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================
         Row 4 — Recent appointments table
         ============================================================ --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Appointments</h5>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-outline-primary">
                        View all →
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAppointments as $appointment)
                                    <tr>
                                        <td>
                                            <strong>{{ $appointment->appointment_date->format('Y-m-d') }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $appointment->appointment_time
                                                    ?? $appointment->appointment_date->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>{{ $appointment->patient->user->name ?? '—' }}</td>
                                        <td>Dr. {{ $appointment->doctor->user->name ?? '—' }}</td>
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
                                            <a href="{{ route('admin.appointments.show', $appointment) }}"
                                               class="btn btn-sm btn-outline-primary">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No appointments yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------- Status doughnut --------
    const statusLabels = [
        @foreach($appointmentsByStatus as $item)
            '{{ ucfirst($item->status) }}',
        @endforeach
    ];
    const statusValues = [
        @foreach($appointmentsByStatus as $item)
            {{ $item->count }},
        @endforeach
    ];

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: ['#0d6efd', '#198754', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // -------- Monthly line --------
    const monthlyLabels = [
        @foreach($monthlyData as $item)
            '{{ $item->month }}',
        @endforeach
    ];
    const monthlyValues = [
        @foreach($monthlyData as $item)
            {{ $item->count }},
        @endforeach
    ];

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Appointments',
                data: monthlyValues,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
});
</script>
@endsection