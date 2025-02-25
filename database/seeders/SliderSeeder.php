<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slider::insert([
            [
                'image_url' => 'images/slider1.jpg',
                'caption' => 'Khuyến mãi lớn 2025',
                'link' => 'https://example.com/promo',
                'position' => 1,
            ],
            [
                'image_url' => 'images/slider2.jpg',
                'caption' => 'Bộ sưu tập giày mới',
                'link' => 'https://example.com/new-arrival',
                'position' => 2,
            ],
        ]);
    }
}
