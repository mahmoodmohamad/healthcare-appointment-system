<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Appointment\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Receptionist\DashboardController as ReceptionistDashboardController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->isDoctor()) {
        return redirect()->route('doctor.dashboard');
    }

    if ($user->isReceptionist()) {
        return redirect()->route('receptionist.dashboard');
    }

    if ($user->isPatient()) {
        return redirect()->route('patient.dashboard');
    }

    abort(403, 'Your account has no assigned role.');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.custom');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/statistics', [AdminController::class, 'statistics'])
            ->name('statistics');

        Route::resource('users', UserManagementController::class)
            ->except(['edit', 'update']);

        Route::post('/users/{user}/toggle-activation', [UserManagementController::class, 'toggleActivation'])
            ->name('users.toggle-activation');
    });

/*
|--------------------------------------------------------------------------
| Receptionist
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:receptionist'])
    ->group(function () {
        Route::get('/receptionist/dashboard', ReceptionistDashboardController::class)
            ->name('receptionist.dashboard');

        Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])
            ->name('appointments.calendar');

        Route::get('/appointments/by-date', [AppointmentController::class, 'getAppointments'])
            ->name('appointments.by-date');

        Route::get('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])
            ->name('appointments.available-slots');

        Route::resource('appointments', AppointmentController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::get('/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory'])
            ->name('patients.medical-history');

        Route::resource('patients', PatientController::class)
            ->except(['destroy']);

        // Legacy template compatibility
        Route::get('/receptionist/patients', [PatientController::class, 'index'])
            ->name('receptionist.patient.list');

        Route::get('/receptionist/patients/{patient}', [PatientController::class, 'show'])
            ->name('receptionist.patient.details');

        Route::get('/receptionist/patients/{patient}/edit', [PatientController::class, 'edit'])
            ->name('receptionist.patient.edit');

        Route::post('/receptionist/patients', [PatientController::class, 'store'])
            ->name('receptionist.patient.save');

        Route::get('/receptionist/patients/{patient}/clinic/edit', [PatientController::class, 'edit'])
            ->name('receptionist.patient.clinic.edit');

        Route::post('/receptionist/patients/{patient}/clinic', [PatientController::class, 'update'])
            ->name('receptionist.patient.clinic.save');

        Route::get('/receptionist/ajax/country', function () {
            return response()->json(
                \App\Models\Country::query()
                    ->orderBy('name')
                    ->get()
            );
        })->name('receptionist.ajax.country');
    });

/*
|--------------------------------------------------------------------------
| Doctor
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/appointments', [DoctorController::class, 'appointments'])
            ->name('appointments.index');

        Route::middleware('can:view,appointment')->group(function () {
            Route::get('/appointments/{appointment}', [DoctorController::class, 'showAppointment'])
                ->name('appointments.show');
        });

        Route::middleware('can:update,appointment')->group(function () {
            Route::get('/appointments/{appointment}/diagnosis/create', [DoctorController::class, 'createDiagnosis'])
                ->name('diagnosis.create');

            Route::post('/appointments/{appointment}/diagnosis', [DoctorController::class, 'storeDiagnosis'])
                ->name('diagnosis.store');

            Route::get('/appointments/{appointment}/diagnosis/edit', [DoctorController::class, 'editDiagnosis'])
                ->name('diagnosis.edit');

            Route::put('/appointments/{appointment}/diagnosis', [DoctorController::class, 'updateDiagnosis'])
                ->name('diagnosis.update');
        });

        Route::get('/patients/{patient}/history', [DoctorController::class, 'patientHistory'])
            ->name('patients.history');
    });

/*
|--------------------------------------------------------------------------
| Patient
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('/dashboard', PatientDashboardController::class)
            ->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Generic Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->get('/dashboard', fn () => redirect('/'))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Locale
|--------------------------------------------------------------------------
*/

Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(
        in_array($locale, ['en', 'ar'], true),
        400
    );

    App::setLocale($locale);
    session(['locale' => $locale]);

    return back();
})->name('language.switch');

