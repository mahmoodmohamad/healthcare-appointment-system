<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::query()->orderBy('id')->first();

        if (! $city) {
            $this->call([CountrySeeder::class, CitySeeder::class]);
            $city = City::query()->orderBy('id')->firstOrFail();
        }

        $doctors = [
            [
                'name' => 'Dr Ahmed Hassan',
                'email' => 'doctor@clinic.com',
                'specialization' => 'General Medicine',
                'phone' => '01000000003',
            ],
            [
                'name' => 'Dr Mohamed Adel',
                'email' => 'doctor2@clinic.com',
                'specialization' => 'Cardiology',
                'phone' => '01000000004',
            ],
        ];

        foreach ($doctors as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'activation' => true,
                ]
            );

            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $data['specialization'],
                    'phone' => $data['phone'],
                    'city_id' => $city->id,
                ]
            );
        }
    }
}
