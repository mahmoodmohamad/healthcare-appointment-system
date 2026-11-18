
@extends('layouts.app')

@section('title', __('site.register_new_patient'))

@section('content')


<div class="patient-create container-fluid py-4">

    {{-- Page Header --}}
    <div class="page-heading">
        <div>
            <div class="breadcrumb-line">
                <a href="{{ route('patients.index') }}">@lang('site.patients')</a>
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                <span>@lang('site.register_new_patient')</span>
            </div>

            <h1 class="page-title">@lang('site.register_new_patient')</h1>

            <p class="page-description">
                Create a patient profile and set up their account.
            </p>
        </div>

        <a href="{{ route('patients.index') }}" class="back-link">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>@lang('site.back_to_patients')</span>
        </a>
    </div>

    {{-- Intro --}}
    <div class="intro-banner">
        <div class="intro-icon">
            <i class="bi bi-person-vcard" aria-hidden="true"></i>
        </div>

        <div>
            <strong>@lang('site.new_patient_registration')</strong>
            <p>
                Please enter accurate patient information. Fields marked with
                <span class="text-danger">*</span> are required.
            </p>
        </div>
    </div>

    {{-- Form Layout --}}
    <div class="form-layout">

        <main>
            @if($errors->any())
                <div class="error-summary" role="alert">
                    <div class="error-summary-title">
                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                        Please review the information below
                    </div>

                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('patients.store') }}"
                class="form-card"
                autocomplete="on"
            >
                @csrf

                {{-- @lang('site.personal_information') --}}
                <section class="form-section">
                    <div class="section-heading">
                        <div class="section-icon">
                            <i class="bi bi-person" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h2>@lang('site.personal_information')</h2>
                            <p>@lang('site.patient_identity_description')</p>
                        </div>
                    </div>

                    <div class="field-grid">

                        <div class="field-group full-width">
                            <label for="name" class="form-label">
                                Full Name <span class="required-mark">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-person input-icon" aria-hidden="true"></i>

                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control ui-input @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}"
                                    placeholder="@lang('site.patient_name_placeholder')"
                                    autocomplete="name"
                                    maxlength="255"
                                    required
                                    autofocus
                                >
                            </div>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group full-width">
                            <label for="national_id" class="form-label">
                                @lang('site.national_id') <span class="required-mark">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-card-heading input-icon" aria-hidden="true"></i>

                                <input
                                    type="text"
                                    name="national_id"
                                    id="national_id"
                                    class="form-control @error('national_id') is-invalid @enderror"
                                    value="{{ old('national_id') }}"
                                    placeholder="@lang('site.national_id_placeholder')"
                                    autocomplete="off"
                                    required
                                >
                            </div>

                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- @lang('site.contact_information') --}}
                <section class="form-section">
                    <div class="section-heading">
                        <div class="section-icon">
                            <i class="bi bi-telephone" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h2>@lang('site.contact_information')</h2>
                            <p>@lang('site.patient_contact_help')</p>
                        </div>
                    </div>

                    <div class="field-grid">

                        <div class="field-group">
                            <label for="phone" class="form-label">
                                Phone Number <span class="required-mark">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-phone input-icon" aria-hidden="true"></i>

                                <input
                                    type="tel"
                                    name="phone"
                                    id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}"
                                    placeholder="@lang('site.phone_placeholder')"
                                    autocomplete="tel"
                                    required
                                >
                            </div>

                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="city_id" class="form-label">
                                City <span class="required-mark">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-geo-alt input-icon" aria-hidden="true"></i>

                                <select
                                    name="city_id"
                                    id="city_id"
                                    class="form-select ui-input @error('city_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">@lang('site.select_city')</option>

                                    @foreach($cities as $city)
                                        <option
                                            value="{{ $city->id }}"
                                            @selected((string) old('city_id') === (string) $city->id)
                                        >
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group full-width">
                            <label for="email" class="form-label">
                                Email Address <span class="required-mark">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-envelope input-icon" aria-hidden="true"></i>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="patient@example.com"
                                    autocomplete="email"
                                    required
                                >
                            </div>

                            <span class="field-hint">
                                This email address will be used by the patient to sign in.
                            </span>

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </section>

                {{-- @lang('site.account_security') --}}
                <section class="form-section">
                    <div class="section-heading">
                        <div class="section-icon">
                            <i class="bi bi-shield-lock" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h2>@lang('site.account_security')</h2>
                            <p>@lang('site.patient_login_help')</p>
                        </div>
                    </div>

                    <div class="field-grid">

                        <div class="field-group">
                            <label for="password" class="form-label">
                                Password <span class="required-mark">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-lock input-icon" aria-hidden="true"></i>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control password-input @error('password') is-invalid @enderror"
                                    placeholder="@lang('site.enter_password')"
                                    autocomplete="new-password"
                                    minlength="10"
                                    required
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="password"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>

                            <span class="field-hint">
                                Use at least 10 characters.
                            </span>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="password_confirmation" class="form-label">
                                Confirm Password <span class="required-mark">*</span>
                            </label>

                            <div class="input-wrap">
                                <i class="bi bi-lock-fill input-icon" aria-hidden="true"></i>

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control password-input"
                                    placeholder="@lang('site.confirm_password_placeholder')"
                                    autocomplete="new-password"
                                    minlength="10"
                                    required
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="password_confirmation"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </section>

                {{-- Form Actions --}}
                <div class="form-footer">
                    <div class="footer-note">
                        <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                        Patient information should be kept confidential.
                    </div>

                    <div class="footer-actions">
                        <a href="{{ route('patients.index') }}" class="ui-btn ui-btn-ghost btn-cancel">
                            @lang('site.cancel')
                        </a>

                        <button type="submit" class="ui-btn ui-btn-primary btn-submit">
                            <i class="bi bi-person-check" aria-hidden="true"></i>
                            Register Patient
                        </button>
                    </div>
                </div>

            </form>
        </main>

        {{-- Helpful Sidebar --}}
        <aside class="sidebar-column">

            <section class="ui-card side-card">
                <h3>@lang('site.registration_checklist')</h3>

                <div class="info-item">
                    <div class="info-item-icon">
                        <i class="bi bi-person-check" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>@lang('site.patient_identity')</strong>
                        <span>@lang('site.patient_identity_help')</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-item-icon">
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>@lang('site.contact_details')</strong>
                        <span>@lang('site.patient_contact_accuracy')</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-item-icon">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>@lang('site.location')</strong>
                        <span>@lang('site.patient_city_help')</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-item-icon">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>@lang('site.login_credentials')</strong>
                        <span>@lang('site.password_confirmation_help')</span>
                    </div>
                </div>
            </section>

            <section class="ui-card side-card privacy-note">
                <div class="info-item">
                    <div class="info-item-icon">
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h3>@lang('site.privacy_matters')</h3>
                        <p>
                            Patient information is confidential.
                            Only collect and access information needed
                            for the registration process.
                        </p>
                    </div>
                </div>
            </section>

        </aside>

    </div>
</div>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const inputId = this.getAttribute('data-password-toggle');
            const input = document.getElementById(inputId);
            const icon = this.querySelector('i');

            if (!input || !icon) {
                return;
            }

            const shouldShow = input.type === 'password';

            input.type = shouldShow ? 'text' : 'password';

            icon.classList.toggle('bi-eye', !shouldShow);
            icon.classList.toggle('bi-eye-slash', shouldShow);

            this.setAttribute('aria-pressed', String(shouldShow));
            this.setAttribute(
                'aria-label',
                shouldShow ? 'Hide password' : 'Show password'
            );
        });
    });
</script>

@endsection
