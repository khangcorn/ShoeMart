<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        // 🔴 Tắt kiểm tra khóa ngoại để tránh lỗi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ thay vì truncate
        DB::table('variants')->delete();
        DB::table('products')->delete();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            DB::table('products')->insert([
                'name' => $faker->words(3, true),
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat(2, 100000, 1000000),
                'price_sale' => $faker->optional()->randomFloat(2, 50000, 900000),
                'stock' => $faker->numberBetween(0, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
