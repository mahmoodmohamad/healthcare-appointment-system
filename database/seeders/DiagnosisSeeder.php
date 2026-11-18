<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Diagnosis;
use Illuminate\Database\Seeder;

class DiagnosisSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = Appointment::query()
            ->where('status', Appointment::COMPLETED)
            ->get();

        foreach ($appointments as $appointment) {
            Diagnosis::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'symptoms' => 'Headache and mild fever',
                    'diagnosis' => 'Viral infection',
                    'description' => 'Rest and fluids recommended.',
                    'prescription' => 'Paracetamol 500mg',
                    'notes' => null,
                ]
            );
        }
    }
}
