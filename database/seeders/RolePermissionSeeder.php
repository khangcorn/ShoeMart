<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy danh sách role_id và permission_id để seed
        $roles = DB::table('roles')->pluck('role_id');
        $permissions = DB::table('permissions')->pluck('permission_id');

        // Kiểm tra nếu có dữ liệu để seed
        if ($roles->isNotEmpty() && $permissions->isNotEmpty()) {
            foreach ($roles as $role) {
                // Gán mỗi role một số quyền ngẫu nhiên
                $randomPermissions = $permissions->random(rand(1, $permissions->count()));

                foreach ($randomPermissions as $permission) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $role,
                        'permission_id' => $permission,
                    ]);
                }
            }
        }
    }
}
