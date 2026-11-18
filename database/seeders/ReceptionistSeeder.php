<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReceptionistSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::query()->orderBy('id')->first();

        if (! $city) {
            $this->call([CountrySeeder::class, CitySeeder::class]);
            $city = City::query()->orderBy('id')->firstOrFail();
        }

        $user = User::firstOrCreate(
            ['email' => 'receptionist@clinic.com'],
            [
                'name' => 'Clinic Receptionist',
                'password' => Hash::make('password'),
                'activation' => true,
            ]
        );

        Receptionist::firstOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => '01000000002',
                'city_id' => $city->id,
            ]
        );
    }
}
