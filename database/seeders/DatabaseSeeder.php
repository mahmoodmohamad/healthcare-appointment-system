<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
            AdminSeeder::class,
            ReceptionistSeeder::class,
            DoctorSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
            DiagnosisSeeder::class,
        ]);
    }
}
