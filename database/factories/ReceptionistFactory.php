<?php
namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceptionistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone'   => fake()->numerify('010########'),
            'city_id' => City::factory(),
        ];
    }
}