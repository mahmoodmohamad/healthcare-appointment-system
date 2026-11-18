
@extends('layouts.app')

@section('title', __('site.book_new_appointment'))

@section('content')

@php
    $bookingCopy = app()->getLocale() === 'ar' ? [
        'intro' => 'حدّد المريض والطبيب والتاريخ والوقت لإنشاء الموعد.',
        'back' => 'العودة إلى المواعيد',
        'unknown_patient' => 'مريض غير معروف',
        'unknown_doctor' => 'طبيب غير معروف',
        'patient_hint' => 'اختر المريض الذي سيحضر هذا الموعد.',
        'doctor_section' => 'اختيار الطبيب',
        'choose_doctor' => 'اختر الطبيب',
        'doctor_hint' => 'اختر الطبيب المسؤول عن هذه الزيارة.',
        'date_time' => 'التاريخ والوقت',
        'morning' => 'صباحًا',
        'afternoon' => 'بعد الظهر',
        'availability_hint' => 'تعتمد الأوقات المتاحة على جدول الطبيب. سيتم التحقق من الموعد عند الإرسال.',
        'additional' => 'معلومات إضافية',
        'notes_label' => 'ملاحظات الموعد',
        'notes_hint' => 'أضف ملاحظات مرتبطة بجدولة الموعد وإجراءاته الإدارية.',
        'confirm' => 'تأكيد الموعد',
        'summary' => 'ملخص الموعد',
        'summary_intro' => 'ستظهر تفاصيل الموعد هنا عند استكمال البيانات.',
        'not_selected' => 'لم يتم الاختيار',
        'not_specified' => 'غير محدد',
        'review' => 'يرجى مراجعة تفاصيل الموعد قبل التأكيد. سيتحقق النظام من الوقت المحدد.',
        'booking' => 'جارٍ الحجز...',
    ] : [
        'intro' => 'Schedule a patient visit by selecting a doctor, date and time.',
        'back' => 'Back to Appointments',
        'unknown_patient' => 'Unknown patient',
        'unknown_doctor' => 'Unknown doctor',
        'patient_hint' => 'Select the patient who will attend this appointment.',
        'doctor_section' => 'Doctor Selection',
        'choose_doctor' => 'Choose Doctor',
        'doctor_hint' => 'Choose the doctor responsible for this visit.',
        'date_time' => 'Date & Time',
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'availability_hint' => 'Available times depend on the doctor schedule. The booking will be validated when submitted.',
        'additional' => 'Additional Information',
        'notes_label' => 'Appointment Notes',
        'notes_hint' => 'Keep notes relevant to scheduling and appointment administration.',
        'confirm' => 'Confirm Appointment',
        'summary' => 'Booking Summary',
        'summary_intro' => 'Your appointment details will appear here as you complete the form.',
        'not_selected' => 'Not selected',
        'not_specified' => 'Not specified',
        'review' => 'Please review the appointment details before confirming. The system will validate the selected time slot.',
        'booking' => 'Booking...',
    ];
@endphp


