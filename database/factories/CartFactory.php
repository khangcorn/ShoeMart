<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory {
    protected $model = Cart::class;

    public function definition(): array {
        return [
            'user_id' => rand(0, 1) ? User::inRandomOrder()->first()?->user_id : null,
            'session_id' => $this->faker->uuid,
        ];
    }
}
