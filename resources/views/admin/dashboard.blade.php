@extends('layouts.app')

@section('title', __('site.admin_dashboard'))

@section('content')
@php
    $summaryCards = [
        [
            'key' => 'total_users',
            'label' => __('site.total_users'),
            'value' => $stats['total_users'],
            'icon' => 'bi-people',
            'tone' => 'teal',
            'url' => route('admin.users.index'),
        ],
        [
            'key' => 'total_patients',
            'label' => __('site.total_patients'),
            'value' => $stats['total_patients'],
            'icon' => 'bi-person-heart',
            'tone' => 'blue',
            'url' => route('admin.patients.index'),
        ],
        [
            'key' => 'total_doctors',
            'label' => __('site.total_doctors'),
            'value' => $stats['total_doctors'],
            'icon' => 'bi-heart-pulse',
            'tone' => 'green',
            'url' => route('admin.doctors.index'),
        ],
        [
            'key' => 'total_receptionists',
            'label' => __('site.total_receptionists'),
            'value' => $stats['total_receptionists'],
            'icon' => 'bi-headset',
            'tone' => 'orange',
            'url' => route('admin.receptionists.index'),
        ],
        [
            'key' => 'total_appointments',
            'label' => __('site.total_appointments'),
            'value' => $stats['total_appointments'],
            'icon' => 'bi-calendar2-check',
            'tone' => 'teal',
            'url' => route('admin.appointments.index'),
        ],
        [
            'key' => 'total_diagnoses',
            'label' => __('site.total_diagnoses'),
            'value' => $stats['total_diagnoses'],
            'icon' => 'bi-clipboard2-pulse',
            'tone' => 'blue',
            'url' => null,
        ],
    ];

    $statusLabels = $appointmentsByStatus->map(function ($item) {
        return match ($item->status) {
            'scheduled' => __('site.scheduled'),
            'completed' => __('site.completed'),
            'cancelled' => __('site.cancelled'),
            default => ucfirst($item->status),
        };
    })->values();

    $statusValues = $appointmentsByStatus->pluck('count')->values();
    $statusColors = $appointmentsByStatus->map(function ($item) {
        return match ($item->status) {
            'completed' => '#2f855a',
            'cancelled' => '#b42318',
            'scheduled' => '#1d6fb8',
            default => '#c2610c',
        };
    })->values();
    $monthlyLabels = $monthlyData->pluck('month')->values();
    $monthlyValues = $monthlyData->pluck('count')->values();
@endphp

