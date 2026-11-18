@extends('layouts.app')
@section('title', __('site.user_details'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>@lang('site.user_details')</h2>
                <div>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-activation', $user) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-{{ $user->activation ? 'warning' : 'success' }}">
                                {{ $user->activation ? '🔒 Deactivate' : '✅ Activate' }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        ← Back to List
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- User Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">@lang('site.basic_information')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID:</th>
                                    <td>#{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('site.name_label')</th>
                                    <td><strong>{{ $user->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>@lang('site.email_label')</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('site.role_label')</th>
                                    <td>
                                        @php
                                            $roleBadge = [
                                                'Admin' => 'danger',
                                                'Doctor' => 'primary',
                                                'Receptionist' => 'info',
                                                'Patient' => 'success',
                                            ];
                                            $role = $user->getRoleName();
                                            $badge = $roleBadge[$role] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ $role }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">@lang('site.status_label')</th>
                                    <td>
                                        @if($user->activation)
                                            <span class="badge bg-success">@lang('site.active')</span>
                                        @else
                                            <span class="badge bg-secondary">@lang('site.inactive')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('site.registered_label')</th>
                                    <td>{{ $user->created_at->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('site.updated_label')</th>
                                    <td>{{ $user->updated_at->diffForHumans() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-Specific Information -->
            @if($user->isDoctor() && $user->doctor)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">@lang('site.doctor_information')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>@lang('site.specialization_label')</strong> {{ $user->doctor->specialization }}</p>
                                <p><strong>@lang('site.phone_label')</strong> {{ $user->doctor->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>@lang('site.city_label')</strong> {{ $user->doctor->city->name ?? 'N/A' }}</p>
                                <p><strong>@lang('site.total_appointments_label')</strong> {{ $user->doctor->appointments()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->isReceptionist() && $user->receptionist)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">@lang('site.receptionist_information')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>@lang('site.phone_label')</strong> {{ $user->receptionist->phone }}</p>
                                <p><strong>@lang('site.city_label')</strong> {{ $user->receptionist->city->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Patients @lang('site.registered_label')</strong> {{ $user->receptionist->patients()->count() }}</p>
                                <p><strong>@lang('site.appointments_created_label')</strong> {{ $user->receptionist->appointments()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->isPatient() && $user->patient)
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">@lang('site.patient_information')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>@lang('site.national_id_label')</strong> {{ $user->patient->national_id }}</p>
                                <p><strong>@lang('site.phone_label')</strong> {{ $user->patient->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>@lang('site.city_label')</strong> {{ $user->patient->city->name ?? 'N/A' }}</p>
                                <p><strong>@lang('site.total_appointments_label')</strong> {{ $user->patient->appointments()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection