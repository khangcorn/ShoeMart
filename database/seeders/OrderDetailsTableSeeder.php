<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrderDetailsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
    
        $order = DB::table('orders')->inRandomOrder()->first();
        $product = DB::table('products')->inRandomOrder()->first();
        $variant = DB::table('product_variants')->inRandomOrder()->first();
    
        if (!$order || !$product) {
            return; // Nếu không có đơn hàng hoặc sản phẩm thì dừng
        }
    
        foreach (range(1, 20) as $index) {
            DB::table('order_details')->insert([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'variant_id' => $variant ? $variant->variant_id : null,
                'quantity' => $faker->numberBetween(1, 5),
                'price' => $faker->numberBetween(500000, 5000000),
                'discount_amount' => $faker->numberBetween(0, 100000),
                'subtotal' => $faker->numberBetween(1000000, 10000000),
                'total_price' => $faker->numberBetween(1000000, 10000000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
}
