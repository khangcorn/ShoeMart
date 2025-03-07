<?php

namespace Database\Factories;

use App\Models\UserAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory {
    protected $model = UserAddress::class;

    public function definition(): array {
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
