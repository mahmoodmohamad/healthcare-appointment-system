@extends('layouts.app')
@section('title', 'New Appointment')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Book New Appointment</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf

                        <!-- Patient Selection -->
                        <div class="mb-3">
                            <label for="patient_id" class="form-label">Patient *</label>
                            <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->user->name }} - {{ $patient->national_id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Doctor Selection -->
                        <div class="mb-3">
                            <label for="doctor_id" class="form-label">Doctor *</label>
                            <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label for="appointment_date" class="form-label">Appointment Date *</label>
                            <input type="date" 
                                   name="appointment_date" 
                                   id="appointment_date" 
                                   class="form-control @error('appointment_date') is-invalid @enderror"
                                   value="{{ old('appointment_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('appointment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label for="appointment_time" class="form-label">Appointment Time *</label>
                            <select name="appointment_time" id="appointment_time" class="form-select @error('appointment_time') is-invalid @enderror" required>
                                <option value="">Select Time</option>
                                <!-- Morning Slots -->
                                <optgroup label="Morning (9 AM - 12 PM)">
                                    @for($hour = 9; $hour < 12; $hour++)
                                        @foreach(['00', '30'] as $minute)
                                            @php $time = sprintf('%02d:%s', $hour, $minute); @endphp
                                            <option value="{{ $time }}" {{ old('appointment_time') == $time ? 'selected' : '' }}>
                                                {{ date('g:i A', strtotime($time)) }}
                                            </option>
                                        @endforeach
                                    @endfor
                                </optgroup>
                                <!-- Afternoon Slots -->
                                <optgroup label="Afternoon (2 PM - 5 PM)">
                                    @for($hour = 14; $hour < 17; $hour++)
                                        @foreach(['00', '30'] as $minute)
                                            @php $time = sprintf('%02d:%s', $hour, $minute); @endphp
                                            <option value="{{ $time }}" {{ old('appointment_time') == $time ? 'selected' : '' }}>
                                                {{ date('g:i A', strtotime($time)) }}
                                            </option>
                                        @endforeach
                                    @endfor
                                </optgroup>
                            </select>
                            @error('appointment_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Optional: Load available slots dynamically when doctor and date are selected
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');

    function loadAvailableSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (doctorId && date) {
            // You can implement AJAX call to getAvailableSlots here
            console.log('Loading slots for doctor:', doctorId, 'on date:', date);
            // fetch(`/appointments/available-slots?doctor_id=${doctorId}&date=${date}`)
            //     .then(response => response.json())
            //     .then(slots => {
            //         // Update time select with available slots
            //     });
        }
    }

    doctorSelect.addEventListener('change', loadAvailableSlots);
    dateInput.addEventListener('change', loadAvailableSlots);
});
</script>
@endsection
@endsection