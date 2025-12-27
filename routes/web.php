<?php

// use App\Http\Controllers\DashBoardController;

use App\Http\Controllers\DashboardController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Appointment\AppointmentController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Secretary\DashboardController as SecretaryDashboard;
use App\Http\Controllers\Admin\{AdminController, UserManagementController};
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\physician\DashboardController as PhysicianDashboardController;
use App\Http\Controllers\Physician\PhysicianController;


Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isPhysician()) return redirect()->route('physician.dashboard');
        if ($user->isSecretary()) return redirect()->route('secretary.dashboard');
    }
    return redirect()->route('login');
});

Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login'])->name('login.custom');
Route::post('logout', [LoginController::class,'logout'])->name('logout');



Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('dashboard');
    
    // Statistics
    Route::get('/statistics', [AdminController::class, 'statistics'])
        ->name('statistics');
    
    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/toggle-activation', [UserManagementController::class, 'toggleActivation'])
        ->name('users.toggle-activation');
});
Route::get('/diagnoses', [AdminController::class, 'diagnoses'])->name('admin.diagnoses');

Route::middleware(['auth', 'role:secretary'])->group(function () {
    Route::get('/', SecretaryDashboard::class)->name('secretary.dashboard');
    
    // ✅ Calendar MUST come BEFORE generic routes
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])
        ->name('appointments.calendar');
    Route::get('/appointments/by-date', [AppointmentController::class, 'getAppointments'])
        ->name('appointments.by-date');
    
    // Then other appointment routes
    Route::get('/appointments', [AppointmentController::class, 'index'])
        ->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])
        ->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])
        ->name('appointments.show');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->name('appointments.destroy');
	
	Route::resource('patients', PatientController::class)->except(['destroy']);
    Route::get('/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory'])
        ->name('patients.medical-history');
});

Route::middleware(['auth', 'role:physician'])->prefix('physician')->name('physician.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PhysicianDashboardController::class, 'dashboard'])
        ->name('dashboard');
    
    // Appointments
    Route::get('/appointments', [PhysicianController::class, 'appointments'])
        ->name('appointments.index');
    Route::get('/appointments/{appointment}', [PhysicianController::class, 'showAppointment'])
        ->name('appointments.show');
    
    // Diagnosis
    Route::get('/appointments/{appointment}/diagnosis/create', [PhysicianController::class, 'createDiagnosis'])
        ->name('diagnosis.create');
    Route::post('/appointments/{appointment}/diagnosis', [PhysicianController::class, 'storeDiagnosis'])
        ->name('diagnosis.store');
    Route::get('/appointments/{appointment}/diagnosis/edit', [PhysicianController::class, 'editDiagnosis'])
        ->name('diagnosis.edit');
    Route::put('/appointments/{appointment}/diagnosis', [PhysicianController::class, 'updateDiagnosis'])
        ->name('diagnosis.update');
    
    // Patient History
    Route::get('/patients/{patient}/history', [PhysicianController::class, 'patientHistory'])
        ->name('patients.history');
});
// clear routes

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return "Cleared!";
});



use Illuminate\Support\Facades\App;

Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        abort(400);
    }

    App::setLocale($locale);
    session(['locale' => $locale]);
    return back();
});
