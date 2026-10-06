<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Receptionist;
use Carbon\Carbon;
use App\Models\Appointment;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$doctor = Doctor::first();
        $receptionist = Receptionist::first();
        $patients = Patient::all();

        $startTime = Carbon::createFromTime(10, 0);

        foreach ($patients as $index => $patient) {

            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'receptionist_id' => $receptionist->id,
                'appointment_date' => now()->addDays(1),
                'appointment_time' => $startTime->copy()->addMinutes($index * 30),
                'status' => 'scheduled',
                'notes' => 'Initial visit',
            ]);
        }
    }
}
