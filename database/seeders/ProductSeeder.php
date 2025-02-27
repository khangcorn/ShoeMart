<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'category_id' => 1, // Đảm bảo category_id = 1 tồn tại trong bảng categories
                'name' => 'Laptop Gaming ASUS ROG',
                'description' => 'Laptop gaming mạnh mẽ với card RTX 4060',
                'price' => 1500.00,
                'price_sale' => 1400.00,
                'stock' => 50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' =>2, // Đảm bảo category_id = 2 tồn tại trong bảng categories
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Smartphone cao cấp của Apple',
                'price' => 1200.00,
                'price_sale' => null, // Để null đúng cách
                'stock' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
