<?php
namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'national_id'     => fake()->unique()->numerify('##############'),
            'phone'           => fake()->numerify('012########'),
            'gender'          => fake()->randomElement(['male', 'female']),
            'birth_date'      => fake()->date('Y-m-d', '-18 years'),
            'city_id'         => City::factory(),
            'receptionist_id' => null,
        ];
    }
}