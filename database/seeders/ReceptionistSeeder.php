<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Receptionist;
use Illuminate\Support\Facades\Hash;
class ReceptionistSeeder extends Seeder
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
        $doctor = Doctor::first();

        $user = User::create([
            'name' => 'Clinic Receptionist',
            'email' => 'receptionist@clinic.com',
            'password' => Hash::make('password'),
            'activation' => true,
        ]);

        Receptionist::create([
            'user_id' => $user->id,
            'phone' => '01000000002',
            'doctor_id' => $doctor->id,
            'city_id' => $city->id,
        ]);
    }
}
