<?php

namespace App\Http\Controllers\Patient;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('patient.dashboard');
    }
}
