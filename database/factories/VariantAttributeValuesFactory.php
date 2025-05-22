<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValues;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariantAttributeValuesFactory extends Factory
{
    protected $model = VariantAttributeValues::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'variant_id' => ProductVariant::factory(),
            'attribute_id' => VariantAttribute::factory(),
            'attribute_value' => $this->faker->randomElement(['Red', 'Blue', 'Green', 'S', 'M', 'L']),
            'stock' => $this->faker->numberBetween(0, 100),
        ];
    }
}
