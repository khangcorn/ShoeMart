<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VariantAttribute>
 */
class VariantAttributeFactory extends Factory {
    protected $model = VariantAttribute::class;

    public function definition(): array {
        return [
            'variant_id' => ProductVariant::inRandomOrder()->first()?->variant_id,
            'attribute_name' => $this->faker->randomElement(['Size', 'Color']),
            'attribute_value' => $this->faker->randomElement(['Red', 'Blue', 'Large', 'Medium']),
        ];
    }
}

