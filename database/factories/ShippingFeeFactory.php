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
            'region' => $this->faker->city,
            'fee' => $this->faker->randomFloat(2, 2, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
