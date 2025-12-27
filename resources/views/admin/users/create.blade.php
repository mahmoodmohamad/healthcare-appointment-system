@extends('layouts.app')
@section('title', 'Create User')




@section('content')
<div class="create-user-container">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <div class="form-card">
                    <!-- Header -->
                    <div class="form-header">
                        <h4>👤 Create New User</h4>
                        <p>Add a new user to the healthcare system</p>
                    </div>

                    <div class="form-body">
                        <!-- Progress Steps -->
                        <div class="form-steps">
                            <div class="step active" id="step1">1</div>
                            <div class="step" id="step2">2</div>
                            <div class="step" id="step3">3</div>
                        </div>

                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>⚠️ Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
                            @csrf

                            <!-- Step 1: Role Selection -->
                            <div class="form-step" id="roleStep">
                                <h5 class="mb-4" style="color: #2d3748; font-weight: 600;">
                                    Select User Role
                                </h5>

                                <input type="hidden" name="role" id="role" value="{{ old('role') }}">
                                
                                <div class="d-flex flex-wrap justify-content-center mb-4">
                                    <div class="role-badge admin" data-role="admin" onclick="selectRole('admin')">
                                        <span style="font-size: 20px;">👨‍💼</span>
                                        <span>Admin</span>
                                    </div>
                                    <div class="role-badge physician" data-role="physician" onclick="selectRole('physician')">
                                        <span style="font-size: 20px;">👨‍⚕️</span>
                                        <span>Physician</span>
                                    </div>
                                    <div class="role-badge secretary" data-role="secretary" onclick="selectRole('secretary')">
                                        <span style="font-size: 20px;">👩‍💼</span>
                                        <span>Secretary</span>
                                    </div>
                                    <div class="role-badge patient" data-role="patient" onclick="selectRole('patient')">
                                        <span style="font-size: 20px;">🏥</span>
                                        <span>Patient</span>
                                    </div>
                                </div>

                                @error('role')
                                    <div class="text-danger text-center">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="section-divider">
                                <span>Basic Information</span>
                            </div>

                            <!-- Step 2: Basic Info -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label">
                                        <span class="required">*</span> Full Name
                                    </label>
                                    <div class="position-relative">
                                        <span class="input-icon">👤</span>
                                        <input type="text" 
                                               name="name" 
                                               id="name" 
                                               class="form-control input-with-icon @error('name') is-invalid @enderror"
                                               value="{{ old('name') }}"
                                               placeholder="Enter full name"
                                               required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">
                                        <span class="required">*</span> Email Address
                                    </label>
                                    <div class="position-relative">
                                        <span class="input-icon">📧</span>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               class="form-control input-with-icon @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="name@example.com"
                                               required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        <span class="required">*</span> Password
                                    </label>
                                    <div class="position-relative">
                                        <span class="input-icon">🔒</span>
                                        <input type="password" 
                                               name="password" 
                                               id="password" 
                                               class="form-control input-with-icon @error('password') is-invalid @enderror"
                                               placeholder="Enter password"
                                               oninput="checkPasswordStrength()"
                                               required>
                                        <span class="password-toggle" onclick="togglePassword('password')">
                                            <i class="bi bi-eye" id="password-eye"></i>
                                        </span>
                                    </div>
                                    <div class="password-strength" id="password-strength">
                                        <div class="password-strength-bar"></div>
                                    </div>
                                    <small class="form-text">Minimum 6 characters</small>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <span class="required">*</span> Confirm Password
                                    </label>
                                    <div class="position-relative">
                                        <span class="input-icon">🔒</span>
                                        <input type="password" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               class="form-control input-with-icon"
                                               placeholder="Confirm password"
                                               required>
                                        <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                                            <i class="bi bi-eye" id="password_confirmation-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Role-Specific Fields -->
                            <div id="roleSpecificFields" style="display: none;">
                                <div class="section-divider">
                                    <span id="roleSpecificTitle">Role-Specific Information</span>
                                </div>

                                <div class="row">
                                    <!-- Phone -->
                                    <div class="col-md-6 mb-3 role-field" data-roles="physician,secretary,patient">
                                        <label for="phone" class="form-label">
                                            <span class="required">*</span> Phone Number
                                        </label>
                                        <div class="position-relative">
                                            <span class="input-icon">📱</span>
                                            <input type="text" 
                                                   name="phone" 
                                                   id="phone" 
                                                   class="form-control input-with-icon @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone') }}"
                                                   placeholder="+20 123 456 7890">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-6 mb-3 role-field" data-roles="physician,secretary,patient">
                                        <label for="city_id" class="form-label">
                                            <span class="required">*</span> City
                                        </label>
                                        <div class="position-relative">
                                            <span class="input-icon">🏙️</span>
                                            <select name="city_id" 
                                                    id="city_id" 
                                                    class="form-select input-with-icon @error('city_id') is-invalid @enderror">
                                                <option value="">Select City</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('city_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Specialization -->
                                    <div class="col-12 mb-3 role-field" data-roles="physician">
                                        <label for="specialization" class="form-label">
                                            <span class="required">*</span> Medical Specialization
                                        </label>
                                        <div class="position-relative">
                                            <span class="input-icon">🩺</span>
                                            <input type="text" 
                                                   name="specialization" 
                                                   id="specialization" 
                                                   class="form-control input-with-icon @error('specialization') is-invalid @enderror"
                                                   value="{{ old('specialization') }}"
                                                   placeholder="e.g., Cardiology, Neurology, Pediatrics">
                                        </div>
                                        @error('specialization')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- National ID -->
                                    <div class="col-12 mb-3 role-field" data-roles="secretary,patient">
                                        <label for="national_id" class="form-label">
                                            <span class="required">*</span> National ID
                                        </label>
                                        <div class="position-relative">
                                            <span class="input-icon">🆔</span>
                                            <input type="text" 
                                                   name="national_id" 
                                                   id="national_id" 
                                                   class="form-control input-with-icon @error('national_id') is-invalid @enderror"
                                                   value="{{ old('national_id') }}"
                                                   placeholder="Enter national ID number">
                                        </div>
                                        @error('national_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-5 gap-3">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-submit text-white" id="submitBtn">
                                    Create User Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Role Selection
function selectRole(role) {
    // Update hidden input
    document.getElementById('role').value = role;
    
    // Update visual selection
    document.querySelectorAll('.role-badge').forEach(badge => {
        badge.classList.remove('active');
    });
    document.querySelector(`[data-role="${role}"]`).classList.add('active');
    
    // Show role-specific fields
    toggleRoleFields();
    
    // Update progress
    updateProgress(2);
}

// Toggle role-specific fields
function toggleRoleFields() {
    const role = document.getElementById('role').value;
    const roleFields = document.querySelectorAll('.role-field');
    const roleSpecificSection = document.getElementById('roleSpecificFields');
    
    if (!role) {
        roleSpecificSection.style.display = 'none';
        return;
    }
    
    roleSpecificSection.style.display = 'block';
    
    // Hide all fields first
    roleFields.forEach(field => {
        field.style.display = 'none';
        const inputs = field.querySelectorAll('input, select');
        inputs.forEach(input => input.removeAttribute('required'));
    });
    
    // Show fields for selected role
    roleFields.forEach(field => {
        const roles = field.getAttribute('data-roles').split(',');
        if (roles.includes(role)) {
            field.style.display = 'block';
            const inputs = field.querySelectorAll('input, select');
            inputs.forEach(input => input.setAttribute('required', 'required'));
        }
    });
}

// Password visibility toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('bi-eye');
        eye.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('bi-eye-slash');
        eye.classList.add('bi-eye');
    }
}

