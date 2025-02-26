<?php

namespace Database\Seeders;

use App\Models\OrderCoupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        OrderCoupon::factory(10)->create(); 
    }
}
