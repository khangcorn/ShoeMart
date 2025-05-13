<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCoupon;
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
    public function cancel($order_id)
    {
        $order = Order::with(['orderDetails.variant', 'user.wallet'])->findOrFail($order_id);
        if (!auth()->user()->hasPermission('update_order_status')) {
            return redirect()->route('admin.orders.index')
                            ->with('error', 'Bạn không có quyền hủy đơn hàng.');
        }
        // Kiểm tra trạng thái đơn hàng, chỉ cho phép hủy đơn khi trạng thái là "Đơn hàng mới" (status_id == 1)
        if ($order->status_id != 1) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng mới.');
        }
    
        // Cộng lại số lượng tồn kho cho từng sản phẩm trong đơn
        foreach ($order->orderDetails as $detail) {
            if ($detail->variant_id) {
                $variant = \App\Models\ProductVariant::find($detail->variant_id);
                if ($variant) {
                    $variant->increment('stock', $detail->quantity);
                }
            }
        }
    
        // Kiểm tra nếu đơn có mã giảm giá, tăng usage_count của các mã giảm giá
        $orderCoupons = OrderCoupon::where('order_id', $order_id)->get(); // Lấy tất cả các mã giảm giá của đơn hàng
        foreach ($orderCoupons as $orderCoupon) {
            // Lấy coupon từ coupon_id trong bảng order_coupons
            $coupon = Coupon::find($orderCoupon->coupon_id);
            if ($coupon) {
                // Tăng usage_count của mã giảm giá
                $coupon->decrement('usage_count');
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
    
        // Cập nhật trạng thái đơn hàng thành "Đã hủy"
        $order->status_id = 5; // 5: Đơn bị hủy bởi shop
        $order->save();
    
        return back()->with('success', 'Đã hủy đơn hàng, hoàn tiền và cập nhật tồn kho.');
    }
    
public function ajaxUpdateStatus(Request $request, $orderId)
{
    $order = Order::find($orderId);
    if (!$order) {
        return response()->json(['error' => 'Không tìm thấy đơn hàng.'], 404);
    }

    // Danh sách trạng thái không thể thay đổi
    $lockedStatuses = [3, 4, 5, 6, 8]; // Nếu trạng thái của đơn hàng là các trạng thái này thì không thể thay đổi

    if (in_array($order->status_id, $lockedStatuses)) {
        return response()->json(['error' => 'Không thể cập nhật trạng thái đơn hàng này.'], 403);
    }

    // Lấy status_id mới từ request
    $newStatusId = $request->input('status_id');

    // Logic thay đổi trạng thái
    if ($order->status_id == 1) { // Nếu đơn hàng đang ở trạng thái "Đơn hàng mới"
        if (!in_array($newStatusId, [2, 7])) { // Chỉ cho phép chuyển sang trạng thái "Đang vận chuyển" (2) hoặc "Đã giao hàng" (7)
            return response()->json(['error' => 'Không thể cập nhật trạng thái đơn hàng này.'], 403);
        }
    } elseif ($order->status_id == 2) { // Nếu đơn hàng đang ở trạng thái "Đang vận chuyển"
        if ($newStatusId == 1) { // Không thể chuyển về trạng thái "Đơn hàng mới"
            return response()->json(['error' => 'Không thể quay lại trạng thái "Đơn hàng mới" khi đơn hàng đang vận chuyển.'], 403);
        }
    } elseif ($order->status_id == 7) { // Nếu đơn hàng đã ở trạng thái "Đã giao hàng"
        return response()->json(['error' => 'Không thể thay đổi trạng thái của đơn hàng đã giao.'], 403);
    }

    // Tiến hành cập nhật trạng thái nếu hợp lệ
    $validated = $request->validate([
        'status_id' => 'required|integer|exists:order_statuses,status_id',
    ]);

    $order->status_id = $validated['status_id'];
    $order->save();

    return response()->json(['message' => 'Cập nhật trạng thái thành công.']);
}




}