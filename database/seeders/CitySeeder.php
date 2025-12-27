<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;;
use App\Models\City;
class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$egypt = Country::where('name', 'Egypt')->first();

        $cities = ['Cairo', 'Giza', 'Alexandria'];

        foreach ($cities as $city) {
            City::create([
                'name' => $city,
                'country_id' => $egypt->id
            ]);
        }
    }
}
