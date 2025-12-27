<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		 $user = User::create([
            'name' => 'System Admin',
            'email' => 'admin@clinic.com',
            'password' => Hash::make('password'),
            'activation' => true,
        ]);

        Admin::create([
            'user_id' => $user->id
        ]);
    }
}
