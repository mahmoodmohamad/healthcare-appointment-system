<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Patient;
use App\Models\Secretary;
use Illuminate\Support\Facades\Hash;
class PatientSeeder extends Seeder
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
        $secretary = Secretary::first();

        for ($i = 1; $i <= 10; $i++) {

            $user = User::create([
                'name' => "Patient $i",
                'email' => "patient$i@clinic.com",
                'password' => Hash::make('password'),
                'activation' => true,
            ]);

            Patient::create([
                'user_id' => $user->id,
                'national_id' => '29801010' . rand(1000, 9999),
                'phone' => '0101000000' . $i,
                'gender' => $i % 2 ? 'male' : 'female',
                'birth_date' => now()->subYears(rand(20, 60)),
                'city_id' => $city->id,
                'secretary_id' => $secretary->id,
            ]);
        }
    }
}
