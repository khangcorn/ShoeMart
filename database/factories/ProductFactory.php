<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory {
    protected $model = Product::class;

    public function definition(): array {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 500), // Giá từ 50 đến 500
            'price_sale' => $this->faker->optional(0.5)->randomFloat(2, 30, 450), // 50% sản phẩm có giá giảm
            'stock' => $this->faker->numberBetween(0, 100), // Số lượng tồn kho
            'category_id' => Category::inRandomOrder()->first()?->category_id, // Lấy ngẫu nhiên category_id
        ];
    }
}
