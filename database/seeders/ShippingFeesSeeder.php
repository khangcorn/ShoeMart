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
        DB::table('shipping_fees')->insert([
            [
                'province' => 'Hà Nội',
                'district' => 'Quận Ba Đình',
                'ward' => 'Phường Cống Vị',
                'fee' => 30.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Phường Bến Nghé',
                'fee' => 40.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'province' => 'Đà Nẵng',
                'district' => 'Quận Hải Châu',
                'ward' => 'Phường Thuận Phước',
                'fee' => 35.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
