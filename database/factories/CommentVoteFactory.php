<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentVoteFactory extends Factory
{
    protected $model = CommentVote::class;

    public function definition(): array
    {
        return [
            'comment_id' => Comment::inRandomOrder()->first()?->comment_id,
            'user_id' => User::inRandomOrder()->first()?->user_id,
            'vote_type' => $this->faker->randomElement(['helpful', 'not_helpful']),
        ];
    }
}
