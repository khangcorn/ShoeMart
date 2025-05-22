<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        $price = $this->faker->randomFloat(2, 100, 500);

        return [
            'product_id' => Product::inRandomOrder()->first()->product_id ?? Product::factory(),
            'price' => $price,
            'price_sale' => $this->faker->boolean(30) ? $price - $this->faker->randomFloat(2, 10, 50) : null,
            'stock' => $this->faker->numberBetween(10, 100),
        ];
    }
}
