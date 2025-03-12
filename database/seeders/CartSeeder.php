<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder {
    public function run() {
        $users = User::pluck('user_id')->toArray(); // Lấy danh sách user_id từ bảng users

        Cart::factory(100)->create([
            'user_id' => $users ? fake()->randomElement($users) : null, // Chọn user_id ngẫu nhiên
        ]);
    }
}

