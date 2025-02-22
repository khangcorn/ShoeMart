<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SlidersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('sliders')->truncate();

        $faker = Faker::create();
        foreach (range(1, 5) as $index) {
            DB::table('sliders')->insert([
                'image_url' => $faker->imageUrl(800, 400, 'fashion'),
                'caption' => $faker->sentence,
                'link' => $faker->url,
                'position' => $index,
            ]);
        }
    }
}

