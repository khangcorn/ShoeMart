<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Coupon;
use App\Models\OrderCoupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderCouponFactory extends Factory
{
    protected $model = OrderCoupon::class;

    public function definition()
    {
        $coupon = Coupon::inRandomOrder()->first();
        $order = Order::inRandomOrder()->first();
        
        $appliedAmount = $this->calculateDiscount($order->total, $coupon);

        return [
            'order_id' => $order->order_id,
            'coupon_id' => $coupon->coupon_id,
            'applied_amount' => $appliedAmount,
            'created_at' => now(),
        ];
    }

    private function calculateDiscount($total, $coupon)
    {
        if ($coupon->discount_type === 'fixed') {
            return min($coupon->discount_value, $total);
        }
        
        $discount = ($total * $coupon->discount_value) / 100;
        
        return $coupon->max_discount_value ? min($discount, $coupon->max_discount_value) : $discount;
    }
}