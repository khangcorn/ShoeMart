<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    public function definition()
    {
        return [
            'order_id' => Order::inRandomOrder()->first()->order_id ?? Order::factory(),
            'product_id' => Product::inRandomOrder()->first()->product_id ?? Product::factory(),
            'variant_id' => ProductVariant::inRandomOrder()->first()->variant_id ?? null, // Cho phép null nếu không có variant
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomFloat(2, 10, 500), // Đảm bảo giá không bị null
            'discount_amount' => $this->faker->randomFloat(2, 0, 50),
            'subtotal' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['price'];
            },
            'total_price' => function (array $attributes) {
                return $attributes['subtotal'] - $attributes['discount_amount'];
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
}
