<?php

namespace Database\Factories;

use App\Models\VariantAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VariantAttribute>
 */
class VariantAttributeFactory extends Factory
{
    protected $model = VariantAttribute::class;

    public function definition(): array
    {
        return [

            'attribute_name' => $this->faker->randomElement(['Size', 'Color', 'Material']),
        ];
    }
}
