<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory {
    public function definition() {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('COUPON###')),
            'discount_type' => $this->faker->randomElement(['fixed', 'percentage']),
            'discount_value' => $this->faker->randomFloat(2, 10, 100),
            'max_discount_value' => $this->faker->optional()->randomFloat(2, 50, 200),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'usage_limit' => $this->faker->optional()->numberBetween(1, 100),
            'usage_count' => 0,
            'status' => $this->faker->randomElement(['active', 'expired', 'disabled']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
