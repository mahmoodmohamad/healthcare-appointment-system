<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
