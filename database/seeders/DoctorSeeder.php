<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Doctor;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
class DoctorSeeder extends Seeder
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
    'name'     => 'Dr Ahmed Hassan',
    'email'    => 'doctor@clinic.com',
    'password' => Hash::make('password'),
]);

        $user = User::create([
    'name'     => 'Dr Mohamed Adel',
    'email'    => 'doctor2@clinic.com',
    'password' => Hash::make('password'),
]);
    }
}
