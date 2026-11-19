<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_endpoints_require_sanctum_authentication(): void
    {
        $this->getJson('/api/appointments')
            ->assertUnauthorized();

        $this->postJson('/api/appointments')
            ->assertUnauthorized();
    }

    public function test_appointment_api_routes_are_registered(): void
    {
        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route) => $route->uri() === 'api/appointments');

        $this->assertTrue($routes->contains(
            fn ($route) => in_array('GET', $route->methods(), true)
        ));

        $this->assertTrue($routes->contains(
            fn ($route) => in_array('POST', $route->methods(), true)
        ));
    }

    public function test_doctor_cannot_reschedule_an_assigned_appointment_through_the_api(): void
    {
        $appointment = Appointment::factory()->create();
        $doctorUser = $appointment->doctor->user;
        $newDoctor = Doctor::factory()->create();

        Sanctum::actingAs($doctorUser);

        $this->putJson(route('api.appointments.update', $appointment), [
            'doctor_id' => $newDoctor->id,
            'appointment_date' => now()->addDays(5)->toDateString(),
            'appointment_time' => '11:30',
        ])
            ->assertForbidden()
            ->assertJsonPath('message', 'Doctors are not authorized to reschedule appointments.');

        $this->assertNotSame($newDoctor->id, $appointment->fresh()->doctor_id);
    }

    public function test_patient_cannot_update_or_reschedule_an_appointment_through_the_api(): void
    {
        $patient = Patient::factory()->create();
        $appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
        ]);
        $newDoctor = Doctor::factory()->create();

        Sanctum::actingAs($patient->user);

        $this->putJson(route('api.appointments.update', $appointment), [
            'doctor_id' => $newDoctor->id,
            'appointment_date' => now()->addDays(5)->toDateString(),
            'appointment_time' => '11:30',
        ])->assertForbidden();

        $this->assertNotSame($newDoctor->id, $appointment->fresh()->doctor_id);
    }

    public function test_receptionist_can_reschedule_their_own_appointment_through_the_api(): void
    {
        $receptionist = Receptionist::factory()->create();
        $appointment = Appointment::factory()->create([
            'receptionist_id' => $receptionist->id,
        ]);
        $newDoctor = Doctor::factory()->create();
        $newDate = now()->addDays(6)->toDateString();

        Sanctum::actingAs($receptionist->user);

        $this->putJson(route('api.appointments.update', $appointment), [
            'doctor_id' => $newDoctor->id,
            'appointment_date' => $newDate,
            'appointment_time' => '12:30',
        ])->assertOk();

        $appointment->refresh();

        $this->assertSame($newDoctor->id, $appointment->doctor_id);
        $this->assertSame($newDate, $appointment->appointment_date->toDateString());
        $this->assertSame('12:30:00', substr((string) $appointment->appointment_time, 0, 8));
    }

    public function test_admin_can_view_but_cannot_update_or_cancel_through_the_api(): void
    {
        $adminUser = \\App\\Models\\User::factory()->create();
        Admin::query()->create(['user_id' => $adminUser->id]);
        $appointment = Appointment::factory()->create();

        Sanctum::actingAs($adminUser);

        $this->getJson(route('api.appointments.show', $appointment))->assertOk();

        $this->putJson(route('api.appointments.update', $appointment), [
            'notes' => 'Admin edit attempt',
        ])->assertForbidden();

        $this->deleteJson(route('api.appointments.destroy', $appointment))
            ->assertForbidden();

        $this->assertSame(Appointment::SCHEDULED, $appointment->fresh()->status);
    }

}
