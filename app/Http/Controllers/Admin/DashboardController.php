<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $patients=Patient::count();
        $clinics=Clinic::count();
        $doctors = Doctor::count();
        return view('admin.dashboard', compact('patients','clinics','doctors'));
    }
}
