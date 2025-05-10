<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_variants')->insert([
            [
                'product_id' => 1, // Laptop ASUS ROG
                'price' => 1550.00,
                'price_sale' => 1450.00,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2, // iPhone 15 Pro Max
                'price' => 1250.00,
                'price_sale' => 1150.00,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
