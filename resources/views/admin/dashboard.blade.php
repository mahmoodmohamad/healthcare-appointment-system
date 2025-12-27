@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Admin Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Users</h6>
                            <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                        </div>
                        <div class="fs-1">👥</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Patients</h6>
                            <h2 class="mb-0">{{ $stats['total_patients'] }}</h2>
                        </div>
                        <div class="fs-1">🏥</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Physicians</h6>
                            <h2 class="mb-0">{{ $stats['total_physicians'] }}</h2>
                        </div>
                        <div class="fs-1">👨‍⚕️</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Appointments</h6>
                            <h2 class="mb-0">{{ $stats['total_appointments'] }}</h2>
                        </div>
                        <div class="fs-1">📅</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Today's Appointments</h6>
                    <h3 class="text-primary">{{ $stats['today_appointments'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Scheduled</h6>
                    <h3 class="text-info">{{ $stats['scheduled'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Completed</h6>
                    <h3 class="text-success">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">This Month</h6>
                    <h3 class="text-warning">{{ $stats['month_appointments'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Charts Column -->
        <div class="col-md-8">
            <!-- Appointments by Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Appointments by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="80"></canvas>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Appointments Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Side Column -->
        <div class="col-md-4">
            <!-- Top Physicians -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">🏆 Top Physicians</h5>
                </div>
                <div class="card-body">
                    @foreach($topPhysicians as $physician)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <strong>Dr. {{ $physician->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $physician->specialization }}</small>
                        </div>
                        <span class="badge bg-primary">{{ $physician->appointments_count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Patients -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">👤 Recent Patients</h5>
                </div>
                <div class="card-body">
                    @foreach($recentPatients as $patient)
                    <div class="mb-2 pb-2 border-bottom">
                        <strong>{{ $patient->user->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $patient->created_at->diffForHumans() }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Physician</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_date->format('Y-m-d H:i') }}</td>
                                    <td>{{ $appointment->patient->user->name }}</td>
                                    <td>Dr. {{ $appointment->physician->user->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
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
// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($appointmentsByStatus as $item)
                '{{ ucfirst($item->status) }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($appointmentsByStatus as $item)
                    {{ $item->count }},
                @endforeach
            ],
            backgroundColor: ['#0dcaf0', '#198754', '#dc3545']
        }]
    }
});

// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($monthlyData as $item)
                '{{ $item->month }}',
            @endforeach
        ],
        datasets: [{
            label: 'Appointments',
            data: [
                @foreach($monthlyData as $item)
                    {{ $item->count }},
                @endforeach
            ],
            borderColor: '#0d6efd',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(13, 110, 253, 0.1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
</script>
@endsection