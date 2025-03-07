<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatus;
use App\Models\ShippingFee;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $status = OrderStatus::inRandomOrder()->first();
        $shipping = ShippingFee::inRandomOrder()->first();
        $coupon = Coupon::inRandomOrder()->first();

        $totalPrice = $this->faker->randomFloat(2, 50, 1000); // Tổng giá trị đơn hàng
        $shippingFee = $shipping ? $shipping->fee : 0; // Phí ship
        $discount = $coupon ? ($coupon->discount_type == 'percentage' ? $totalPrice * $coupon->discount_value / 100 : $coupon->discount_value) : 0;

        return [
            'user_id' => $user ? $user->user_id : User::factory(), // Nếu không có user nào, tạo mới
            'total_price' => $totalPrice,
            'total' => max(0, $totalPrice - $discount + $shippingFee), // Tổng tiền cuối cùng
            'status_id' => $status ? $status->status_id : OrderStatus::factory(),
            'shipping_fee' => $shippingFee,
            'shipping_id' => $shipping ? $shipping->shipping_id : ShippingFee::factory(),
            'coupon_id' => $coupon ? $coupon->coupon_id : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
