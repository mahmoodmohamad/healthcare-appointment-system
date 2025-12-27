<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$countries = ['Egypt', 'Saudi Arabia', 'UAE'];

        foreach ($countries as $country) {
            Country::create([
                'name' => $country
            ]);
        }
    }
}
