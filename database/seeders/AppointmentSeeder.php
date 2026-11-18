<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Receptionist;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run()
    {
        $doctor       = Doctor::first();
        $receptionist = Receptionist::first();
        $patients     = Patient::all();

        $date      = now()->addDay()->startOfDay();
        $startTime = Carbon::createFromTime(10, 0);

        foreach ($patients as $index => $patient) {
            $time = $startTime->copy()->addMinutes($index * 30)->format('H:i:s');

            Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctor->id,
                'receptionist_id'  => $receptionist->id,
                'appointment_date' => $date->copy()->setTimeFromTimeString($time),
                'appointment_time' => $time,
                'status'           => Appointment::SCHEDULED,
                'notes'            => 'Initial visit',
            ]);
        }
    }
}
