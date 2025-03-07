<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingFee;

class ShippingFeeSeeder extends Seeder
{
    public function run()
    {
        ShippingFee::factory(10)->create();
    }
}
