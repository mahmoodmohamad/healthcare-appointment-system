<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Receptionist;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        if (! Doctor::query()->exists()) {
            $this->call(DoctorSeeder::class);
        }
        if (! Patient::query()->exists()) {
            $this->call(PatientSeeder::class);
        }

        $doctor = Doctor::query()->orderBy('id')->firstOrFail();
        $receptionistId = Receptionist::query()->value('id');
        $date = now()->addDay()->toDateString();

        foreach (Patient::query()->orderBy('id')->get() as $index => $patient) {
            $time = now()->startOfDay()->addHours(9)->addMinutes($index * 30)->format('H:i:s');

            $alreadySeeded = Appointment::query()
                ->where('patient_id', $patient->id)
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $date)
                ->where('appointment_time', $time)
                ->exists();

            if ($alreadySeeded || ! Appointment::isAvailable($doctor->id, $date, $time)) {
                continue;
            }

            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'receptionist_id' => $receptionistId,
                'appointment_date' => $date . ' ' . $time,
                'appointment_time' => $time,
                'status' => Appointment::SCHEDULED,
                'notes' => 'Initial visit',
            ]);
        }
    }
}
