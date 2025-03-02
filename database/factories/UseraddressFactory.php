<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\UserAddress;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Useraddress>
 */
class UseraddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->user_id,
            'province' => $this->faker->state,
            'district' => $this->faker->city,
            'ward' => $this->faker->streetName,
            'street_address' => $this->faker->address,
            'is_default' => $this->faker->boolean,
        ];
    }
}
