<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
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
}
