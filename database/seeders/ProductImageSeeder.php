<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_images')->insert([
            [
                'product_id' => 1,
                'image_url' => 'images/laptop_asus.jpg',
                'type' => 'main',
            ],
            [
                'product_id' => 1,
                'image_url' => 'images/laptop_asus_2.jpg',
                'type' => 'gallery',
            ],
            [
                'product_id' => 2,
                'image_url' => 'images/iphone_15_pro_max.jpg',
                'type' => 'main',
            ],
            [
                'product_id' => 2,
                'image_url' => 'images/iphone_15_pro_max_2.jpg',
                'type' => 'gallery',
            ],
        ]);
    }
}
