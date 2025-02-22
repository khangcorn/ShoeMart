<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserAddressesTableSeeder extends Seeder
{
    public function run()
    {
        // 🔴 Đảm bảo users có dữ liệu trước khi tạo user_addresses
        $userIds = DB::table('users')->pluck('user_id')->toArray();
        if (empty($userIds)) {
            throw new \Exception("Không có user nào trong bảng users. Hãy chạy UsersTableSeeder trước.");
        }

        // 🔴 Tắt kiểm tra khóa ngoại để tránh lỗi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('user_addresses')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('user_addresses')->insert([
                'user_id' => $faker->randomElement($userIds), // 🟢 Luôn lấy user_id từ danh sách có sẵn
                'province' => $faker->state,
                'district' => $faker->city,
                'ward' => $faker->streetName,
                'street_address' => $faker->address,
                'is_default' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
