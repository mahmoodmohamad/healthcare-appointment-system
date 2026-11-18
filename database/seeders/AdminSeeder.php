<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@clinic.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'activation' => true,
            ]
        );

        Admin::firstOrCreate(['user_id' => $user->id]);
    }
}
