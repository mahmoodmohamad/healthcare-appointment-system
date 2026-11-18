<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Egypt', 'Saudi Arabia', 'UAE'] as $name) {
            Country::firstOrCreate(['name' => $name]);
        }
    }
}
