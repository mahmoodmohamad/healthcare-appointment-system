@extends('layouts.app')
@section('title', '@lang('site.add_diagnosis')')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>@lang('site.add_diagnosis')</h2>
                <a href="{{ route('doctor.appointments.show', $appointment) }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Patient Info Summary -->
            <div class="card mb-4 border-info">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ $appointment->patient->user->name }}</h5>
                            <p class="mb-0">
                                <strong>@lang('site.national_id_label')</strong> {{ $appointment->patient->national_id }}<br>
                                <strong>@lang('site.appointment_label')</strong> {{ $appointment->appointment_date->format('F j, Y - g:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if($appointment->patient->phone)
                                <p class="mb-0"><strong>@lang('site.phone_label')</strong> {{ $appointment->patient->phone }}</p>
                            @endif
                            <a href="{{ route('doctor.patients.history', $appointment->patient) }}" class="btn btn-sm btn-outline-info mt-2">
                                @lang('site.view_medical_history')
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- @lang('site.diagnosis_form') -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">@lang('site.diagnosis_form')</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('doctor.diagnosis.store', $appointment) }}">
                        @csrf

                        <!-- Symptoms -->
                        <div class="mb-4">
                            <label for="symptoms" class="form-label">@lang('site.symptoms_complaints_required')</label>
                            <textarea name="symptoms" 
                                      id="symptoms" 
                                      rows="4"
                                      class="form-control @error('symptoms') is-invalid @enderror"
                                      placeholder="@lang('site.symptoms_placeholder')"
                                      required>{{ old('symptoms') }}</textarea>
                            @error('symptoms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">@lang('site.patient_reported')</small>
                        </div>

                        <!-- Diagnosis -->
                        <div class="mb-4">
                            <label for="diagnosis" class="form-label">@lang('site.diagnosis_assessment_required')</label>
                            <textarea name="diagnosis" 
                                      id="diagnosis" 
                                      rows="4"
                                      class="form-control @error('diagnosis') is-invalid @enderror"
                                      placeholder="@lang('site.diagnosis_placeholder')"
                                      required>{{ old('diagnosis') }}</textarea>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">@lang('site.professional_diagnosis')</small>
                        </div>

                        <!-- Prescription -->
                        <div class="mb-4">
                            <label for="prescription" class="form-label">@lang('site.prescription_treatment_plan')</label>
                            <textarea name="prescription" 
                                      id="prescription" 
                                      rows="6"
                                      class="form-control @error('prescription') is-invalid @enderror"
                                      placeholder="1. Medication name - dosage and frequency&#10;2. Additional instructions&#10;3. Follow-up recommendations">{{ old('prescription') }}</textarea>
                            @error('prescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">@lang('site.medications_treatments_recommendations')</small>
                        </div>

                        <!-- @lang('site.additional_notes') -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">@lang('site.additional_notes')</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="@lang('site.additional_notes_placeholder')">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Alert -->
                        <div class="alert alert-info">
                            <strong>@lang('site.note_label')</strong> @lang('site.appointment_marked_as') <strong>completed</strong> once you save the diagnosis.
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('doctor.appointments.show', $appointment) }}" class="btn btn-secondary btn-lg">
                                @lang('site.cancel')
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                💾 @lang('site.save_diagnosis')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection