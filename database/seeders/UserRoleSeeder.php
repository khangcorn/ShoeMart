<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy danh sách user_id và role_id để seed
        $users = DB::table('users')->pluck('user_id');
        $roles = DB::table('roles')->pluck('role_id');

        // Kiểm tra nếu có user và role để seed
        if ($users->isNotEmpty() && $roles->isNotEmpty()) {
            foreach ($users as $user) {
                // Gán mỗi user một role ngẫu nhiên
                DB::table('user_roles')->insert([
                    'user_id' => $user,
                    'role_id' => $roles->random(),
                ]);
            }
        }
    }
}
