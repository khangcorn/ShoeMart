<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CartDetail;
use Attribute;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            CartSeeder::class,
            CartDetailsSeeder::class,
            SliderSeeder::class,
            ProductImageSeeder::class,
            ProductVariantSeeder::class,
            AttributeVariantSeeder::class
        ]);
        // $this->call(UserSeeder::class);

    }
}
