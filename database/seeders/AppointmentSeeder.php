<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Physician;
use App\Models\Patient;
use App\Models\Secretary;
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
		$physician = Physician::first();
        $secretary = Secretary::first();
        $patients = Patient::all();

        $startTime = Carbon::createFromTime(10, 0);

        foreach ($patients as $index => $patient) {

            Appointment::create([
                'patient_id' => $patient->id,
                'physician_id' => $physician->id,
                'secretary_id' => $secretary->id,
                'appointment_date' => now()->addDays(1),
                'appointment_time' => $startTime->copy()->addMinutes($index * 30),
                'status' => 'scheduled',
                'notes' => 'Initial visit',
            ]);
        }
    }
}
