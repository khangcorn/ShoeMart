<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition()
    {
        return [
            'code' => strtoupper(Str::random(10)),
            'discount_type' => $this->faker->randomElement(['fixed', 'percentage']),
            'discount_value' => $this->faker->randomFloat(2, 5, 50),
            'max_discount_value' => $this->faker->optional()->randomFloat(2, 50, 200),
            'expiration_date' => optional($this->faker->optional()->dateTimeBetween('now', '+1 year'))->format('Y-m-d') ?? now()->addYear()->format('Y-m-d'),
            'usage_limit' => $this->faker->optional()->numberBetween(10, 100),
            'usage_count' => 0,
            'status' => 'active',
        ];
    }
}

