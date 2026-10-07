<?php
namespace Tests\Feature;

use App\Models\{Appointment, Doctor};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_cannot_view_another_doctors_appointment(): void
    {
        $appointment = Appointment::factory()->create();
        $other = Doctor::factory()->create()->user;

        $this->actingAs($other)
             ->get(route('doctor.appointments.show', $appointment))
             ->assertForbidden();
    }

    public function test_saving_diagnosis_completes_the_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        $this->actingAs($appointment->doctor->user)
             ->post(route('doctor.diagnosis.store', $appointment), [
                 'symptoms'  => 'Headache',
                 'diagnosis' => 'Migraine',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('diagnoses', ['appointment_id' => $appointment->id]);
        $this->assertSame('completed', $appointment->fresh()->status);
    }

}