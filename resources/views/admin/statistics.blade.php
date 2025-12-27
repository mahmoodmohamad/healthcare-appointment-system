@extends('layouts.app')
@section('title', 'System Statistics')

<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #e0e7ff;
        --secondary: #10b981;
        --danger: #ef4444;
        --info: #06b6d4;
        --warning: #f59e0b;
        --dark: #1f2937;
        --light: #f9fafb;
        --gray: #6b7280;
        --border: #e5e7eb;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .stats-container {
        min-height: calc(100vh - 80px);
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid var(--border);
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
        position: relative;
        padding-left: 1rem;
    }

    .page-header h1::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 70%;
        background: var(--primary);
        border-radius: 2px;
    }

    .refresh-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .refresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: #4338ca;
    }

    /* Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--info));
    }

    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, var(--dark) 0%, var(--gray) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stat-label {
        color: var(--gray);
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-change {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .stat-change.positive {
        color: var(--secondary);
    }

    .stat-change.negative {
        color: var(--danger);
    }

    /* Role-specific colors */
    .admin-stat .stat-icon { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    .physician-stat .stat-icon { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
    .secretary-stat .stat-icon { background: rgba(6, 182, 212, 0.1); color: var(--info); }
    .patient-stat .stat-icon { background: rgba(16, 185, 129, 0.1); color: var(--secondary); }

    /* Chart Card */
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        height: 100%;
        border: 1px solid var(--border);
    }

    .chart-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
    }

    .chart-period {
        font-size: 0.875rem;
        color: var(--gray);
        padding: 0.375rem 0.75rem;
        background: var(--light);
        border-radius: 8px;
    }

    /* Top Physicians */
    .rank-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        height: 100%;
        border: 1px solid var(--border);
    }

    .rank-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        transition: background 0.2s ease;
        margin-bottom: 0.5rem;
    }

    .rank-item:hover {
        background: var(--light);
    }

    .rank-number {
        width: 32px;
        height: 32px;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .rank-number.top-3 {
        background: linear-gradient(135deg, var(--warning), #f97316);
        color: white;
    }

    .physician-info {
        flex: 1;
    }

    .physician-name {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .physician-specialty {
        font-size: 0.875rem;
        color: var(--gray);
    }

    .appointment-count {
        background: var(--primary-light);
        color: var(--primary);
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.875rem;
    }

    /* Table Styling */
    .table-container {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .table-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .table {
        margin: 0;
    }

    .table thead th {
        background: var(--light);
        border-bottom: 2px solid var(--border);
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: var(--dark);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-color: var(--border);
    }

    .city-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--light);
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        color: var(--dark);
    }

    .total-cell {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.125rem;
    }

    /* Loading Animation */
    .loading {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .loading.active {
        display: flex;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .stat-number {
            font-size: 2rem;
        }

        .stat-card {
            padding: 1rem;
        }

        .table {
            display: block;
            overflow-x: auto;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
</style>

@section('content')
<div class="stats-container">
    <!-- Loading Overlay -->
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>System Statistics</h1>
                <p class="text-muted mb-0">Real-time analytics and insights about your healthcare system</p>
            </div>
            <button class="refresh-btn" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i>
                Refresh Data
            </button>
        </div>

        <!-- Users by Role -->
        <div class="row mb-4 fade-in-up">
            @php
                $roleConfig = [
                    'admins' => ['icon' => 'fas fa-user-shield', 'class' => 'admin-stat', 'trend' => '+2'],
                    'physicians' => ['icon' => 'fas fa-user-md', 'class' => 'physician-stat', 'trend' => '+12'],
                    'secretaries' => ['icon' => 'fas fa-user-tie', 'class' => 'secretary-stat', 'trend' => '+5'],
                    'patients' => ['icon' => 'fas fa-user-injured', 'class' => 'patient-stat', 'trend' => '+45']
                ];
            @endphp
            
            @foreach(['admins'=>'Admins','physicians'=>'Physicians','secretaries'=>'Secretaries','patients'=>'Patients'] as $key=>$label)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card {{ $roleConfig[$key]['class'] }}">
                    <div class="stat-card-header">
                        <div class="stat-icon">
                            <i class="{{ $roleConfig[$key]['icon'] }}"></i>
                        </div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            {{ $roleConfig[$key]['trend'] }}%
                        </div>
                    </div>
                    <div class="stat-number">
                        {{ number_format($stats['users_by_role'][$key]) }}
                    </div>
                    <div class="stat-label">{{ $label }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Charts & Top Physicians -->
        <div class="row mb-4">
            <!-- Appointments Chart -->
            <div class="col-xl-8 col-lg-7 mb-4 fade-in-up" style="animation-delay: 0.2s">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <h3 class="chart-title">Appointments Overview</h3>
                            <p class="text-muted mb-0">Monthly appointment trends</p>
                        </div>
                        <div class="chart-period">Last 12 Months</div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="monthlyAppointments"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Physicians -->
            <div class="col-xl-4 col-lg-5 mb-4 fade-in-up" style="animation-delay: 0.4s">
                <div class="rank-card">
                    <div class="chart-card-header">
                        <div>
                            <h3 class="chart-title">Top Physicians</h3>
                            <p class="text-muted mb-0">Most active doctors</p>
                        </div>
                        <div class="chart-period">This Month</div>
                    </div>
                    <div class="rank-list">
                        @foreach($stats['appointments_by_physician']->take(5) as $index => $physician)
                        <div class="rank-item">
                            <div class="rank-number {{ $index < 3 ? 'top-3' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="physician-info">
                                <div class="physician-name">Dr. {{ $physician->user->name }}</div>
                                <div class="physician-specialty">{{ $physician->specialization }}</div>
                            </div>
                            <div class="appointment-count">
                                {{ $physician->appointments_count }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Cities Distribution Table -->
        <div class="row fade-in-up" style="animation-delay: 0.6s">
            <div class="col-12">
                <div class="table-container">
                    <div class="table-header">
                        <h4 class="table-title">Geographic Distribution</h4>
                        <p class="text-muted mb-0">User distribution across cities</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>City</th>
                                    <th>Patients</th>
                                    <th>Physicians</th>
                                    <th>Secretaries</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['cities_distribution'] as $city)
                                <tr>
                                    <td>
                                        <div class="city-badge">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <strong>{{ $city->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ number_format($city->patients_count) }}</td>
                                    <td>{{ number_format($city->physicians_count) }}</td>
                                    <td>{{ number_format($city->secretaries_count) }}</td>
                                    <td class="total-cell">
                                        {{ number_format($city->patients_count + $city->physicians_count + $city->secretaries_count) }}
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
// Initialize with fade animations
document.addEventListener('DOMContentLoaded', function() {
    initChart();
    setupAnimations();
});

// Chart initialization with enhanced options
function initChart() {
    const ctx = document.getElementById('monthlyAppointments').getContext('2d');
    
    // Prepare data
    const labels = [
        @foreach($stats['appointments_by_month'] as $item)
        '{{ $item->month }}',
        @endforeach
    ];
    
    const data = [
        @foreach($stats['appointments_by_month'] as $item)
        {{ $item->count }},
        @endforeach
    ];

    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.8)');
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Appointments',
                data: data,
                backgroundColor: gradient,
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'white',
                pointBorderColor: 'rgba(79, 70, 229, 1)',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.95)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(79, 70, 229, 0.5)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(229, 231, 235, 0.5)'
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 12
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animations: {
                tension: {
                    duration: 1000,
                    easing: 'linear'
                }
            }
        }
    });
}

// Setup animations
function setupAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.fade-in-up').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Refresh data function
function refreshData() {
    const loading = document.getElementById('loading');
    loading.classList.add('active');

    // Simulate API call
    setTimeout(() => {
        loading.classList.remove('active');
        
        // Show success message
        showNotification('Data refreshed successfully!', 'success');
        
        // In a real app, you would fetch new data and update the chart
        // For now, we'll just re-initialize the chart
        const chart = Chart.getChart('monthlyAppointments');
        if (chart) {
            chart.destroy();
            initChart();
        }
    }, 1500);
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add notification animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
    }
`;
document.head.appendChild(style);
</script>
@endsection