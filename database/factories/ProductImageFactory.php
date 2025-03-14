<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition()
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->product_id ?? Product::factory(),
            'image_url' => $this->faker->imageUrl(640, 480, 'shoes', true),
            'type' => $this->faker->randomElement(['main', 'gallery']),
        ];
    }
}
