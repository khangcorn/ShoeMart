<?php

namespace Database\Factories;

use App\Models\ShippingFee;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingFeeFactory extends Factory
{
    protected $model = ShippingFee::class;

    public function definition()
    {
        return [
            'province' => $this->faker->city,
            'district' => $this->faker->optional()->city,
            'ward' => $this->faker->optional()->streetName,
            'fee' => $this->faker->randomFloat(2, 10000, 100000),
        ];
    }
}
