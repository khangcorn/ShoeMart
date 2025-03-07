<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderDetail;

class OrderDetailSeeder extends Seeder
{
    public function run()
    {
        OrderDetail::query()->delete();
        OrderDetail::factory(60)->create();
    }
}
