<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CartDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartDetailFactory extends Factory
{
    protected $model = CartDetail::class;

    public function definition()
    {
        $cart = Cart::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        $variant = ProductVariant::where('product_id', $product->product_id)->inRandomOrder()->first();
        
        return [
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'variant_id' => $variant ? $variant->variant_id : null,
            'quantity' => $this->faker->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
