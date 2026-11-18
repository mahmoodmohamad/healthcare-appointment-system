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

    public function test_receptionist_cannot_change_patient_login_credentials(): void
    {
        $receptionistUser = User::factory()->create();
        Receptionist::factory()->for($receptionistUser)->create();
        $patient = Patient::factory()->create();
        $originalEmail = $patient->user->email;
        $originalPasswordHash = $patient->user->password;

        $this->actingAs($receptionistUser)
            ->put(route('patients.update', $patient), [
                'name' => 'Updated Patient Name',
                'email' => 'changed@example.com',
                'password' => 'ChangedPassword123!',
                'password_confirmation' => 'ChangedPassword123!',
                'national_id' => $patient->national_id,
                'phone' => $patient->phone,
                'city_id' => $patient->city_id,
                'gender' => $patient->gender,
                'birth_date' => $patient->birth_date?->format('Y-m-d'),
            ])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertSame($originalEmail, $patient->user->fresh()->email);
        $this->assertSame($originalPasswordHash, $patient->user->fresh()->password);
    }


    public function test_receptionist_can_reschedule_their_own_appointment(): void
    {
        $receptionistUser = User::factory()->create();
        $receptionist = Receptionist::factory()->for($receptionistUser)->create();
        $appointment = Appointment::factory()->create([
            'receptionist_id' => $receptionist->id,
            'appointment_date' => now()->addDays(2)->startOfDay(),
            'appointment_time' => '10:00:00',
            'status' => Appointment::SCHEDULED,
        ]);
        $newDoctor = Doctor::factory()->create();
        $newDate = now()->addDays(4)->toDateString();

        $this->actingAs($receptionistUser)
            ->put(route('appointments.update', $appointment), [
                'doctor_id' => $newDoctor->id,
                'appointment_date' => $newDate,
                'appointment_time' => '11:30',
                'notes' => 'Rescheduled by receptionist',
            ])
            ->assertRedirect(route('appointments.show', $appointment));

        $appointment->refresh();

        $this->assertSame($newDoctor->id, $appointment->doctor_id);
        $this->assertSame($newDate, $appointment->appointment_date->toDateString());
        $this->assertSame('11:30:00', substr((string) $appointment->appointment_time, 0, 8));
        $this->assertSame('Rescheduled by receptionist', $appointment->notes);
    }

    public function test_receptionist_cannot_reschedule_another_receptionists_appointment(): void
    {
        $ownerUser = User::factory()->create();
        $owner = Receptionist::factory()->for($ownerUser)->create();
        $otherUser = User::factory()->create();
        Receptionist::factory()->for($otherUser)->create();
        $appointment = Appointment::factory()->create([
            'receptionist_id' => $owner->id,
            'status' => Appointment::SCHEDULED,
        ]);
        $originalDoctorId = $appointment->doctor_id;
        $newDoctor = Doctor::factory()->create();

        $this->actingAs($otherUser)
            ->put(route('appointments.update', $appointment), [
                'doctor_id' => $newDoctor->id,
                'appointment_date' => now()->addDays(5)->toDateString(),
                'appointment_time' => '12:00',
            ])
            ->assertForbidden();

        $this->assertSame($originalDoctorId, $appointment->fresh()->doctor_id);
    }


    public function test_appointment_seeder_is_idempotent_when_run_twice(): void
    {
        Doctor::factory()->create();
        Patient::factory()->count(2)->create();

        $this->seed(AppointmentSeeder::class);
        $countAfterFirstRun = Appointment::query()->count();

        $this->seed(AppointmentSeeder::class);

        $this->assertSame($countAfterFirstRun, Appointment::query()->count());
    }

}
