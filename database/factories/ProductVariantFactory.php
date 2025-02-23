<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id ?? Product::factory(),
            'size' => $this->faker->randomElement(['38', '39', '40', '41', '42', '43', '44']),
            'color' => $this->faker->safeColorName,
            'stock' => $this->faker->numberBetween(0, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
