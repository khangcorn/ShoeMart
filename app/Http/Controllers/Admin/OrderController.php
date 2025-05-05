<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    $allowedStatusIds = [1, 2, 5, 7]; // Chỉ cho phép các trạng thái này

    $request->validate([
        'status_id' => ['required', Rule::in($allowedStatusIds)],
    ]);

    $order = Order::findOrFail($order_id);
    $order->status_id = $request->status_id;
    $order->save();

    return redirect()->route('admin.orders.show', $order_id)
        ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
}
public function cancel($order_id)
{
    $order = Order::with(['orderDetails.variant', 'user.wallet'])->findOrFail($order_id);

    if ($order->status_id != 1) {
        return back()->with('error', 'Chỉ có thể hủy đơn hàng mới.');
    }

    foreach ($order->orderDetails as $detail) {
        if ($detail->variant_id) {
            $variant = \App\Models\ProductVariant::find($detail->variant_id);
            if ($variant) {
                $variant->increment('stock', $detail->quantity);
            }
        }
    }
    // Hoàn tiền vào ví nếu thanh toán qua ví
    if ($order->payment_method === 'wallet') {
        $wallet = $order->user->wallet;
        $wallet->balance += $order->total;
        $wallet->save();

        // Ghi log giao dịch hoàn tiền
        \App\Models\WalletTransaction::create([
            'wallet_id'   => $wallet->wallet_id,
            'amount'      => $order->total,
            'type'        => 'refund',
            'description' => 'Hoàn tiền do shop hủy đơn hàng #' . $order->order_code,
            'status'      => 'completed',
        ]);
    }

    $order->status_id = 5; // Đơn bị hủy bởi shop
    $order->save();

    return back()->with('success', 'Đã hủy đơn hàng, hoàn tiền và cập nhật tồn kho.');
}


}