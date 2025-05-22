<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ trước khi seed

        \App\Models\OrderStatus::factory(10)->create(); // Số lượng vừa đủ để tránh trùng
    }
}