<div class="ui-page admin-dashboard">
    <header class="ui-page-head admin-dashboard-head">
        <div>
            <span class="admin-eyebrow">
                <i class="bi bi-grid-1x2" aria-hidden="true"></i>
                @lang('site.overview')
            </span>
            <h1 class="ui-title">@lang('site.admin_dashboard')</h1>
            <p class="ui-sub">@lang('site.admin_dashboard_welcome')</p>
            <div class="admin-date-line">
                <i class="bi bi-calendar3" aria-hidden="true"></i>
                <span>{{ now()->translatedFormat('l, j F Y') }}</span>
            </div>
        </div>

        <div class="admin-dashboard-actions">
            <a href="{{ route('admin.appointments.index') }}" class="ui-btn ui-btn-primary">
                <i class="bi bi-calendar2-week" aria-hidden="true"></i>
                <span>@lang('site.open_appointments')</span>
            </a>
            <a href="{{ route('admin.statistics') }}" class="ui-btn ui-btn-ghost">
                <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
                <span>@lang('site.full_statistics')</span>
            </a>
        </div>
    </header>

    <section aria-label="@lang('site.system_activity')">
        <div class="ui-stats admin-primary-stats">
            @foreach ($summaryCards as $card)
                @if ($card['url'])
                    <a href="{{ $card['url'] }}" class="ui-card ui-stat admin-stat-card">
                @else
                    <div class="ui-card ui-stat admin-stat-card">
                @endif
                    <span class="ui-stat-icon ui-tone-{{ $card['tone'] }}">
                        <i class="bi {{ $card['icon'] }}" aria-hidden="true"></i>
                    </span>
                    <span class="admin-stat-copy">
                        <span class="ui-stat-label">{{ $card['label'] }}</span>
                        <strong class="ui-stat-number">{{ number_format($card['value']) }}</strong>
                    </span>
                    @if ($card['url'])
                        <i class="bi bi-arrow-up-left admin-stat-arrow" aria-hidden="true"></i>
                    @endif
                @if ($card['url'])
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    <section class="admin-activity-section" aria-labelledby="admin-activity-title">
        <div class="admin-section-heading">
            <div>
                <h2 id="admin-activity-title">@lang('site.system_activity')</h2>
                <p>@lang('site.appointments_system_description')</p>
            </div>
            <span class="ui-chip">
                <span class="admin-live-dot"></span>
                {{ now()->translatedFormat('F Y') }}
            </span>
        </div>

        <div class="row g-3 admin-activity-grid">
            <div class="col-6 col-xl-3">
                <article class="ui-card admin-activity-card">
                    <span class="admin-activity-icon ui-tone-teal"><i class="bi bi-calendar-day" aria-hidden="true"></i></span>
                    <div>
                        <span>@lang('site.todays_appointments')</span>
                        <strong>{{ number_format($stats['today_appointments']) }}</strong>
                    </div>
                </article>
            </div>
            <div class="col-6 col-xl-3">
                <article class="ui-card admin-activity-card">
                    <span class="admin-activity-icon ui-tone-blue"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                    <div>
                        <span>@lang('site.scheduled')</span>
                        <strong>{{ number_format($stats['scheduled']) }}</strong>
                    </div>
                </article>
            </div>
            <div class="col-6 col-xl-3">
                <article class="ui-card admin-activity-card">
                    <span class="admin-activity-icon ui-tone-green"><i class="bi bi-check2-circle" aria-hidden="true"></i></span>
                    <div>
                        <span>@lang('site.completed')</span>
                        <strong>{{ number_format($stats['completed']) }}</strong>
                    </div>
                </article>
            </div>
            <div class="col-6 col-xl-3">
                <article class="ui-card admin-activity-card">
                    <span class="admin-activity-icon ui-tone-orange"><i class="bi bi-calendar2-month" aria-hidden="true"></i></span>
                    <div>
                        <span>@lang('site.this_month')</span>
                        <strong>{{ number_format($stats['month_appointments']) }}</strong>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <div class="row g-4 admin-insights-grid">
        <div class="col-xl-7">
            <section class="ui-card admin-panel h-100" aria-labelledby="monthly-chart-title">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="monthly-chart-title">@lang('site.appointments_trend')</h2>
                        <p class="ui-card-sub">@lang('site.appointment_volume')</p>
                    </div>
                    <span class="admin-panel-icon"><i class="bi bi-graph-up" aria-hidden="true"></i></span>
                </div>
                <div class="admin-chart-wrap">
                    <canvas id="monthlyChart" role="img" aria-label="@lang('site.appointments_trend')"></canvas>
                    <p class="visually-hidden">@lang('site.appointments_trend')</p>
                </div>
            </section>
        </div>

        <div class="col-xl-5">
            <section class="ui-card admin-panel h-100" aria-labelledby="status-chart-title">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="status-chart-title">@lang('site.appointments_by_status')</h2>
                        <p class="ui-card-sub">{{ number_format($stats['total_appointments']) }} @lang('site.appointments_total')</p>
                    </div>
                    <span class="admin-panel-icon"><i class="bi bi-pie-chart" aria-hidden="true"></i></span>
                </div>
                @if ($appointmentsByStatus->isNotEmpty())
                    <div class="admin-status-chart-wrap">
                        <canvas id="statusChart" role="img" aria-label="@lang('site.appointments_by_status')"></canvas>
                    </div>
                @else
                    <div class="ui-empty admin-chart-empty">
                        <span class="ui-empty-icon ui-tone-blue"><i class="bi bi-pie-chart" aria-hidden="true"></i></span>
                        <strong>@lang('site.no_data')</strong>
                        <p>@lang('site.no_recent_appointments')</p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <div class="row g-4 admin-leaderboards">
        <div class="col-xl-6">
            <section class="ui-card admin-panel h-100" aria-labelledby="top-doctors-title">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="top-doctors-title">@lang('site.top_doctors')</h2>
                        <p class="ui-card-sub">@lang('site.appointments_system_description')</p>
                    </div>
                    <a href="{{ route('admin.doctors.index') }}" class="ui-link">@lang('site.view_all')</a>
                </div>
                <div class="admin-list">
                    @forelse ($topDoctors as $doctor)
                        <div class="ui-row admin-person-row">
                            <span class="ui-avatar"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
                            <div class="ui-row-main">
                                <strong>{{ $doctor->user?->name ?? __('site.unknown_doctor') }}</strong>
                                <span class="ui-meta">{{ $doctor->specialization ?: __('site.no_specialization') }}</span>
                            </div>
                            <span class="admin-count-pill">{{ number_format($doctor->appointments_count) }}</span>
                        </div>
                    @empty
                        <div class="ui-empty">
                            <span class="ui-empty-icon ui-tone-blue"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
                            <strong>@lang('site.no_doctors_yet')</strong>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-xl-6">
            <section class="ui-card admin-panel h-100" aria-labelledby="top-receptionists-title">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="top-receptionists-title">@lang('site.top_receptionists')</h2>
                        <p class="ui-card-sub">@lang('site.receptionist_bookings')</p>
                    </div>
                    <a href="{{ route('admin.receptionists.index') }}" class="ui-link">@lang('site.view_all')</a>
                </div>
                <div class="admin-list">
                    @forelse ($topReceptionists as $receptionist)
                        <div class="ui-row admin-person-row">
                            <span class="ui-avatar admin-avatar-blue"><i class="bi bi-person-workspace" aria-hidden="true"></i></span>
                            <div class="ui-row-main">
                                <strong>{{ $receptionist->user?->name ?? __('site.user') }}</strong>
                                <span class="ui-meta">{{ $receptionist->phone ?: __('site.no_contact_details') }}</span>
                            </div>
                            <span class="admin-count-pill">{{ number_format($receptionist->appointments_count) }}</span>
                        </div>
                    @empty
                        <div class="ui-empty">
                            <span class="ui-empty-icon ui-tone-blue"><i class="bi bi-headset" aria-hidden="true"></i></span>
                            <strong>@lang('site.no_receptionists_yet')</strong>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="row g-4 admin-recent-grid">
        <div class="col-xl-8">
            <section class="ui-card admin-panel h-100" aria-labelledby="recent-appointments-title">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="recent-appointments-title">@lang('site.recent_appointments')</h2>
                        <p class="ui-card-sub">@lang('site.recent_activity')</p>
                    </div>
                    <a href="{{ route('admin.appointments.index') }}" class="ui-link">@lang('site.view_all')</a>
                </div>
                <div class="table-responsive">
                    <table class="table ui-table admin-recent-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">@lang('site.date_time')</th>
                                <th scope="col">@lang('site.patient')</th>
                                <th scope="col">@lang('site.doctor')</th>
                                <th scope="col">@lang('site.status')</th>
                                <th scope="col"><span class="visually-hidden">@lang('site.actions')</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentAppointments as $appointment)
                                @php
                                    $appointmentStatus = match ($appointment->status) {
                                        'scheduled' => 'scheduled',
                                        'completed' => 'completed',
                                        'cancelled' => 'cancelled',
                                        default => 'pending',
                                    };
                                    $statusLabel = match ($appointment->status) {
                                        'scheduled' => __('site.scheduled'),
                                        'completed' => __('site.completed'),
                                        'cancelled' => __('site.cancelled'),
                                        default => ucfirst($appointment->status),
                                    };
                                    $patientName = $appointment->patient?->user?->name ?? __('site.unknown_patient');
                                    $doctorName = $appointment->doctor?->user?->name ?? __('site.unknown_doctor');
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="admin-table-date">{{ $appointment->appointment_date->translatedFormat('d M Y') }}</strong>
                                        <span class="admin-table-time">
                                            {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-table-person">{{ $patientName }}</span>
                                    </td>
                                    <td>
                                        <span class="admin-table-person">{{ $doctorName }}</span>
                                    </td>
                                    <td>
                                        <span class="ui-badge ui-badge-{{ $appointmentStatus }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.appointments.show', $appointment) }}"
                                           class="ui-btn ui-btn-ghost ui-btn-sm"
                                           aria-label="@lang('site.open') #{{ $appointment->id }}">
                                            <i class="bi bi-arrow-up-left" aria-hidden="true"></i>
                                            @lang('site.open')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="ui-empty">
                                            <span class="ui-empty-icon ui-tone-blue"><i class="bi bi-calendar2-x" aria-hidden="true"></i></span>
                                            <strong>@lang('site.no_recent_appointments')</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="ui-card admin-panel h-100" aria-labelledby="recent-patients-title">
                <div class="ui-card-head">
                    <div>
                        <h2 class="ui-card-title" id="recent-patients-title">@lang('site.recent_patients')</h2>
                        <p class="ui-card-sub">@lang('site.recent_activity')</p>
                    </div>
                    <a href="{{ route('admin.patients.index') }}" class="ui-link">@lang('site.view_all')</a>
                </div>
                <div class="admin-list">
                    @forelse ($recentPatients as $patient)
                        @php
                            $recentPatientName = $patient->user?->name ?? __('site.unknown_patient');
                        @endphp
                        <a href="{{ route('admin.patients.show', $patient) }}" class="ui-row admin-person-row">
                            <span class="ui-avatar">{{ mb_strtoupper(mb_substr($recentPatientName, 0, 1)) }}</span>
                            <span class="ui-row-main">
                                <strong>{{ $recentPatientName }}</strong>
                                <span class="ui-meta">{{ $patient->created_at->diffForHumans() }}</span>
                            </span>
                            <i class="bi bi-chevron-left admin-row-chevron" aria-hidden="true"></i>
                        </a>
                    @empty
                        <div class="ui-empty">
                            <span class="ui-empty-icon ui-tone-blue"><i class="bi bi-people" aria-hidden="true"></i></span>
                            <strong>@lang('site.no_recent_patients')</strong>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        return;
    }

    const isRtl = document.documentElement.dir === 'rtl';
    const mutedColor = '#71858c';
    const gridColor = 'rgba(31, 65, 75, .08)';
    const statusCanvas = document.getElementById('statusChart');
    const monthlyCanvas = document.getElementById('monthlyChart');

    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($statusLabels),
                datasets: [{
                    data: @json($statusValues),
                    backgroundColor: @json($statusColors),
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: isRtl,
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 18,
                            color: mutedColor,
                            font: { family: getComputedStyle(document.body).fontFamily, size: 12 }
                        }
                    }
                }
            }
        });
    }

    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: 'line',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: @json(__('site.appointments_chart_label')),
                    data: @json($monthlyValues),
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15, 118, 110, .10)',
                    fill: true,
                    tension: .35,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#0f766e'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: { rtl: isRtl }
                },
                scales: {
                    x: {
                        reverse: isRtl,
                        grid: { display: false },
                        ticks: { color: mutedColor, maxRotation: 0, autoSkip: true }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: mutedColor },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    }
});
</script>
@endsection
