@extends('layouts.app')
@section('title', 'Add Diagnosis')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Add Diagnosis</h2>
                <a href="{{ route('physician.appointments.show', $appointment) }}" class="btn btn-secondary">
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
                                <strong>National ID:</strong> {{ $appointment->patient->national_id }}<br>
                                <strong>Appointment:</strong> {{ $appointment->appointment_date->format('F j, Y - g:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if($appointment->patient->phone)
                                <p class="mb-0"><strong>Phone:</strong> {{ $appointment->patient->phone }}</p>
                            @endif
                            <a href="{{ route('physician.patients.history', $appointment->patient) }}" class="btn btn-sm btn-outline-info mt-2">
                                📋 View Medical History
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis Form -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Diagnosis Form</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('physician.diagnosis.store', $appointment) }}">
                        @csrf

                        <!-- Symptoms -->
                        <div class="mb-4">
                            <label for="symptoms" class="form-label">Symptoms / Chief Complaints *</label>
                            <textarea name="symptoms" 
                                      id="symptoms" 
                                      rows="4"
                                      class="form-control @error('symptoms') is-invalid @enderror"
                                      placeholder="Describe patient's symptoms and complaints..."
                                      required>{{ old('symptoms') }}</textarea>
                            @error('symptoms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">What the patient reported</small>
                        </div>

                        <!-- Diagnosis -->
                        <div class="mb-4">
                            <label for="diagnosis" class="form-label">Diagnosis / Assessment *</label>
                            <textarea name="diagnosis" 
                                      id="diagnosis" 
                                      rows="4"
                                      class="form-control @error('diagnosis') is-invalid @enderror"
                                      placeholder="Your medical diagnosis and assessment..."
                                      required>{{ old('diagnosis') }}</textarea>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Your professional diagnosis</small>
                        </div>

                        <!-- Prescription -->
                        <div class="mb-4">
                            <label for="prescription" class="form-label">Prescription / Treatment Plan</label>
                            <textarea name="prescription" 
                                      id="prescription" 
                                      rows="6"
                                      class="form-control @error('prescription') is-invalid @enderror"
                                      placeholder="1. Medication name - dosage and frequency&#10;2. Additional instructions&#10;3. Follow-up recommendations">{{ old('prescription') }}</textarea>
                            @error('prescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Medications, treatments, and recommendations</small>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Any additional observations or follow-up instructions...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Alert -->
                        <div class="alert alert-info">
                            <strong>ℹ️ Note:</strong> This appointment will be marked as <strong>completed</strong> once you save the diagnosis.
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('physician.appointments.show', $appointment) }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                💾 Save Diagnosis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection