@extends('layouts.app')
@section('title', 'Register Patient')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">🏥 Register New Patient</h4>
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

                        <h6 class="text-muted text-uppercase mb-3">Account</h6>

                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            <small class="text-muted">Used as the login credential.</small>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                <small class="text-muted">Minimum 10 characters</small>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted text-uppercase mb-3">Patient Details</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">National ID *</label>
                                <input type="text" name="national_id"
                                       class="form-control @error('national_id') is-invalid @enderror"
                                       value="{{ old('national_id') }}" required>
                                @error('national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="text" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">—</option>
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Birth Date</label>
                                <input type="date" name="birth_date"
                                       class="form-control @error('birth_date') is-invalid @enderror"
                                       value="{{ old('birth_date') }}">
                                @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City *</label>
                                <select name="city_id"
                                        class="form-select @error('city_id') is-invalid @enderror" required>
                                    <option value="">Select city</option>
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
                            <label class="form-label">Registered by Receptionist</label>
                            <select name="receptionist_id" class="form-select">
                                <option value="">— None / self-registered —</option>
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
                                Register Patient
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection