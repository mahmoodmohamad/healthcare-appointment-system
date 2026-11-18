@extends('layouts.app')
@section('title', 'Patients')

@section('content')

{{-- Icon sprite (no extra icon library needed) --}}
<svg xmlns="http://www.w3.org/2000/svg" class="d-none" aria-hidden="true">
    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3 10h18M8 3v4M16 3v4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></symbol>
    <symbol id="i-eye" viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/></symbol>
    <symbol id="i-edit" viewBox="0 0 24 24"><path d="M4 20h4L19 9l-4-4L4 16v4Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="m3 7 9 6 9-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.800 7 11 7 11Z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="2.500" fill="none" stroke="currentColor" stroke-width="2"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.500" fill="none" stroke="currentColor" stroke-width="2"/><path d="M2.500 20a6.500 6.500 0 0 1 13 0M16 4.500a3.500 3.500 0 0 1 0 7M18 14.500a6.500 6.500 0 0 1 3.500 5.500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></symbol>
</svg>

<div class="container-fluid py-4 pt-page">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="pt-title">Patients</h1>
            <p class="pt-sub">
                {{ number_format($patients->total()) }}
                {{ \Illuminate\Support\Str::plural('patient', $patients->total()) }}
                @if(request('search') || request('city_id')) match your filters @else registered @endif
            </p>
        </div>
        <a href="{{ route('patients.create') }}" class="btn btn-accent d-inline-flex align-items-center gap-2 px-3 py-2">
            <svg class="icon" aria-hidden="true"><use href="#i-plus"/></svg>
            Register patient
        </a>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="status" id="flash-success">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss message"></button>
        </div>
    @endif

    {{-- Search & filter --}}
    <div class="pt-card p-3 p-md-4 mb-4 pt-filters">
        <form method="GET" action="{{ route('patients.index') }}" class="row g-3 align-items-end" role="search">
            <div class="col-12 col-md-6 col-lg-5">
                <label for="search" class="form-label">Search patients</label>
                <div class="pt-search">
                    <svg class="icon" aria-hidden="true"><use href="#i-search"/></svg>
                    <input type="search" id="search" name="search" class="form-control"
                           placeholder="Name, national ID, phone or email"
                           value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label for="city_id" class="form-label">City</label>
                <select id="city_id" name="city_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-accent flex-grow-1" style="min-height:2.75rem">Search</button>
                @if(request('search') || request('city_id'))
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" style="min-height:2.75rem">Clear</a>
                @endif
            </div>
        </form>

        {{-- Active filter chips --}}
        @if(request('search') || request('city_id'))
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                <span class="pt-meta">Filtered by</span>
                @if(request('search'))
                    <a class="pt-chip" href="{{ route('patients.index', request()->except('search', 'page')) }}"
                       aria-label="Remove search filter: {{ request('search') }}">
                        “{{ \Illuminate\Support\Str::limit(request('search'), 24) }}”
                        <svg class="icon" aria-hidden="true"><use href="#i-x"/></svg>
                    </a>
                @endif
                @if(request('city_id'))
                    @php $activeCity = $cities->firstWhere('id', request('city_id')); @endphp
                    <a class="pt-chip" href="{{ route('patients.index', request()->except('city_id', 'page')) }}"
                       aria-label="Remove city filter">
                        {{ $activeCity->name ?? 'City' }}
                        <svg class="icon" aria-hidden="true"><use href="#i-x"/></svg>
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Patients table --}}
    <div class="pt-card overflow-hidden">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table pt-table">
                    <caption class="visually-hidden">List of registered patients</caption>
                    <thead>
                        <tr>
                            <th scope="col">Patient</th>
                            <th scope="col">Contact</th>
                            <th scope="col" class="pt-hide-sm">City</th>
                            <th scope="col" class="pt-hide-sm">Registered</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            @php
                                $name = $patient->user->name ?? 'Unknown';
                                $email = $patient->user->email ?? null;
                                $initials = collect(preg_split('/\s+/', trim($name)))->filter()->take(2)
                                    ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="pt-avatar" aria-hidden="true">{{ $initials ?: '?' }}</span>
                                        <div>
                                            <a href="{{ route('patients.show', $patient) }}" class="pt-name">{{ $name }}</a>
                                            <div class="pt-meta">Patient #{{ $patient->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="pt-contact" data-label="Contact">
                                    @if($patient->phone)
                                        <div class="d-flex align-items-center gap-2">
                                            <svg class="icon pt-meta" aria-hidden="true"><use href="#i-phone"/></svg>
                                            <a href="tel:{{ $patient->phone }}">{{ $patient->phone }}</a>
                                        </div>
                                    @endif
                                    @if($email)
                                        <div class="d-flex align-items-center gap-2 pt-meta">
                                            <svg class="icon" aria-hidden="true"><use href="#i-mail"/></svg>
                                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                                        </div>
                                    @endif
                                    @unless($patient->phone || $email)
                                        <span class="pt-meta">No contact details</span>
                                    @endunless
                                </td>

                                <td class="pt-hide-sm" data-label="City">
                                    @if($patient->city)
                                        <span class="pt-city">
                                            <svg class="icon" aria-hidden="true"><use href="#i-pin"/></svg>
                                            {{ $patient->city->name }}
                                        </span>
                                    @else
                                        <span class="pt-meta">Not set</span>
                                    @endif
                                </td>

                                <td class="pt-hide-sm" data-label="Registered">
                                    <time class="pt-meta" datetime="{{ $patient->created_at->toDateString() }}"
                                          title="{{ $patient->created_at->format('d M Y, H:i') }}">
                                        {{ $patient->created_at->format('d M Y') }}
                                    </time>
                                </td>

                                <td>
                                    <div class="pt-actions">
                                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                                           class="btn btn-soft btn-sm d-inline-flex align-items-center gap-2 px-3 py-2"
                                           aria-label="Book appointment for {{ $name }}">
                                            <svg class="icon" aria-hidden="true"><use href="#i-calendar"/></svg>
                                            Book
                                        </a>
                                        <a href="{{ route('patients.show', $patient) }}" class="pt-icon-btn"
                                           title="View details" aria-label="View details for {{ $name }}">
                                            <svg class="icon" aria-hidden="true"><use href="#i-eye"/></svg>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="pt-icon-btn"
                                           title="Edit patient" aria-label="Edit {{ $name }}">
                                            <svg class="icon" aria-hidden="true"><use href="#i-edit"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer: result count + pagination --}}
            <div class="pt-foot">
                <span class="pt-meta">
                    Showing {{ $patients->firstItem() }}–{{ $patients->lastItem() }} of {{ number_format($patients->total()) }}
                </span>
                @if($patients->hasPages())
                    <nav aria-label="Patients pagination">
                        {{ $patients->appends(request()->query())->links() }}
                    </nav>
                @endif
            </div>
        @else
            {{-- Empty state --}}
            <div class="pt-empty">
                <div class="pt-empty-icon" aria-hidden="true">
                    <svg class="icon"><use href="#i-users"/></svg>
                </div>
                @if(request('search') || request('city_id'))
                    <h3>No patients match your filters</h3>
                    <p>Check the spelling, try a different city, or search by phone or national ID.</p>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Clear filters</a>
                @else
                    <h3>No patients yet</h3>
                    <p>Register your first patient to start booking appointments.</p>
                    <a href="{{ route('patients.create') }}" class="btn btn-accent d-inline-flex align-items-center gap-2">
                        <svg class="icon" aria-hidden="true"><use href="#i-plus"/></svg>
                        Register first patient
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    // Auto-dismiss the success message after 5 seconds
    (function () {
        var flash = document.getElementById('flash-success');
        if (!flash || !window.bootstrap) return;
        setTimeout(function () { bootstrap.Alert.getOrCreateInstance(flash).close(); }, 5000);
    })();
</script>
@endsection