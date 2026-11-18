@extends('layouts.app')
@section('title', 'Register {{ __('site.patient') }}')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">🏥 Register New {{ __('site.patient') }}</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.patients.store') }}">
                        @csrf

                        <h6 class="text-muted text-uppercase mb-3">{{ __('site.account') }}</h6>

                        <div class="mb-3">
                            <label class="form-label">{{ __('site.full_name_required') }}</label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('site.email_required') }}</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            <small class="text-muted">{{ __('site.login_credential_help') }}</small>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('site.password_required') }}</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                <small class="text-muted">{{ __('site.minimum_10_characters') }}</small>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm {{ __('site.password_required') }}</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted text-uppercase mb-3">{{ __('site.patient') }} Details</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">National {{ __('site.id') }} *</label>
                                <input type="text" name="national_id"
                                       class="form-control @error('national_id') is-invalid @enderror"
                                       value="{{ old('national_id') }}" required>
                                @error('national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('site.phone_required') }}</label>
                                <input type="text" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('site.gender') }}</label>
                                <select name="gender" class="form-select">
                                    <option value="">—</option>
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>{{ __('site.male') }}</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('site.female') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Birth {{ __('site.date') }}</label>
                                <input type="date" name="birth_date"
                                       class="form-control @error('birth_date') is-invalid @enderror"
                                       value="{{ old('birth_date') }}">
                                @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('site.city_required') }}</label>
                                <select name="city_id"
                                        class="form-select @error('city_id') is-invalid @enderror" required>
                                    <option value="">{{ __('site.select_city') }}</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Registered by {{ __('site.receptionist') }}</label>
                            <select name="receptionist_id" class="form-select">
                                <option value="">{{ __('site.none_self_registered') }}</option>
                                @foreach($receptionists as $r)
                                    <option value="{{ $r->id }}"
                                        {{ old('receptionist_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Register {{ __('site.patient') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection