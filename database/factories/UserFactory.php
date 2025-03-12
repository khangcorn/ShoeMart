<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'password' => Hash::make('password'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+84' . $this->faker->numerify('#########'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
