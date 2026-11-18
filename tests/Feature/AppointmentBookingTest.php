<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // tests/Feature/AppointmentBookingTest.php
    use RefreshDatabase;

    public function test_cannot_double_book_same_slot(): void
    {
        $receptionist = User::factory()->create(['activation' => true]);
        Receptionist::factory()->for($receptionist)->create();
        $doctor  = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $payload = [
            'patient_id'       => $patient->id,
            'doctor_id'        => $doctor->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '10:00',
        ];

        $this->actingAs($receptionist)->post(route('appointments.store'), $payload)
             ->assertRedirect(route('appointments.index'));

        $this->actingAs($receptionist)->post(route('appointments.store'), $payload)
             ->assertSessionHasErrors('appointment_time');
    }
    public function test_patient_cannot_have_two_appointments_at_the_same_time_with_different_doctors(): void
    {
        $receptionistUser = User::factory()->create();
        Receptionist::factory()->for($receptionistUser)->create();
        $firstDoctor = Doctor::factory()->create();
        $secondDoctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $date = now()->addDay()->toDateString();

        Appointment::book([
            'patient_id' => $patient->id,
            'doctor_id' => $firstDoctor->id,
            'appointment_date' => $date . ' 10:00:00',
            'appointment_time' => '10:00:00',
            'status' => Appointment::SCHEDULED,
        ]);

        $this->actingAs($receptionistUser)
            ->post(route('appointments.store'), [
                'patient_id' => $patient->id,
                'doctor_id' => $secondDoctor->id,
                'appointment_date' => $date,
                'appointment_time' => '10:00',
            ])
            ->assertSessionHasErrors('appointment_time');
    }

    public function test_cannot_book_a_time_that_has_already_passed_today(): void
    {
        $receptionistUser = User::factory()->create();
        Receptionist::factory()->for($receptionistUser)->create();
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $this->actingAs($receptionistUser)
            ->post(route('appointments.store'), [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => today()->toDateString(),
                'appointment_time' => now()->subHour()->format('H:i'),
            ])
            ->assertSessionHasErrors('appointment_time');
    }

    public function test_receptionist_can_open_rescheduling_form_for_their_own_appointment(): void
    {
        $receptionistUser = User::factory()->create();
        $receptionist = Receptionist::factory()->for($receptionistUser)->create();
        $appointment = Appointment::factory()->create([
            'receptionist_id' => $receptionist->id,
        ]);

        $this->actingAs($receptionistUser)
            ->get(route('appointments.edit', $appointment))
            ->assertOk();
    }

}
