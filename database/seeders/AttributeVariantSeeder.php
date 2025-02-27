<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeVariantSeeder extends Seeder
{
    public function run()
    {
        DB::table('variant_attributes')->insert([
            [
                'variant_id' => 1, // Laptop ASUS ROG
                'attribute_name' => 'RAM',
                'attribute_value' => '16GB',
            ],
            [
                'variant_id' => 1,
                'attribute_name' => 'SSD',
                'attribute_value' => '512GB',
            ],
            [
                'variant_id' => 2, // iPhone 15 Pro Max
                'attribute_name' => 'Màu sắc',
                'attribute_value' => 'Xanh Titan',
            ],
            [
                'variant_id' => 2,
                'attribute_name' => 'Bộ nhớ',
                'attribute_value' => '256GB',
            ],
        ]);
    }
}
