<?php

namespace App\Http\Controllers;

use App\Models\OrderCoupon;

class OrderCouponController extends Controller
{
    public function index()
    {
        $orderCoupons = OrderCoupon::with(['order', 'coupon'])->get();
        return view('admin.order_coupons.index', compact('orderCoupons'));
    }

    public function show(OrderCoupon $orderCoupon)
    {
        return view('admin.order_coupons.show', compact('orderCoupon'));
    }
}
