<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo accounts use predictable local-only credentials. Never seed them in production.
        if (app()->isProduction()) {
            return;
        }

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
