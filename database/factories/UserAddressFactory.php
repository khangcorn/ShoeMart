<?php

namespace Database\Factories;

use App\Models\UserAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory {
    protected $model = UserAddress::class;

    public function definition(): array {
        return [
           'user_id' => User::query()->inRandomOrder()->value('user_id') ?? User::factory(),
            'province' => $this->faker->state,
            'district' => $this->faker->city,
            'ward' => $this->faker->streetName,
            'street_address' => $this->faker->address,
            'is_default' => $this->faker->boolean,
        ];
    }
}
