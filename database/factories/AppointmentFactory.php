<?php
namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id'       => Patient::factory(),
            'doctor_id'        => Doctor::factory(),
            'receptionist_id'  => null,
            'appointment_date' => now()->addDay()->setTime(10, 0),
            'appointment_time' => '10:00:00',
            'status'           => 'scheduled',
            'notes'            => null,
        ];
    }
}