<div class="ui-page booking-page container-fluid py-4">

    {{-- Header --}}
    <div class="ui-page-head booking-header">
        <div>
            <h1 class="ui-title page-title">@lang('site.book_new_appointment')</h1>
            <p class="ui-sub page-subtitle">
                {{ $bookingCopy['intro'] }}
            </p>
        </div>

        <a href="{{ route('appointments.index') }}" class="back-link">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            {{ $bookingCopy['back'] }}
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-circle-fill mt-1"
                   aria-hidden="true"></i>

                <div>
                    <strong>@lang('site.appointment_save_error')</strong>
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
                    <h2>@lang('site.appointment_information')</h2>
                    <p>@lang('site.complete_booking_information')</p>
                </div>

                <div class="booking-body">

                    {{-- Section 1: @lang('site.patient') --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">01</span>
                            @lang('site.patient_information')
                        </h3>

                        <div>
                            <label for="patient_id" class="form-label">
                                @lang('site.select_patient') <span class="required-mark">*</span>
                            </label>

                            <div class="field-icon">
                                <i class="bi bi-person" aria-hidden="true"></i>

                                <select name="patient_id"
                                        id="patient_id"
                                        class="ui-input form-select @error('patient_id') is-invalid @enderror"
                                        required>
                                    <option value="">@lang('site.choose_patient')</option>

                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}"
                                            data-name="{{ $patient->user?->name ?? '' }}"
                                            data-national-id="{{ $patient->national_id ?? '' }}"
                                            @selected(old('patient_id') == $patient->id)>
                                            {{ $patient->user?->name ?? $bookingCopy['unknown_patient'] }}
                                            - {{ $patient->national_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('patient_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <div class="form-hint">
                                {{ $bookingCopy['patient_hint'] }}
                            </div>
                        </div>
                    </section>

                    {{-- Section 2: Doctor --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">02</span>
                            {{ $bookingCopy['doctor_section'] }}
                        </h3>

                        <div>
                            <label for="doctor_id" class="form-label">
                                {{ $bookingCopy['choose_doctor'] }} <span class="required-mark">*</span>
                            </label>

                            <div class="field-icon">
                                <i class="bi bi-person-badge" aria-hidden="true"></i>

                                <select name="doctor_id"
                                        id="doctor_id"
                                        class="ui-input form-select @error('doctor_id') is-invalid @enderror"
                                        required>
                                    <option value="">@lang('site.select_doctor')</option>

                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}"
                                            data-name="{{ $doctor->user?->name ?? '' }}"
                                            data-specialization="{{ $doctor->specialization ?? '' }}"
                                            @selected(old('doctor_id') == $doctor->id)>
                                            Dr. {{ $doctor->user?->name ?? $bookingCopy['unknown_doctor'] }}
                                            - {{ $doctor->specialization }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('doctor_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <div class="form-hint">
                                {{ $bookingCopy['doctor_hint'] }}
                            </div>
                        </div>
                    </section>

                    {{-- Section 3: Date and Time --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">03</span>
                            {{ $bookingCopy['date_time'] }}
                        </h3>

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label for="appointment_date" class="form-label">
                                    @lang('site.appointment_date') <span class="required-mark">*</span>
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
                                    @lang('site.appointment_time') <span class="required-mark">*</span>
                                </label>

                                <div class="field-icon">
                                    <i class="bi bi-clock" aria-hidden="true"></i>

                                    <select name="appointment_time"
                                            id="appointment_time"
                                            class="ui-input form-select @error('appointment_time') is-invalid @enderror"
                                            required>

                                        <option value="">@lang('site.select_time')</option>

                                        <optgroup label="{{ $bookingCopy['morning'] }}">
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

                                        <optgroup label="{{ $bookingCopy['afternoon'] }}">
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
                                <strong>@lang('site.appointment_availability')</strong>
                                <span>
                                    {{ $bookingCopy['availability_hint'] }}
                                </span>
                            </div>
                        </div>
                    </section>

                    {{-- Section 4: Notes --}}
                    <section class="form-section">
                        <h3 class="section-title">
                            <span class="section-number">04</span>
                            {{ $bookingCopy['additional'] }}
                            <span class="text-muted fw-normal small">@lang('site.optional_parenthetical')</span>
                        </h3>

                        <label for="notes" class="form-label">
                            {{ $bookingCopy['notes_label'] }}
                        </label>

                        <textarea name="notes"
                                  id="notes"
                                  rows="3"
                                  maxlength="1000"
                                  class="ui-input form-control @error('notes') is-invalid @enderror"
                                  placeholder="@lang('site.administrative_notes_placeholder')">{{ old('notes') }}</textarea>

                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="form-hint">
                            {{ $bookingCopy['notes_hint'] }}
                        </div>
                    </section>

                    {{-- Actions --}}
                    <div class="form-actions">
                        <a href="{{ route('appointments.index') }}"
                           class="ui-btn ui-btn-ghost btn btn-light">
                            @lang('site.cancel')
                        </a>

                        <button type="submit"
                                class="ui-btn ui-btn-primary btn btn-primary"
                                id="submitAppointment">
                            <i class="bi bi-calendar-check me-2"
                               aria-hidden="true"></i>
                            {{ $bookingCopy['confirm'] }}
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
                    {{ $bookingCopy['summary'] }}
                </h2>

                <p class="summary-description">
                    {{ $bookingCopy['summary_intro'] }}
                </p>

                <div class="summary-item">
                    <div class="summary-label">@lang('site.patient')</div>
                    <div class="summary-value" id="summaryPatient">
                        {{ $bookingCopy['not_selected'] }}
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">@lang('site.doctor')</div>
                    <div class="summary-value" id="summaryDoctor">
                        {{ $bookingCopy['not_selected'] }}
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">@lang('site.specialty')</div>
                    <div class="summary-value" id="summarySpecialty">
                        {{ $bookingCopy['not_selected'] }}
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">@lang('site.appointment_date')</div>
                    <div class="summary-value" id="summaryDate">
                        {{ $bookingCopy['not_selected'] }}
                    </div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">@lang('site.appointment_time')</div>
                    <div class="summary-value" id="summaryTime">
                        {{ $bookingCopy['not_selected'] }}
                    </div>
                </div>

                <div class="summary-footer">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    <p>
                        {{ $bookingCopy['review'] }}
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
    const summarySpecialty = document.getElementById('summarySpecialty');
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');
    const copy = @json($bookingCopy);

    function selectedOption(select) {
        return select.options[select.selectedIndex];
    }

    function updateSummary() {
        const patientOption = selectedOption(patientSelect);
        const doctorOption = selectedOption(doctorSelect);

        summaryPatient.textContent =
            patientSelect.value && patientOption
                ? patientOption.dataset.name || patientOption.textContent.trim()
                : copy.not_selected;

        summaryDoctor.textContent =
            doctorSelect.value && doctorOption
                ? '{{ __('site.doctor') }} ' + (doctorOption.dataset.name || doctorOption.textContent.trim())
                : copy.not_selected;

        summarySpecialty.textContent =
            doctorSelect.value && doctorOption
                ? doctorOption.dataset.specialization || copy.not_specified
                : copy.not_selected;

        if (dateInput.value) {
            const date = new Date(dateInput.value + 'T00:00:00');

            summaryDate.textContent = date.toLocaleDateString(undefined, {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } else {
            summaryDate.textContent = copy.not_selected;
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
            summaryTime.textContent = copy.not_selected;
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
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ' + copy.booking;
    });
});
</script>
@endsection
