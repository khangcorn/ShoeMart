<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserfactoryFactory extends Factory
{
    protected $model = User::class;
    public function definition(): array
    {
        return [
            'username' => $this->faker->userName,
            'password_hash' => Hash::make('password'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '+84' . $this->faker->numerify('#########'),
            'address' => $this->faker->address,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
