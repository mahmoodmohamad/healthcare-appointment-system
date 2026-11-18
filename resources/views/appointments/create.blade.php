
@extends('layouts.app')

@section('title', 'Book New Appointment')

@section('content')


<div class="ui-page booking-page container-fluid py-4">

    {{-- Header --}}
    <div class="ui-page-head booking-header">
        <div>
            <h1 class="ui-title page-title">Book New Appointment</h1>
            <p class="ui-sub page-subtitle">
                Schedule a patient visit by selecting a doctor, date and time.
            </p>
        </div>

        <a href="{{ route('appointments.index') }}" class="back-link">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            Back to Appointments
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-circle-fill mt-1"
                   aria-hidden="true"></i>

                <div>
                    <strong>We couldn't save this appointment.</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ route('appointments.store') }}"
          id="appointmentForm">

        @csrf

        <div class="booking-layout">

            {{-- Main Form --}}
            <div class="ui-card booking-card">

                <div class="card-top">
                    <h2>Appointment Information</h2>
                    <p>Complete the information below to create a new booking.</p>
                </div>

                <div class="booking-body">

                    {{-- Section 1: Patient --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">01</span>
                            Patient Information
                        </h3>

                        <div>
                            <label for="patient_id" class="form-label">
                                Select Patient <span class="required-mark">*</span>
                            </label>

                            <div class="field-icon">
                                <i class="bi bi-person" aria-hidden="true"></i>

                                <select name="patient_id"
                                        id="patient_id"
                                        class="ui-input form-select @error('patient_id') is-invalid @enderror"
                                        required>
                                    <option value="">Choose a patient</option>

                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}"
                                            data-name="{{ $patient->user?->name ?? '' }}"
                                            data-national-id="{{ $patient->national_id ?? '' }}"
                                            @selected(old('patient_id') == $patient->id)>
                                            {{ $patient->user?->name ?? 'Unknown patient' }}
                                            - {{ $patient->national_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('patient_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <div class="form-hint">
                                Select the patient who will attend this appointment.
                            </div>
                        </div>
                    </section>

                    {{-- Section 2: Doctor --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">02</span>
                            Doctor Selection
                        </h3>

                        <div>
                            <label for="doctor_id" class="form-label">
                                Choose Doctor <span class="required-mark">*</span>
                            </label>

                            <div class="field-icon">
                                <i class="bi bi-person-badge" aria-hidden="true"></i>

                                <select name="doctor_id"
                                        id="doctor_id"
                                        class="ui-input form-select @error('doctor_id') is-invalid @enderror"
                                        required>
                                    <option value="">Select a doctor</option>

                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}"
                                            data-name="{{ $doctor->user?->name ?? '' }}"
                                            data-specialization="{{ $doctor->specialization ?? '' }}"
                                            @selected(old('doctor_id') == $doctor->id)>
                                            Dr. {{ $doctor->user?->name ?? 'Unknown doctor' }}
                                            - {{ $doctor->specialization }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('doctor_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <div class="form-hint">
                                Choose the doctor responsible for this visit.
                            </div>
                        </div>
                    </section>

                    {{-- Section 3: Date and Time --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">03</span>
                            Date & Time
                        </h3>

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label for="appointment_date" class="form-label">
                                    Appointment Date <span class="required-mark">*</span>
                                </label>

                                <div class="field-icon">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>

                                    <input type="date"
                                           name="appointment_date"
                                           id="appointment_date"
                                           class="ui-input form-control @error('appointment_date') is-invalid @enderror"
                                           value="{{ old('appointment_date') }}"
                                           min="{{ now()->toDateString() }}"
                                           required>
                                </div>

                                @error('appointment_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="appointment_time" class="form-label">
                                    Appointment Time <span class="required-mark">*</span>
                                </label>

                                <div class="field-icon">
                                    <i class="bi bi-clock" aria-hidden="true"></i>

                                    <select name="appointment_time"
                                            id="appointment_time"
                                            class="ui-input form-select @error('appointment_time') is-invalid @enderror"
                                            required>

                                        <option value="">Select a time</option>

                                        <optgroup label="Morning">
                                            @for($hour = 9; $hour < 12; $hour++)
                                                @foreach(['00', '30'] as $minute)
                                                    @php
                                                        $time = sprintf('%02d:%s', $hour, $minute);
                                                    @endphp

                                                    <option value="{{ $time }}"
                                                        @selected(old('appointment_time') == $time)>
                                                        {{ \Illuminate\Support\Carbon::createFromFormat('H:i', $time)->format('h:i A') }}
                                                    </option>
                                                @endforeach
                                            @endfor
                                        </optgroup>

                                        <optgroup label="Afternoon">
                                            @for($hour = 14; $hour < 17; $hour++)
                                                @foreach(['00', '30'] as $minute)
                                                    @php
                                                        $time = sprintf('%02d:%s', $hour, $minute);
                                                    @endphp

                                                    <option value="{{ $time }}"
                                                        @selected(old('appointment_time') == $time)>
                                                        {{ \Illuminate\Support\Carbon::createFromFormat('H:i', $time)->format('h:i A') }}
                                                    </option>
                                                @endforeach
                                            @endfor
                                        </optgroup>

                                    </select>
                                </div>

                                @error('appointment_time')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="availability-note">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <div>
                                <strong>Appointment availability</strong>
                                <span>
                                    Available times depend on the doctor's schedule.
                                    The booking will be validated when submitted.
                                </span>
                            </div>
                        </div>
                    </section>

                    {{-- Section 4: Notes --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">04</span>
                            Additional Information
                            <span class="text-muted fw-normal small">(Optional)</span>
                        </h3>

                        <label for="notes" class="form-label">
                            Appointment Notes
                        </label>

                        <textarea name="notes"
                                  id="notes"
                                  rows="3"
                                  maxlength="1000"
                                  class="ui-input form-control @error('notes') is-invalid @enderror"
                                  placeholder="Add any relevant administrative notes...">{{ old('notes') }}</textarea>

                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="form-hint">
                            Keep notes relevant to scheduling and appointment administration.
                        </div>
                    </section>

                    {{-- Actions --}}
                    <div class="form-actions">
                        <a href="{{ route('appointments.index') }}"
                           class="ui-btn ui-btn-ghost btn btn-light">
                            Cancel
                        </a>

                        <button type="submit"
                                class="ui-btn ui-btn-primary btn btn-primary"
                                id="submitAppointment">
                            <i class="bi bi-calendar-check me-2"
                               aria-hidden="true"></i>
                            Confirm Appointment
                        </button>
                    </div>

                </div>
            </div>

            {{-- Booking Summary --}}
            <aside class="ui-card summary-card" aria-labelledby="summary-heading">

                <div class="summary-placeholder">
                    <i class="bi bi-calendar2-check" aria-hidden="true"></i>
                </div>

                <h2 class="summary-heading" id="summary-heading">
                    Booking Summary
                </h2>

                <p class="summary-description">
                    Your appointment details will appear here as you complete the form.
                </p>

                <div class="summary-item">
                    <div class="summary-label">Patient</div>
                    <div class="summary-value" id="summaryPatient">
                        Not selected
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Doctor</div>
                    <div class="summary-value" id="summaryDoctor">
                        Not selected
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Specialization</div>
                    <div class="summary-value" id="summarySpecialization">
                        Not selected
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Appointment Date</div>
                    <div class="summary-value" id="summaryDate">
                        Not selected
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Appointment Time</div>
                    <div class="summary-value" id="summaryTime">
                        Not selected
                    </div>
                </div>

                <div class="summary-footer">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    <p>
                        Please review the appointment details before confirming.
                        The system will validate the selected time slot.
                    </p>
                </div>

            </aside>

        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const patientSelect = document.getElementById('patient_id');
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const appointmentForm = document.getElementById('appointmentForm');

    const summaryPatient = document.getElementById('summaryPatient');
    const summaryDoctor = document.getElementById('summaryDoctor');
    const summarySpecialization = document.getElementById('summarySpecialization');
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');

    function selectedOption(select) {
        return select.options[select.selectedIndex];
    }

    function updateSummary() {
        const patientOption = selectedOption(patientSelect);
        const doctorOption = selectedOption(doctorSelect);

        summaryPatient.textContent =
            patientSelect.value && patientOption
                ? patientOption.dataset.name || patientOption.textContent.trim()
                : 'Not selected';

        summaryDoctor.textContent =
            doctorSelect.value && doctorOption
                ? 'Dr. ' + (doctorOption.dataset.name || doctorOption.textContent.trim())
                : 'Not selected';

        summarySpecialization.textContent =
            doctorSelect.value && doctorOption
                ? doctorOption.dataset.specialization || 'Not specified'
                : 'Not selected';

        if (dateInput.value) {
            const date = new Date(dateInput.value + 'T00:00:00');

            summaryDate.textContent = date.toLocaleDateString(undefined, {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } else {
            summaryDate.textContent = 'Not selected';
        }

        if (timeSelect.value) {
            const [hours, minutes] = timeSelect.value.split(':');
            const time = new Date();

            time.setHours(Number(hours), Number(minutes), 0, 0);

            summaryTime.textContent = time.toLocaleTimeString(undefined, {
                hour: '2-digit',
                minute: '2-digit'
            });
        } else {
            summaryTime.textContent = 'Not selected';
        }
    }

    [patientSelect, doctorSelect, dateInput, timeSelect].forEach(function (element) {
        element.addEventListener('change', updateSummary);
    });

    updateSummary();

    // Prevent duplicate submissions while the request is being sent.
    appointmentForm.addEventListener('submit', function () {
        const submitButton = document.getElementById('submitAppointment');

        if (!appointmentForm.checkValidity()) {
            return;
        }

        submitButton.disabled = true;
        submitButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Booking...';
    });
});
</script>
@endsection
