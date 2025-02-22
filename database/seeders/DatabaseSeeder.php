<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,           // 🟢 Không có khóa ngoại
            StatusesTableSeeder::class,        // 🟢 Không có khóa ngoại
            OrderStatusesTableSeeder::class,   // 🟢 Không có khóa ngoại
            CouponsTableSeeder::class,         // 🟢 Không có khóa ngoại
            SlidersTableSeeder::class,         // 🟢 Không có khóa ngoại
            ProductsTableSeeder::class,        // 🟢 Không có khóa ngoại
            
            UserAddressesTableSeeder::class,   // 🔵 Phụ thuộc vào Users
            ProductVariantsTableSeeder::class, // 🔵 Phụ thuộc vào Products
            VariantsTableSeeder::class,        // 🔵 Phụ thuộc vào ProductVariants
            
            OrdersTableSeeder::class,          // 🔴 Phụ thuộc vào Users, UserAddresses, OrderStatuses
            OrderDetailsTableSeeder::class,    // 🔴 Phụ thuộc vào Orders, Products, ProductVariants
            OrderCouponsTableSeeder::class,    // 🔴 Phụ thuộc vào Orders, Coupons
        ]);
    }
}
