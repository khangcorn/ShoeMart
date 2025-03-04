<?php

namespace Database\Factories;

use App\Models\Slider;
use Illuminate\Database\Eloquent\Factories\Factory;

class SliderFactory extends Factory
{
    protected $model = Slider::class;

    public function definition()
    {
        return [
            'image_url' => $this->faker->imageUrl(1200, 400, 'banner'),
            'caption' => $this->faker->sentence(10), // Mô tả ngắn cho slider
            'link' => $this->faker->optional()->url(), // Link có thể có hoặc không
            'position' => $this->faker->unique()->numberBetween(1, 10), // Tránh trùng vị trí
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
