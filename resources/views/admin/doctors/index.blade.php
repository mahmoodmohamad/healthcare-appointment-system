@extends('layouts.app')
@section('title', '{{ __('site.all') }} {{ __('site.doctor') }}s')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ __('site.doctor') }}s</h2>
            <p class="text-muted mb-0">{{ __('site.all_doctors_description') }}</p>
        </div>
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
            ➕ Add {{ __('site.doctor') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.doctors.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('site.search') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('site.name') }}, email, specialization, phone..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary flex-grow-1">{{ __('site.filter') }}</button>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">{{ __('site.clear') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($doctors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('site.id') }}</th>
                                <th>{{ __('site.name') }}</th>
                                <th>{{ __('site.email') }}</th>
                                <th>{{ __('site.specialty') }}</th>
                                <th>{{ __('site.phone') }}</th>
                                <th>{{ __('site.city') }}</th>
                                <th>{{ __('site.appointment') }}s</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctors as $doctor)
                                <tr>
                                    <td>#{{ $doctor->id }}</td>
                                    <td><strong>{{ $doctor->user->name }}</strong></td>
                                    <td>{{ $doctor->user->email }}</td>
                                    <td><span class="badge bg-info">{{ $doctor->specialization }}</span></td>
                                    <td>{{ $doctor->phone }}</td>
                                    <td>{{ $doctor->city->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $doctor->appointments_count }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.doctors.show', $doctor) }}"
                                           class="btn btn-sm btn-info">{{ __('site.view') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $doctors->links() }}
                </div>
            @else
                <p class="text-center text-muted py-5 mb-0">{{ __('site.no_doctors_found') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection