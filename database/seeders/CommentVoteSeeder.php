<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommentVote;

class CommentVoteSeeder extends Seeder {
    public function run(): void {
        CommentVote::factory(50)->create(); // Tạo 50 bản ghi mẫu
    }
}

