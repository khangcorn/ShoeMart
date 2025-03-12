<?php

namespace Database\Seeders;

use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder {
    public function run(): void {
        UserAddress::factory(10)->create();
    }
}
