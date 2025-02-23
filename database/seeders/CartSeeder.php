<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dữ liệu mẫu cho bảng carts
        DB::table('carts')->insert([
            [
                'user_id' => 1, // Nếu có người dùng đã đăng nhập
                'session_id' => null, // Nếu là người dùng đã đăng nhập
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => null, // Khách vãng lai
                'session_id' => Str::random(255), // Tạo session_id ngẫu nhiên
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}

