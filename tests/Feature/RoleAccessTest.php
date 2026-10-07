<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRoles;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase, CreatesRoles;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_doctor_cannot_open_admin_pages(): void
    {
        $this->actingAs($this->doctorUser())->get(route('admin.doctors.index'))->assertForbidden();
    }

    public function test_receptionist_cannot_open_admin_pages(): void
    {
        $this->actingAs($this->receptionistUser())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $this->actingAs($this->adminUser())->get(route('admin.dashboard'))->assertOk();
    }
}