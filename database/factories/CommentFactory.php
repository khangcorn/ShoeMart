<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('user_id') ?? User::factory(),
            'product_id' => Product::query()->inRandomOrder()->value('product_id') ?? Product::factory(),
            'parent_id' => (rand(0, 1) ? Comment::query()->inRandomOrder()->value('comment_id') : null), // 50% có parent_id để tạo reply
            'content' => $this->faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
