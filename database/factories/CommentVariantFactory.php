<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentVariantFactory extends Factory
{
    protected $model = CommentVariant::class;

    public function definition(): array
    {
        return [
            'comment_id' => Comment::inRandomOrder()->first()?->comment_id,
            'user_id' => User::inRandomOrder()->first()?->user_id,
            'content' => $this->faker->sentence(10),
        ];
    }
}
