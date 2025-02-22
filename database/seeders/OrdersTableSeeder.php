<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrdersTableSeeder extends Seeder {
    public function run() {
        $faker = Faker::create();
        
        if (\App\Models\User::count() === 0 || \App\Models\UserAddress::count() === 0 || \App\Models\OrderStatus::count() === 0) {
            return;
        }

        foreach (range(1, 10) as $index) {
            DB::table('orders')->insert([
                'order_code' => strtoupper($faker->bothify('ORDER###')),
                'user_id' => \App\Models\User::inRandomOrder()->first()->user_id,
                'address_id' => \App\Models\UserAddress::inRandomOrder()->first()->address_id,
                'total' => $faker->randomFloat(2, 500000, 5000000),
                'status_id' => \App\Models\Status::inRandomOrder()->first()->status_id ?? 1,
                'shipping_fee' => $faker->randomFloat(2, 20000, 100000),
                'payment_method' => $faker->randomElement(['cod', 'bank_transfer', 'credit_card', 'paypal']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
