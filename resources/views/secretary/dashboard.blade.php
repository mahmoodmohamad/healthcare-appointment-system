@extends('layouts.app')
@section('title', 'Secretary Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Welcome, {{ auth()->user()->name }}</h2>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->secretary->patients()->count() }}</h3>
                    <p class="mb-0">Patients Registered</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->secretary->appointments()->count() }}</h3>
                    <p class="mb-0">Appointments Created</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->secretary->appointments()->whereDate('created_at', today())->count() }}</h3>
                    <p class="mb-0">Today's Bookings</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->secretary->appointments()->where('status', 'scheduled')->count() }}</h3>
                    <p class="mb-0">Active Appointments</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('patients.create') }}" class="btn btn-primary btn-lg">
                            ➕ Register New Patient
                        </a>
                        <a href="{{ route('appointments.create') }}" class="btn btn-success btn-lg">
                            📅 Book Appointment
                        </a>
                        <a href="{{ route('patients.index') }}" class="btn btn-info btn-lg">
                            👥 View All Patients
                        </a>
                        <a href="{{ route('appointments.calendar') }}" class="btn btn-warning btn-lg">
                            📆 View Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Patients</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentPatients = auth()->user()->secretary->patients()->with('user')->latest()->take(5)->get();
                    @endphp
                    @if($recentPatients->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPatients as $patient)
                            <a href="{{ route('patients.show', $patient) }}" class="list-group-item list-group-item-action">
                                <strong>{{ $patient->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $patient->created_at->diffForHumans() }}</small>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No patients registered yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection