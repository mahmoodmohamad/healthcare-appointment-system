@extends('layouts.app')
@section('title', '{{ __('site.all') }} {{ __('site.patient') }}s')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ __('site.patient') }}s</h2>
            <p class="text-muted mb-0">{{ __('site.patient_registry_description') }}</p>
        </div>
        <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">
            ➕ Register {{ __('site.patient') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- {{ __('site.filter') }}s -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.patients.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('site.search') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('site.name') }}, email, national {{ __('site.id') }}, phone..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('site.city') }}</label>
                    <select name="city_id" class="form-select">
                        <option value="">{{ __('site.all_cities') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ ($filters['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('site.receptionist') }}</label>
                    <select name="receptionist_id" class="form-select">
                        <option value="">{{ __('site.all') }}</option>
                        @foreach($receptionists as $r)
                            <option value="{{ $r->id }}"
                                {{ ($filters['receptionist_id'] ?? '') == $r->id ? 'selected' : '' }}>
                                {{ $r->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('site.gender') }}</label>
                    <select name="gender" class="form-select">
                        <option value="">{{ __('site.all') }}</option>
                        <option value="male"   {{ ($filters['gender'] ?? '') === 'male'   ? 'selected' : '' }}>{{ __('site.male') }}</option>
                        <option value="female" {{ ($filters['gender'] ?? '') === 'female' ? 'selected' : '' }}>{{ __('site.female') }}</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-primary flex-grow-1">{{ __('site.filter') }}</button>
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary">{{ __('site.clear') }}</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('site.id') }}</th>
                                <th>{{ __('site.name') }}</th>
                                <th>National {{ __('site.id') }}</th>
                                <th>{{ __('site.phone') }}</th>
                                <th>{{ __('site.city') }}</th>
                                <th>{{ __('site.gender') }}</th>
                                <th>{{ __('site.appointment') }}s</th>
                                <th>{{ __('site.receptionist') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td>#{{ $patient->id }}</td>
                                    <td>
                                        <strong>{{ $patient->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $patient->user->email }}</small>
                                    </td>
                                    <td>{{ $patient->national_id }}</td>
                                    <td>{{ $patient->phone }}</td>
                                    <td>{{ $patient->city->name ?? '—' }}</td>
                                    <td>
                                        @if($patient->gender)
                                            <span class="badge bg-{{ $patient->gender === 'male' ? 'info' : 'secondary' }}">
                                                {{ ucfirst($patient->gender) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $patient->appointments_count }}</span>
                                    </td>
                                    <td>
                                        {{ $patient->receptionist->user->name ?? '—' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.patients.show', $patient) }}"
                                           class="btn btn-sm btn-info">{{ __('site.view') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $patients->links() }}
                </div>
            @else
                <p class="text-center text-muted py-5 mb-0">{{ __('site.no_patients_filter') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection