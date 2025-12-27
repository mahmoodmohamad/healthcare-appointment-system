<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Physician;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
class PhysicianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$city = City::first();

        $user = User::create([
            'name' => 'Dr Ahmed Hassan',
            'email' => 'doctor@clinic.com',
            'password' => Hash::make('password'),
            'activation' => true,
        ]);

        Physician::create([
            'user_id' => $user->id,
            'specialization' => 'Internal Medicine',
            'phone' => '01000000001',
            'city_id' => $city->id,
        ]);
    }
}
