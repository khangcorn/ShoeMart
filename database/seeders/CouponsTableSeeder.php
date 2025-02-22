<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CouponsTableSeeder extends Seeder
{
    public function run()
    {
        // 🔴 Tắt kiểm tra khóa ngoại để tránh lỗi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('coupons')->delete(); // Không dùng truncate() vì có khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create();
        $existingCodes = DB::table('coupons')->pluck('code')->toArray(); // Lấy danh sách mã đã có

        foreach (range(1, 20) as $index) {
            do {
                $couponCode = strtoupper($faker->bothify('COUPON###'));
            } while (in_array($couponCode, $existingCodes)); // Kiểm tra trùng lặp

            $existingCodes[] = $couponCode; // Lưu mã mới vào danh sách để tránh trùng

            DB::table('coupons')->insert([
                'code' => $couponCode,
                'discount_type' => $faker->randomElement(['fixed', 'percentage']),
                'discount_value' => $faker->randomFloat(2, 10000, 200000),
                'max_discount_value' => $faker->optional()->randomFloat(2, 50000, 300000),
                'expiration_date' => $faker->dateTimeBetween('+1 month', '+6 months'),
                'usage_limit' => $faker->optional()->numberBetween(1, 100),
                'usage_count' => 0,
                'status' => $faker->randomElement(['active', 'expired', 'disabled']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
