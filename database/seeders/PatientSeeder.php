<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::query()->first();
        if (! $city) {
            $this->call([CountrySeeder::class, CitySeeder::class]);
            $city = City::query()->firstOrFail();
        }

        if (! Receptionist::query()->exists()) {
            $this->call( ReceptionistSeeder::class);
        }
        $receptionist = Receptionist::query()->firstOrFail();

        for ($i = 1; $i <= 10; $i++) {
            $email = "patient{$i}@clinic.com";
            $user = User::firstOrCreate(['email' => $email], [
                'name' => "Patient {$i}",
                'password' => Hash::make('password'),
                'activation' => true,
            ]);

            Patient::firstOrCreate(['user_id' => $user->id], [
                'national_id' => sprintf('CLINIC-PAT-%04d', $i),
                'phone' => sprintf('010100000%02d', $i),
                'gender' => $i % 2 === 0 ? 'female' : 'male',
                'birth_date' => now()->subYears(20 + $i),
                'city_id' => $city->id,
                'receptionist_id' => $receptionist->id,
            ]);
        }
    }
}
