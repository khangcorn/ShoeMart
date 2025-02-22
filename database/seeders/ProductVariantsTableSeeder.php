<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductVariantsTableSeeder extends Seeder
{
    public function run()
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Tắt kiểm tra khóa ngoại

    DB::table('order_details')->delete(); // Xóa dữ liệu order_details trước
    DB::table('product_variants')->truncate(); // Sau đó mới xóa product_variants

    DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Bật lại kiểm tra khóa ngoại

    $faker = Faker::create();
    $productIds = DB::table('products')->pluck('product_id')->toArray();

    foreach (range(1, 20) as $index) {
        DB::table('product_variants')->insert([
            'product_id' => $faker->randomElement($productIds),
            'price' => $faker->randomFloat(2, 100, 1000),
            'price_sale' => $faker->optional()->randomFloat(2, 50, 900),
            'stock' => $faker->numberBetween(1, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}


}
