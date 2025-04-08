<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'status'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($order_id)
    {
        $order = Order::with([
            'userAddresses',
            'orderDetails.product',
            'orderDetails.variant.attributes.variantAttribute',
            'orderCoupons',
            'status',
            'user',
        ])->findOrFail($order_id);

        $statuses = OrderStatus::all();

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, $order_id)
    {
        $request->validate([
            'status_id' => 'required|exists:order_statuses,status_id',
        ]);

        $order = Order::findOrFail($order_id);
        $order->status_id = $request->status_id;
        $order->save();

        return redirect()->route('admin.orders.show', $order_id)->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}