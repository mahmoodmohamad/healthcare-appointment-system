<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use Database\Seeders\AppointmentSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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


    public function test_appointment_seeder_keeps_completed_and_cancelled_visits_unchanged_on_reruns(): void
    {
        Doctor::factory()->create();
        Patient::factory()->count(2)->create();

        $this->seed(AppointmentSeeder::class);

        $completedSeed = Appointment::query()
            ->where('notes', '[SEED] Completed visit')
            ->firstOrFail();
        $cancelledSeed = Appointment::query()
            ->where('notes', '[SEED] Cancelled visit')
            ->firstOrFail();
        $initialSeeds = Appointment::query()
            ->where('notes', '[SEED] Initial visit')
            ->orderBy('id')
            ->get();

        $initialCompleted = $initialSeeds[0];
        $initialCancelled = $initialSeeds[1];

        $completedSeed->update([
            'appointment_date' => '1999-01-01 12:15:00',
            'appointment_time' => '12:15:00',
        ]);
        $cancelledSeed->update([
            'appointment_date' => '1999-01-02 13:30:00',
            'appointment_time' => '13:30:00',
        ]);
        $initialCompleted->update([
            'status' => Appointment::COMPLETED,
            'appointment_date' => '1999-01-03 14:45:00',
            'appointment_time' => '14:45:00',
        ]);
        $initialCancelled->update([
            'status' => Appointment::CANCELLED,
            'appointment_date' => '1999-01-04 15:00:00',
            'appointment_time' => '15:00:00',
        ]);

        $protectedIds = [
            $completedSeed->id,
            $cancelledSeed->id,
            $initialCompleted->id,
            $initialCancelled->id,
        ];
        $before = Appointment::query()
            ->whereIn('id', $protectedIds)
            ->orderBy('id')
            ->get(['id', 'status', 'appointment_date', 'appointment_time', 'notes'])
            ->toArray();
        $countBefore = Appointment::query()->count();

        $this->seed(AppointmentSeeder::class);

        $after = Appointment::query()
            ->whereIn('id', $protectedIds)
            ->orderBy('id')
            ->get(['id', 'status', 'appointment_date', 'appointment_time', 'notes'])
            ->toArray();

        $this->assertSame($before, $after);
        $this->assertSame($countBefore, Appointment::query()->count());
    }

    public function test_sqlite_migrations_create_both_unique_generated_slot_columns(): void
    {
        $this->assertSame('sqlite', DB::getDriverName());
        $this->assertTrue(Schema::hasColumn('appointments', 'slot_lock'));
        $this->assertTrue(Schema::hasColumn('appointments', 'patient_slot_lock'));
    }

    public function test_database_unique_indexes_prevent_doctor_and_patient_double_booking(): void
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $otherPatient = Patient::factory()->create();
        $date = now()->addDays(8)->toDateString();

        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'appointment_date' => $date . ' 10:00:00',
            'appointment_time' => '10:00:00',
        ]);

        try {
            Appointment::factory()->create([
                'doctor_id' => $doctor->id,
                'patient_id' => $otherPatient->id,
                'appointment_date' => $date . ' 10:00:00',
                'appointment_time' => '10:00:00',
            ]);
            $this->fail('The database should reject a second appointment for the same doctor and time.');
        } catch (QueryException $exception) {
            $this->assertTrue(Appointment::isDuplicateKey($exception));
        }

        try {
            Appointment::factory()->create([
                'doctor_id' => $otherDoctor->id,
                'patient_id' => $patient->id,
                'appointment_date' => $date . ' 10:00:00',
                'appointment_time' => '10:00:00',
            ]);
            $this->fail('The database should reject a second appointment for the same patient and time.');
        } catch (QueryException $exception) {
            $this->assertTrue(Appointment::isDuplicateKey($exception));
        }
    }

    public function test_cancelled_appointment_releases_doctor_and_patient_slots(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $date = now()->addDays(9)->toDateString();

        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'appointment_date' => $date . ' 10:00:00',
            'appointment_time' => '10:00:00',
            'status' => Appointment::CANCELLED,
        ]);

        $replacement = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'appointment_date' => $date . ' 10:00:00',
            'appointment_time' => '10:00:00',
            'status' => Appointment::SCHEDULED,
        ]);

        $this->assertSame(Appointment::SCHEDULED, $replacement->status);
    }

    public function test_doctors_and_patients_cannot_reschedule_through_the_policy(): void
    {
        $appointment = Appointment::factory()->create();
        $doctorUser = $appointment->doctor->user;
        $patientUser = $appointment->patient->user;

        $this->assertFalse(Gate::forUser($doctorUser)->allows('reschedule', $appointment));
        $this->assertFalse(Gate::forUser($patientUser)->allows('reschedule', $appointment));
    }

    public function test_admin_retains_read_only_appointment_access(): void
    {
        $adminUser = User::factory()->create();
        Admin::query()->create(['user_id' => $adminUser->id]);
        $appointment = Appointment::factory()->create();

        $this->assertTrue(Gate::forUser($adminUser)->allows('view', $appointment));
        $this->assertTrue(Gate::forUser($adminUser)->allows('viewAny', Appointment::class));
        $this->assertFalse(Gate::forUser($adminUser)->allows('manage', $appointment));
        $this->assertFalse(Gate::forUser($adminUser)->allows('reschedule', $appointment));
        $this->assertFalse(Gate::forUser($adminUser)->allows('cancel', $appointment));
    }

}
