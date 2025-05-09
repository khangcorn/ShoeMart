<?php

namespace Database\Seeders;

use App\Models\UserAddresses;  // <— dùng đúng tên model
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder {
    public function run(): void {
        UserAddresses::factory(10)->create();
    }
}
