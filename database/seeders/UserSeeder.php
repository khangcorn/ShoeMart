<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'email'    => 'admin@example.com',
                'phone'    => '123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'user1',
                'password' => Hash::make('password123'),
                'email'    => 'user1@example.com',
                'phone'    => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
