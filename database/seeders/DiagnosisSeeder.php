<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Diagnosis;

class DiagnosisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$appointments = Appointment::take(3)->get();

        foreach ($appointments as $appointment) {
            $appointment->update(['status' => 'completed']);

            Diagnosis::create([
                'appointment_id' => $appointment->id,
                'description' => 'General examination completed',
                'prescription' => 'Vitamin C + Rest',
            ]);
        }
    }
}
