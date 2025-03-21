<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['role_id' => 1, 'name' => 'admin', 'description' => 'Quản trị viên'],
            ['role_id' => 2, 'name' => 'user', 'description' => 'Người dùng'],
        ]);
    }
}
