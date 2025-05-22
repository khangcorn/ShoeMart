<?php

namespace Database\Seeders;

use App\Models\CommentVariant;
use Illuminate\Database\Seeder;

class CommentVariantSeeder extends Seeder
{
    public function run(): void
    {
        CommentVariant::factory(30)->create(); // Tạo 30 bản ghi mẫu
    }
}
