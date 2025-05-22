<?php

namespace Database\Seeders;

use App\Models\OrderDetail;
use Illuminate\Database\Seeder;

class OrderDetailSeeder extends Seeder
{
    public function run()
    {
        OrderDetail::query()->delete();
        OrderDetail::factory(60)->create();
    }
}
