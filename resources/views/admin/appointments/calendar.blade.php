@extends('layouts.app')
@section('title', 'Appointment Calendar')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $date->format('F Y') }} — All Appointments</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-primary">
                📋 List View
            </a>
            <a href="{{ route('admin.appointments.calendar', ['month' => $date->copy()->subMonth()->month, 'year' => $date->copy()->subMonth()->year]) }}"
               class="btn btn-outline-secondary">← Prev</a>
            <a href="{{ route('admin.appointments.calendar') }}" class="btn btn-outline-secondary">Today</a>
            <a href="{{ route('admin.appointments.calendar', ['month' => $date->copy()->addMonth()->month, 'year' => $date->copy()->addMonth()->year]) }}"
               class="btn btn-outline-secondary">Next →</a>
        </div>
    </div>

    <!-- Doctor filter -->
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center gap-3">
            <label class="mb-0 fw-semibold">Filter by doctor:</label>
            <form method="GET" class="d-flex gap-2" id="doctorFilterForm">
                <input type="hidden" name="month" value="{{ $date->month }}">
                <input type="hidden" name="year"  value="{{ $date->year }}">
                <select name="doctor_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}"
                            {{ ($filters['doctor_id'] ?? '') == $doctor->id ? 'selected' : '' }}>
                            Dr. {{ $doctor->user->name }} — {{ $doctor->specialization }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Calendar grid -->
    <div class="card">
        <div class="card-body">
            @php
                $start = $date->copy()->startOfMonth()->startOfWeek();
                $end   = $date->copy()->endOfMonth()->endOfWeek();
                $cursor = $start->copy();
            @endphp

            <div class="row row-cols-7 g-0 text-center fw-bold border-bottom pb-2 mb-2">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                    <div class="col">{{ $d }}</div>
                @endforeach
            </div>

            <div class="row row-cols-7 g-0">
                @while($cursor <= $end)
                    @php
                        $key          = $cursor->format('Y-m-d');
                        $inMonth      = $cursor->month === $date->month;
                        $isToday      = $cursor->isToday();
                        $dayItems     = $appointments[$key] ?? collect();
                    @endphp
                    <div class="col border p-2"
                         style="min-height: 110px;
                                {{ !$inMonth ? 'background:#f8f9fa;color:#adb5bd;' : '' }}
                                {{ $isToday ? 'border:2px solid #0d6efd;' : '' }}">
                        <div class="fw-bold mb-1">{{ $cursor->day }}</div>
                        @foreach($dayItems as $item)
                            <a href="{{ route('admin.appointments.show', $item) }}"
                               class="d-block text-truncate small text-decoration-none"
                               title="{{ $item->patient->user->name }} — Dr. {{ $item->doctor->user->name }}">
                                <span class="badge bg-{{ $item->status === 'completed' ? 'success' : ($item->status === 'cancelled' ? 'danger' : 'primary') }}">
                                    {{ $item->appointment_time ?? $item->appointment_date->format('H:i') }}
                                </span>
                                {{ $item->patient->user->name }}
                            </a>
                        @endforeach
                    </div>
                    @php $cursor->addDay(); @endphp
                @endwhile
            </div>
        </div>
    </div>
</div>
@endsection