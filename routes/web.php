<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Appointment\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Physician\DashboardController as PhysicianDashboardController;
use App\Http\Controllers\Physician\PhysicianController;
use App\Http\Controllers\Secretary\DashboardController as SecretaryDashboardController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->isPhysician()) {
        return redirect()->route('physician.dashboard');
    }

    if ($user->isSecretary()) {
        return redirect()->route('secretary.dashboard');
    }

    if ($user->isPatient()) {
        return redirect()->route('patient.dashboard');
    }

    abort(403, 'Your account has no assigned role.');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.custom');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
        Route::resource('users', UserManagementController::class);
        Route::post('/users/{user}/toggle-activation', [UserManagementController::class, 'toggleActivation'])
            ->name('users.toggle-activation');
    });

Route::middleware(['auth', 'role:secretary'])
    ->group(function () {
        Route::get('/secretary/dashboard', SecretaryDashboardController::class)->name('secretary.dashboard');

        Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])
            ->name('appointments.calendar');
        Route::get('/appointments/by-date', [AppointmentController::class, 'getAppointments'])
            ->name('appointments.by-date');
        Route::get('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])
            ->name('appointments.available-slots');
        Route::resource('appointments', AppointmentController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::resource('patients', PatientController::class)->except(['destroy']);
        Route::get('/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory'])
            ->name('patients.medical-history');

        // Compatibility routes for the legacy secretary patient templates.
        Route::get('/secretary/patients', [PatientController::class, 'index'])
            ->name('secretary.patient.list');
        Route::get('/secretary/patients/{patient}', [PatientController::class, 'show'])
            ->name('secretary.patient.details');
        Route::get('/secretary/patients/{patient}/edit', [PatientController::class, 'edit'])
            ->name('secretary.patient.edit');
        Route::post('/secretary/patients', [PatientController::class, 'store'])
            ->name('secretary.patient.save');
        Route::get('/secretary/patients/{patient}/clinic/edit', [PatientController::class, 'edit'])
            ->name('secretary.patient.clinic.edit');
        Route::post('/secretary/patients/{patient}/clinic', [PatientController::class, 'update'])
            ->name('secretary.patient.clinic.save');
        Route::get('/secretary/ajax/country', function () {
            return response()->json(\App\Models\Country::query()->orderBy('name')->get());
        })->name('secretary.ajax.country');
    });

Route::middleware(['auth', 'role:physician'])
    ->prefix('physician')
    ->name('physician.')
    ->group(function () {
        Route::get('/dashboard', [PhysicianDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [PhysicianController::class, 'appointments'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [PhysicianController::class, 'showAppointment'])
            ->name('appointments.show');
        Route::get('/appointments/{appointment}/diagnosis/create', [PhysicianController::class, 'createDiagnosis'])
            ->name('diagnosis.create');
        Route::post('/appointments/{appointment}/diagnosis', [PhysicianController::class, 'storeDiagnosis'])
            ->name('diagnosis.store');
        Route::get('/appointments/{appointment}/diagnosis/edit', [PhysicianController::class, 'editDiagnosis'])
            ->name('diagnosis.edit');
        Route::put('/appointments/{appointment}/diagnosis', [PhysicianController::class, 'updateDiagnosis'])
            ->name('diagnosis.update');
        Route::get('/patients/{patient}/history', [PhysicianController::class, 'patientHistory'])
            ->name('patients.history');
    });

Route::middleware(['auth', 'role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('/dashboard', PatientDashboardController::class)->name('dashboard');
    });

Route::middleware('auth')->get('/dashboard', function () {
    return redirect('/');
})->name('dashboard');

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return 'Cleared!';
});

Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ar'], true), 400);

    App::setLocale($locale);
    session(['locale' => $locale]);

    return back();
})->name('language.switch');
