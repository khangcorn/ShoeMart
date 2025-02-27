<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartDetail;

class CartDetailsSeeder extends Seeder
{
    public function run()
    {
        CartDetail::insert([
            [
                'cart_id'    => 1,
                'product_id' => 1,
                'variant_id' => 1,
                'quantity'   => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cart_id'    => 2,
                'product_id' => 2,
                'variant_id' => 2,
                'quantity'   => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
