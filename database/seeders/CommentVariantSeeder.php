<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommentVariant;

class CommentVariantSeeder extends Seeder {
    public function run(): void {
        CommentVariant::factory(30)->create(); // Tạo 30 bản ghi mẫu
    }
}
