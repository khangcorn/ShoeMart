<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Coupon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->bothify('COUP###'),
            'discount_type' => $this->faker->randomElement(['fixed', 'percentage']),
            'discount_value' => $this->faker->randomFloat(2, 5, 50),
            'expiration_date' => $this->faker->dateTimeBetween('+1 week', '+1 year')->format('Y-m-d'),
            'usage_limit' => $this->faker->numberBetween(1, 100),
            'usage_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
