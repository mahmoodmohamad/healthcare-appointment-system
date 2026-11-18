<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $egypt = Country::firstOrCreate(['name' => 'Egypt']);

        foreach (['Cairo', 'Giza', 'Alexandria'] as $name) {
            City::firstOrCreate([
                'name' => $name,
                'country_id' => $egypt->id,
            ]);
        }
    }
}
