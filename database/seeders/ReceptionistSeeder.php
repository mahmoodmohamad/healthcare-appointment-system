<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReceptionistSeeder extends Seeder
{
    public function run()
    {
        $city = City::firstOrFail();

        $user = User::create([
            'name'     => 'Clinic Receptionist',
            'email'    => 'receptionist@clinic.com',
            'password' => Hash::make('password'),
        ]);

        Receptionist::create([
            'user_id' => $user->id,
            'phone'   => '01000000002',
            'city_id' => $city->id,
        ]);
    }
}