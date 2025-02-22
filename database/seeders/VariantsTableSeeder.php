<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class VariantsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 20) as $index) {
            DB::table('variants')->insert([
                'product_id' => DB::table('products')->inRandomOrder()->first()->product_id,
                'price' => $faker->numberBetween(500000, 5000000),
                'price_sale' => $faker->optional()->numberBetween(400000, 4500000),
                'stock' => $faker->numberBetween(10, 50),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
