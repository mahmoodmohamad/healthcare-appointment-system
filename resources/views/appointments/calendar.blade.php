@extends('layouts.app')

@section('title','Users')

@section('content')
<div class="container-fluid">
    <div class="calendar-header">
        <div>
            <h2>{{ $date->format('F Y') }}</h2>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('appointments.index') }}" class="btn btn-info">
                📋 List View
            </a>
            <div class="calendar-nav">
                <button onclick="changeMonth(-1)">← Previous</button>
                <button onclick="changeMonth(0)">Today</button>
                <button onclick="changeMonth(1)">Next →</button>
            </div>
        </div>
    </div>

    <div class="filter-section">
        <label>Filter by Physician:</label>
        <select id="physicianFilter" onchange="filterByPhysician()">
            <option value="">All Physicians</option>
            @foreach($physicians as $physician)
                <option value="{{ $physician->id }}" 
                    {{ request('physician_id') == $physician->id ? 'selected' : '' }}>
                    {{ $physician->user->name }} - {{ $physician->specialization }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="calendar">
        <div class="calendar-weekdays">
            <div>Sunday</div>
            <div>Monday</div>
            <div>Tuesday</div>
            <div>Wednesday</div>
            <div>Thursday</div>
            <div>Friday</div>
            <div>Saturday</div>
        </div>
        
        <div class="calendar-days">
            @php
                $startDate = $date->copy()->startOfMonth()->startOfWeek();
                $endDate = $date->copy()->endOfMonth()->endOfWeek();
                $currentDate = $startDate->copy();
            @endphp
            
            @while($currentDate <= $endDate)
                @php
                    $dateKey = $currentDate->format('Y-m-d');
                    $isToday = $currentDate->isToday();
                    $isCurrentMonth = $currentDate->month === $date->month;
                @endphp
                
                <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                    <div class="day-number">{{ $currentDate->day }}</div>
                    
                    @if(isset($appointments[$dateKey]))
                        @foreach($appointments[$dateKey] as $appointment)
                            <div class="appointment-item {{ $appointment->status }}" 
                                 onclick="showAppointmentDetails({{ $appointment->id }})"
                                 title="{{ $appointment->patient->user->name }} with Dr. {{ $appointment->physician->user->name }}">
                                {{ $appointment->appointment_time ?? $appointment->appointment_date->format('H:i') }} - 
                                {{ $appointment->patient->user->name }}
                            </div>
                        @endforeach
                    @endif
                </div>
                
                @php $currentDate->addDay(); @endphp
            @endwhile
        </div>
    </div>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background: #007bff;"></div>
            <span>Scheduled</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #28a745;"></div>
            <span>Completed</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #dc3545;"></div>
            <span>Cancelled</span>
        </div>
    </div>
</div>

<script>
function changeMonth(delta) {
    const url = new URL(window.location);
    let month = parseInt(url.searchParams.get('month') || {{ now()->month }});
    let year = parseInt(url.searchParams.get('year') || {{ now()->year }});
    
    if (delta === 0) {
        month = {{ now()->month }};
        year = {{ now()->year }};
    } else {
        month += delta;
        if (month > 12) { month = 1; year++; }
        if (month < 1) { month = 12; year--; }
    }
    
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    
    // Preserve physician_id if it exists
    const physicianId = document.getElementById('physicianFilter').value;
    if (physicianId) {
        url.searchParams.set('physician_id', physicianId);
    }
    
    window.location = url;
}

function filterByPhysician() {
    const physicianId = document.getElementById('physicianFilter').value;
    const url = new URL(window.location);
    
    if (physicianId) {
        url.searchParams.set('physician_id', physicianId);
    } else {
        url.searchParams.delete('physician_id');
    }
    
    // Preserve month and year
    const month = url.searchParams.get('month') || {{ now()->month }};
    const year = url.searchParams.get('year') || {{ now()->year }};
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    
    window.location = url;
}

function showAppointmentDetails(id) {
    window.location = `/appointments/${id}`;
}
</script>
@endsection