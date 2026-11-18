@extends('layouts.app')
@section('title', __('site.patients'))

@section('content')

{{-- Icon sprite (no extra ui-icon library needed) --}}
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

<div class="ui-page container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="ui-title">@lang('site.patients')</h1>
            <p class="ui-sub">
                {{ number_format($patients->total()) }} @lang('site.patients')
                @if(request('search') || request('city_id')) @lang('site.matching_filters') @else @lang('site.registered') @endif
            </p>
        </div>
        <a href="{{ route('patients.create') }}" class="btn ui-btn ui-btn-primary d-inline-flex align-items-center gap-2 px-3 py-2">
            <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-plus"/></svg>
            @lang('site.register_patient')
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
    <div class="ui-card p-3 p-md-4 mb-4">
        <form method="GET" action="{{ route('patients.index') }}" class="row g-3 align-items-end" role="search">
            <div class="col-12 col-md-6 col-lg-5">
                <label for="search" class="form-label">@lang('site.search_patients')</label>
                <div class="ui-search">
                    <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"/></svg>
                    <input type="search" id="search" name="search" class="form-control ui-input"
                           placeholder="@lang('site.patient_search_placeholder')"
                           value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label for="city_id" class="form-label">@lang('site.city')</label>
                <select id="city_id" name="city_id" class="form-select ui-input" >
                    <option value="">@lang('site.all_cities')</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn ui-btn ui-btn-primary flex-grow-1">@lang('site.search')</button>
                @if(request('search') || request('city_id'))
                    <a href="{{ route('patients.index') }}" class="ui-btn ui-btn-ghost">@lang('site.clear')</a>
                @endif
            </div>
        </form>

        {{-- Active filter chips --}}
        @if(request('search') || request('city_id'))
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                <span class="ui-meta">@lang('site.filtered_by')</span>
                @if(request('search'))
                    <a class="ui-tab" href="{{ route('patients.index', request()->except('search', 'page')) }}"
                       aria-label="Remove search filter: {{ request('search') }}">
                        “{{ \Illuminate\Support\Str::limit(request('search'), 24) }}”
                        <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-x"/></svg>
                    </a>
                @endif
                @if(request('city_id'))
                    @php $activeCity = $cities->firstWhere('id', request('city_id')); @endphp
                    <a class="ui-tab" href="{{ route('patients.index', request()->except('city_id', 'page')) }}"
                       aria-label="Remove city filter">
                        {{ $activeCity->name ?? __('site.city') }}
                        <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-x"/></svg>
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- @lang('site.patient')s table --}}
    <div class="ui-card overflow-hidden">
        @if($patients->count() > 0)
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <caption class="visually-hidden">@lang('site.registered_patients_list')</caption>
                    <thead>
                        <tr>
                            <th scope="col">@lang('site.patient')</th>
                            <th scope="col">@lang('site.contact')</th>
                            <th scope="col" >@lang('site.city')</th>
                            <th scope="col" >@lang('site.registered')</th>
                            <th scope="col" class="text-end">@lang('site.actions')</th>
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
                                <td data-label="@lang('site.patient')">
                                    <div class="ui-cell-person">
                                        <span class="ui-avatar" aria-hidden="true">{{ $initials ?: '?' }}</span>
                                        <div>
                                            <a href="{{ route('patients.show', $patient) }}" class="ui-link">{{ $name }}</a>
                                            <div class="ui-meta">@lang('site.patient') #{{ $patient->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td  data-label="@lang('site.contact')">
                                    @if($patient->phone)
                                        <div class="d-flex align-items-center gap-2">
                                            <svg class="ui-icon ui-meta" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-phone"/></svg>
                                            <a href="tel:{{ $patient->phone }}">{{ $patient->phone }}</a>
                                        </div>
                                    @endif
                                    @if($email)
                                        <div class="d-flex align-items-center gap-2 ui-meta">
                                            <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-mail"/></svg>
                                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                                        </div>
                                    @endif
                                    @unless($patient->phone || $email)
                                        <span class="ui-meta">@lang('site.no_contact_details')</span>
                                    @endunless
                                </td>

                                <td  data-label="@lang('site.city')">
                                    @if($patient->city)
                                        <span class="ui-chip">
                                            <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-pin"/></svg>
                                            {{ $patient->city->name }}
                                        </span>
                                    @else
                                        <span class="ui-meta">@lang('site.not_set')</span>
                                    @endif
                                </td>

                                <td  data-label="@lang('site.registered')">
                                    <time class="ui-meta" datetime="{{ $patient->created_at->toDateString() }}"
                                          title="{{ $patient->created_at->translatedFormat('d M Y, H:i') }}">
                                        {{ $patient->created_at->translatedFormat('d M Y') }}
                                    </time>
                                </td>

                                <td data-label="@lang('site.actions')">
                                    <div class="ui-row-actions">
                                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                                           class="btn ui-btn ui-btn-ghost btn-sm d-inline-flex align-items-center gap-2 px-3 py-2"
                                           aria-label="Book appointment for {{ $name }}">
                                            <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-calendar"/></svg>
                                            @lang('site.book_appointment')
                                        </a>
                                        <a href="{{ route('patients.show', $patient) }}" class="ui-btn ui-btn-ghost ui-btn-sm"
                                           title="@lang('site.view_details')" aria-label="@lang('site.view_details') — {{ $name }}">
                                            <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-eye"/></svg>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="ui-btn ui-btn-ghost ui-btn-sm"
                                           title="@lang('site.edit_patient')" aria-label="@lang('site.edit_patient') — {{ $name }}">
                                            <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-edit"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer: result count + pagination --}}
            <div class="ui-card-foot ui-pagination">
                <span class="ui-meta">
                    @lang('site.showing') {{ $patients->firstItem() }}–{{ $patients->lastItem() }} @lang('site.of') {{ number_format($patients->total()) }}
                </span>
                @if($patients->hasPages())
                    <nav aria-label="@lang('site.patients') pagination">
                        {{ $patients->appends(request()->query())->links() }}
                    </nav>
                @endif
            </div>
        @else
            {{-- Empty state --}}
            <div class="ui-empty">
                <div class="ui-empty-icon ui-tone-teal" aria-hidden="true">
                    <svg class="ui-icon" viewBox="0 0 24 24"><use href="#i-users"/></svg>
                </div>
                @if(request('search') || request('city_id'))
                    <h3>@lang('site.no_patients_match_filters')</h3>
                    <p>@lang('site.patient_filter_help')</p>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">@lang('site.clear_filters')</a>
                @else
                    <h3>@lang('site.no_patients_yet')</h3>
                    <p>@lang('site.register_first_patient')</p>
                    <a href="{{ route('patients.create') }}" class="btn ui-btn ui-btn-primary d-inline-flex align-items-center gap-2">
                        <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-plus"/></svg>
                        Register first patient
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@endsection