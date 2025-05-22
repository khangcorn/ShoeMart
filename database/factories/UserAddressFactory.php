<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory
{
    protected $model = UserAddress::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('user_id') ?? User::factory(),
            'rephpcipient_name' => $this->faker->name,
            'recipient_phone' => substr($this->faker->numerify('+84 (###) ###-####'), 0, 10),
            'recipient_email' => $this->faker->optional()->safeEmail,
            'province' => $this->faker->state,
            'district' => $this->faker->city,
            'ward' => $this->faker->streetName,
            'street_address' => $this->faker->address,
            'is_default' => $this->faker->boolean(20), // 20% địa chỉ mặc định
        ];
    }
}
