<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRoles;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, CreatesRoles;

    public function test_deactivated_user_cannot_login(): void
    {
        $user = $this->doctorUser();
        $user->forceFill(['activation' => false])->save();

        $this->post(route('login.custom'), ['email' => $user->email, 'password' => 'password'])
             ->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->doctorUser();

        $this->post(route('login.custom'), ['email' => $user->email, 'password' => 'wrong'])
             ->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_login_redirects_by_role(): void
    {
        $this->post(route('login.custom'), ['email' => $this->adminUser()->email, 'password' => 'password'])
             ->assertRedirect(route('admin.dashboard'));
        $this->post('/logout');

        $this->post(route('login.custom'), ['email' => $this->doctorUser()->email, 'password' => 'password'])
             ->assertRedirect(route('doctor.dashboard'));
    }

    public function test_login_is_throttled_after_5_attempts(): void
    {
        $user = $this->doctorUser();
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.custom'), ['email' => $user->email, 'password' => 'bad']);
        }
        $this->post(route('login.custom'), ['email' => $user->email, 'password' => 'password'])
             ->assertSessionHas('error');
        $this->assertGuest();
    }
}