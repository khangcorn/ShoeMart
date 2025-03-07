<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::inRandomOrder()->first()->product_id ?? 1, // Chọn product_id hợp lệ
            'image_url' => $this->faker->imageUrl(640, 480, 'shoes'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }    
}
