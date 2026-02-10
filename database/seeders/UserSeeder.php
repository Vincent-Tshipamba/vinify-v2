<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Admin UNIKIN',
                'email' => 'admin@unikin.cd',
                'password' => Hash::make('password'),
                'university_id' => null,
            ],
            [
                'name' => 'Admin UNILU',
                'email' => 'admin@unilu.cd',
                'password' => Hash::make('password'),
                'university_id' => null,
            ],
        ]);
    }
}