// Password strength checker
function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('password-strength');
    
    if (password.length === 0) {
        strengthBar.style.display = 'none';
        return;
    }
    
    strengthBar.style.display = 'block';
    strengthBar.classList.remove('weak', 'medium', 'strong');
    
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    if (strength <= 2) {
        strengthBar.classList.add('weak');
    } else if (strength <= 4) {
        strengthBar.classList.add('medium');
    } else {
        strengthBar.classList.add('strong');
    }
    
    updateProgress(3);
}

// Update progress steps
function updateProgress(step) {
    for (let i = 1; i <= 3; i++) {
        const stepEl = document.getElementById('step' + i);
        if (i < step) {
            stepEl.classList.add('completed');
            stepEl.classList.remove('active');
        } else if (i === step) {
            stepEl.classList.add('active');
            stepEl.classList.remove('completed');
        } else {
            stepEl.classList.remove('active', 'completed');
        }
    }
}

// Form submission with loading state
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.classList.add('btn-loading');
    submitBtn.textContent = 'Creating...';
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const selectedRole = document.getElementById('role').value;
    if (selectedRole) {
        document.querySelector(`[data-role="${selectedRole}"]`).classList.add('active');
        toggleRoleFields();
        updateProgress(2);
    }
});
</script>
@endsection



