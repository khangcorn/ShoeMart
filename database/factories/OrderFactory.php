<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatus;
use App\Models\ShippingFee;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        // Lấy dữ liệu ngẫu nhiên nếu có, nếu không sẽ dùng factory
        $user = optional(User::inRandomOrder()->first()) ?? User::factory()->create();
        $status = optional(OrderStatus::inRandomOrder()->first()) ?? OrderStatus::factory()->create();
        $shipping = optional(ShippingFee::inRandomOrder()->first()) ?? ShippingFee::factory()->create();
        $coupon = optional(Coupon::inRandomOrder()->first());

        // Tạo tổng giá trị đơn hàng ngẫu nhiên
        $totalPrice = $this->faker->randomFloat(2, 50, 1000);
        $shippingFee = $shipping->fee ?? 0;

        // Tính giảm giá từ coupon (nếu có)
        $discount = 0;
        if ($coupon) {
            $discount = $coupon->discount_type == 'percentage'
                ? $totalPrice * $coupon->discount_value / 100
                : $coupon->discount_value;
        }

        // Đảm bảo total không âm
        $finalTotal = max(0, $totalPrice - $discount + $shippingFee);

        return [
            'user_id' => $user->user_id,
            'status_id' => $status->status_id,
            'shipping_id' => $shipping->shipping_id,
            'coupon_id' => $coupon->coupon_id ?? null,
            'order_code' => strtoupper(Str::random(10)), // Mã đơn hàng duy nhất
            'total_price' => $totalPrice,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discount, // Lưu số tiền giảm giá
            'total' => $finalTotal, // Tổng tiền cuối cùng
            'payment_method' => $this->faker->randomElement(['cod', 'bank_transfer', 'credit_card', 'paypal']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
