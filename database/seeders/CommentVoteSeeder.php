<?php

namespace Database\Seeders;

use App\Models\CommentVote;
use Illuminate\Database\Seeder;

class CommentVoteSeeder extends Seeder
{
    public function run(): void
    {
        CommentVote::factory(50)->create(); // Tạo 50 bản ghi mẫu
    }
}
