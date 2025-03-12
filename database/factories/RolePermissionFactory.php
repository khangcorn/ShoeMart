<?php

namespace Database\Factories;

use App\Models\RolePermission;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolePermissionFactory extends Factory
{
    protected $model = RolePermission::class;

    public function definition()
    {
        return [
            'role_id' => Role::inRandomOrder()->first()->id ?? Role::factory(),
            'permission_id' => Permission::inRandomOrder()->first()->id ?? Permission::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
