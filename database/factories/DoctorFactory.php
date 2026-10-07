<?php
namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'specialization' => fake()->randomElement(['Cardiology', 'Neurology', 'Pediatrics']),
            'phone'          => fake()->numerify('011########'),
            'city_id'        => City::factory(),
        ];
    }
}