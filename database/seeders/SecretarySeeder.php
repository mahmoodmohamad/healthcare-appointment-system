<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Physician;
use App\Models\Secretary;
use Illuminate\Support\Facades\Hash;
class SecretarySeeder extends Seeder
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
        $physician = Physician::first();

        $user = User::create([
            'name' => 'Clinic Secretary',
            'email' => 'secretary@clinic.com',
            'password' => Hash::make('password'),
            'activation' => true,
        ]);

        Secretary::create([
            'user_id' => $user->id,
            'phone' => '01000000002',
            'physician_id' => $physician->id,
            'city_id' => $city->id,
        ]);
    }
}
