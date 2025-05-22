<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index()
    {
        // Lấy tất cả đơn hàng
        $orders = Order::with('user', 'status')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    // Hiển thị chi tiết đơn hàng
    public function show($order_id)
    {
        $order = Order::with(['orderDetails.product', 'status'])->findOrFail($order_id);

        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $order->status_id = $request->status_id;
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }
}
