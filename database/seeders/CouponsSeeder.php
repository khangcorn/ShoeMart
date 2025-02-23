<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coupons')->insert([
            [
                'code' => 'DISCOUNT10',
                'discount_type' => 'fixed',
                'discount_value' => 10.00,
                'max_discount_value' => null,
                'expiration_date' => '2025-12-31',
                'usage_limit' => 100,
                'usage_count' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SAVE20',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'max_discount_value' => 50.00,
                'expiration_date' => '2025-12-31',
                'usage_limit' => 200,
                'usage_count' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FREESHIP',
                'discount_type' => 'fixed',
                'discount_value' => 0.00,
                'max_discount_value' => null,
                'expiration_date' => '2025-12-31',
                'usage_limit' => 500,
                'usage_count' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
