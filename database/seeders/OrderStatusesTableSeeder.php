<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusesTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('order_statuses')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('order_statuses')->insert([
            ['name' => 'Pending', 'description' => 'Order is pending', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Completed', 'description' => 'Order completed', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cancelled', 'description' => 'Order cancelled', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
