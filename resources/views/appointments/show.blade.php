@extends('layouts.app')
@section('title', 'Appointment Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Appointment Details</h2>
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                    ← Back to List
                </a>
            </div>

            <!-- Appointment Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Appointment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID:</th>
                                    <td>#{{ $appointment->id }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $appointment->appointment_date->format('l, F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Time:</th>
                                    <td>{{ $appointment->appointment_time ?? $appointment->appointment_date->format('g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($appointment->status == 'scheduled')
                                            <span class="badge bg-primary">Scheduled</span>
                                        @elseif($appointment->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($appointment->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Booked:</th>
                                    <td>{{ $appointment->created_at->format('Y-m-d h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Booked By:</th>
                                    <td>
                                        @if($appointment->secretary)
                                            {{ $appointment->secretary->user->name }} (Secretary)
                                        @else
                                            System
                                        @endif
                                    </td>
                                </tr>
                                @if($appointment->notes)
                                <tr>
                                    <th>Notes:</th>
                                    <td>{{ $appointment->notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Name:</th>
                                    <td>{{ $appointment->patient->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>National ID:</th>
                                    <td>{{ $appointment->patient->national_id }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $appointment->patient->phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Email:</th>
                                    <td>{{ $appointment->patient->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>{{ $appointment->patient->city->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physician Info -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Physician Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Name:</th>
                                    <td>Dr. {{ $appointment->physician->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Specialization:</th>
                                    <td>{{ $appointment->physician->specialization }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Phone:</th>
                                    <td>{{ $appointment->physician->phone }}</td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>{{ $appointment->physician->city->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis Info -->
            @if($appointment->diagnosis)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Diagnosis</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Symptoms:</strong>
                        <p>{{ $appointment->diagnosis->symptoms }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Diagnosis:</strong>
                        <p>{{ $appointment->diagnosis->diagnosis }}</p>
                    </div>
                    @if($appointment->diagnosis->prescription)
                    <div class="mb-3">
                        <strong>Prescription:</strong>
                        <p>{{ $appointment->diagnosis->prescription }}</p>
                    </div>
                    @endif
                    <small class="text-muted">
                        Diagnosed on: {{ $appointment->diagnosis->created_at->format('Y-m-d h:i A') }}
                    </small>
                </div>
            </div>
            @else
                @if($appointment->status == 'completed')
                <div class="alert alert-info">
                    This appointment is completed but no diagnosis has been recorded yet.
                </div>
                @endif
            @endif

            <!-- Actions -->
            @if($appointment->status == 'scheduled')
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Actions</h5>
                    <form action="{{ route('appointments.destroy', $appointment) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            ❌ Cancel Appointment
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection