@extends('layouts.app')
@section('title', 'All Doctors')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">@lang('site.doctors')</h2>
            <p class="text-muted mb-0">@lang('site.all_doctors_description')</p>
        </div>
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
            ➕ Add Doctor
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
                    <label class="form-label">@lang('site.search')</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Name, email, specialization, phone..."
                           value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">@lang('site.city')</label>
                    <select name="city_id" class="form-select">
                        <option value="">@lang('site.all_cities')</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ ($filters['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary flex-grow-1">@lang('site.filter')</button>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">@lang('site.clear')</a>
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
                                <th>@lang('site.id')</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.email')</th>
                                <th>@lang('site.specialty')</th>
                                <th>@lang('site.phone')</th>
                                <th>@lang('site.city')</th>
                                <th>@lang('site.appointments')</th>
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
                                           class="btn btn-sm btn-info">@lang('site.view')</a>
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
                <p class="text-center text-muted py-5 mb-0">@lang('site.no_doctors_found')</p>
            @endif
        </div>
    </div>
</div>
@endsection