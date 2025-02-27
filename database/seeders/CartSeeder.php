<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use Illuminate\Support\Str;

class CartSeeder extends Seeder
{
    public function run()
    {
        Cart::insert([
            [
                'user_id'    => 1,
                'session_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 2,
                'session_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => null,
                'session_id' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => null,
                'session_id' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
