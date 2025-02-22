<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Order;
use App\Models\Coupon;

class OrderCouponsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Kiểm tra nếu có dữ liệu hợp lệ
        $order = Order::inRandomOrder()->first();
        $coupon = Coupon::inRandomOrder()->first();

        if (!$order || !$coupon) {
            return; // Tránh lỗi nếu thiếu dữ liệu
        }

        foreach (range(1, 20) as $index) {
            DB::table('order_coupons')->insert([
                'order_id' => Order::inRandomOrder()->first()->order_id ?? null,
                'coupon_id' => Coupon::inRandomOrder()->first()->coupon_id ?? null,
                'applied_amount' => $faker->randomFloat(2, 5000, 50000),
                'created_at' => now(),
            ]);
        }
    }
}
