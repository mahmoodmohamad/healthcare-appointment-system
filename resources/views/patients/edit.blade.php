@extends('layouts.app')
@section('title', 'Edit Patient')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Patient Information</h4>
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

                    <form method="POST" action="{{ route('patients.update', $patient) }}">
                        @csrf
                        @method('PUT')

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $patient->user->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- National ID -->
                        <div class="mb-3">
                            <label for="national_id" class="form-label">National ID *</label>
                            <input type="text" 
                                   name="national_id" 
                                   id="national_id" 
                                   class="form-control @error('national_id') is-invalid @enderror"
                                   value="{{ old('national_id', $patient->national_id) }}"
                                   required>
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone" 
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $patient->phone) }}"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $patient->user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="mb-3">
                            <label for="city_id" class="form-label">City *</label>
                            <select name="city_id" 
                                    id="city_id" 
                                    class="form-select @error('city_id') is-invalid @enderror"
                                    required>
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" 
                                        {{ old('city_id', $patient->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                                                        @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select name="gender" 
                                    id="gender" 
                                    class="form-select @error('gender') is-invalid @enderror"
                                    required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Birth Date -->
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Birth Date *</label>
                            <input type="date" 
                                   name="birth_date" 
                                   id="birth_date" 
                                   class="form-control @error('birth_date') is-invalid @enderror"
                                   value="{{ old('birth_date', optional(\Carbon\Carbon::parse($patient->birth_date))->format('Y-m-d')) }}"
                                   required>
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Secretary -->
                        <div class="mb-3">
                            <label for="secretary_id" class="form-label">Secretary *</label>
                            <select name="secretary_id" 
                                    id="secretary_id" 
                                    class="form-select @error('secretary_id') is-invalid @enderror"
                                    required>
                                <option value="">Select Secretary</option>
                                @foreach($secretaries as $secretary)
                                    <option value="{{ $secretary->id }}" 
                                        {{ old('secretary_id', $patient->secretary_id) == $secretary->id ? 'selected' : '' }}>
                                        {{ $secretary->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('secretary_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Patient</button>
                            <a href="{{ route('patients.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
