<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(), // Tạo sản phẩm ngẫu nhiên nếu chưa có
            'name' => $this->faker->randomElement(['Size S', 'Size M', 'Size L', 'Màu Đỏ', 'Màu Xanh']),
            'price' => $this->faker->randomFloat(2, 100, 1000), // Giá ngẫu nhiên từ 100 đến 1000
            'stock' => $this->faker->numberBetween(10, 100), // Số lượng tồn kho ngẫu nhiên
        ];
    }
}
