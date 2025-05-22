<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dữ liệu mẫu cho phí vận chuyển mặc định (trừ Hà Nội)
        $shippingFees = [
            ['province' => '*', 'district' => '*', 'ward' => '*', 'fee' => 300000],

        ];

        // Insert dữ liệu vào bảng shipping_fees
        DB::table('shipping_fees')->insert($shippingFees);
    }
}
