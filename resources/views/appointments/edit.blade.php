@extends('layouts.app')

@section('title', __('site.reschedule_appointment'))

@section('content')
<div class="ui-page container-fluid py-4">
    <div class="ui-page-head">
        <div>
            <h1 class="ui-title">@lang('site.reschedule_appointment')</h1>
            <p class="ui-sub">@lang('site.reschedule_description')</p>
        </div>
        <a href="{{ route('appointments.show', $appointment) }}" class="ui-btn ui-btn-ghost">
            @lang('site.cancel')
        </a>
    </div>

    <div class="ui-card p-3 p-md-4">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('appointments.update', $appointment) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="doctor_id">@lang('site.doctor')</label>
                    <select class="form-select ui-input @error('doctor_id') is-invalid @enderror"
                            id="doctor_id" name="doctor_id" required>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}"
                                @selected((string) old('doctor_id', $appointment->doctor_id) === (string) $doctor->id)>
                                {{ $doctor->user?->name ?? __('site.not_available') }}
                                — {{ $doctor->specialization }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="appointment_date">@lang('site.appointment_date')</label>
                    <input type="date"
                           class="form-control ui-input @error('appointment_date') is-invalid @enderror"
                           id="appointment_date"
                           name="appointment_date"
                           min="{{ today()->toDateString() }}"
                           value="{{ old('appointment_date', $appointment->appointment_date->toDateString()) }}"
                           required>
                    @error('appointment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label" for="appointment_time">@lang('site.appointment_time')</label>
                    <input type="time"
                           class="form-control ui-input @error('appointment_time') is-invalid @enderror"
                           id="appointment_time"
                           name="appointment_time"
                           value="{{ old('appointment_time', substr((string) $appointment->appointment_time, 0, 5)) }}"
                           required>
                    @error('appointment_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="notes">@lang('site.notes')</label>
                    <textarea class="form-control ui-input @error('notes') is-invalid @enderror"
                              id="notes" name="notes" rows="4">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                <a href="{{ route('appointments.show', $appointment) }}" class="ui-btn ui-btn-ghost">
                    @lang('site.cancel')
                </a>
                <button type="submit" class="ui-btn ui-btn-primary">
                    @lang('site.save_changes')
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
