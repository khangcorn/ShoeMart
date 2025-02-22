<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory {
    public function definition() {
        return [
            'product_id' => Product::factory(),
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'price_sale' => $this->faker->optional()->randomFloat(2, 50, 900),
            'stock' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
