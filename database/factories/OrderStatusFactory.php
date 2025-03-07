<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderStatus;

class OrderStatusFactory extends Factory
{
    protected $model = OrderStatus::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(), // Đảm bảo không trùng lặp
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
