<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'name' => fake()->unique()->jobTitle(), // Đảm bảo giá trị không trùng
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
