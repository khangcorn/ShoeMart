<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCoupon;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'status']);

        if ($request->has('date_filter')) {
            $dateFilter = $request->date_filter;

            if ($dateFilter !== 'all') {
                $days = (int) $dateFilter;
                $query->where('created_at', '>=', now()->subDays($days));
            }
        } else {
            // Mặc định: 3 ngày gần đây (nếu muốn)
            $query->where('created_at', '>=', now()->subDays(3));
        }

        // Tìm kiếm theo mã đơn hàng
        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%'.$request->order_code.'%');
        }

        // Sắp xếp và phân trang (giữ query string khi chuyển trang)
        $orders = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

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

    public function cancel(Request $request, $order_id)
    {
        $order = Order::with(['orderDetails.variant', 'user.wallet'])->findOrFail($order_id);

        if (! auth()->user()->hasPermission('update_order_status')) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Bạn không có quyền hủy đơn hàng.');
        }

        if ($order->status_id != 1) {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng mới.');
        }
        $cancelReason = $request->input('cancel_reason');

        // Tính lại tổng tiền các biến thể còn active (chưa bị hủy)
        $subtotal = $order->orderDetails()
            ->where('status', 'active')
            ->sum(DB::raw('price * quantity'));

        $shippingFee = $order->shipping_fee ?? 0;
        $discount = $order->discount_amount ?? 0;

        $totalToRefund = max(0, $subtotal + $shippingFee - $discount);

        // Cộng lại số lượng tồn kho cho từng sản phẩm trong đơn
        foreach ($order->orderDetails as $detail) {
            if ($detail->status !== 'cancelled') {  // Chỉ cộng tồn kho nếu biến thể chưa bị hủy
                if ($detail->variant_id) {
                    $variant = \App\Models\ProductVariant::find($detail->variant_id);
                    if ($variant) {
                        $variant->increment('stock', $detail->quantity);
                    }
                } else {
                    $product = \App\Models\Product::find($detail->product_id);
                    if ($product) {
                        $product->increment('stock', $detail->quantity);
                    }
                }
            }
        }

        // Cập nhật trạng thái hủy cho từng biến thể nếu chưa hủy
        foreach ($order->orderDetails as $detail) {
            if ($detail->status != 'cancelled') {
                $detail->status = 'cancelled';
                $detail->save();
            }
        }

        // Tăng usage_count các coupon
        $orderCoupons = OrderCoupon::where('order_id', $order_id)->get();
        foreach ($orderCoupons as $orderCoupon) {
            $coupon = Coupon::find($orderCoupon->coupon_id);
            if ($coupon) {
                $coupon->decrement('usage_count');
            }
        }

        if ($order->payment_method != 'cod') {
            $wallet = $order->user->wallet;
            $wallet->balance += $totalToRefund;
            $wallet->save();

            \App\Models\WalletTransaction::create([
                'wallet_id' => $wallet->wallet_id,
                'amount' => $totalToRefund,
                'type' => 'refund',
                'description' => 'Hoàn tiền do shop hủy đơn hàng #'.$order->order_code,
                'status' => 'completed',
            ]);
        }

        // Cập nhật trạng thái đơn hàng thành "Đã hủy"
        $order->status_id = 5;
        $order->total = 0; // Có thể reset lại tổng tiền đơn
        $order->cancel_reason = $cancelReason; // ✅ Lưu lý do
        $order->save();

        return back()->with('success', 'Đã hủy đơn hàng, hoàn tiền và cập nhật tồn kho.');
    }

    public function cancelOrderDetail($orderId, $detailId,Request $request)
    {
            if (! auth()->user()->hasPermission('update_order_status')) {
        return response()->json(['error' => 'Bạn không có quyền hủy đơn hàng.'], 403);
    }
    try {

            // Lấy detail cần hủy
            $detail = OrderDetail::where('order_id', $orderId)
                ->where('order_detail_id', $detailId)
                ->firstOrFail();

            $order = Order::with(['orderDetails.variant', 'user.wallet'])->findOrFail($orderId);

            if ($detail->status === 'cancelled') {
                return response()->json(['message' => 'Sản phẩm đã bị hủy trước đó.'], 400);
            }
            $cancelReason = $request->input('cancel_reason'); // ✅ Lấy lý do từ form

            $shippingFee = $order->shipping_fee ?? 0;
            $discount = $order->discount_amount ?? 0;

            // --- Sửa đây: tính tổng tiền đơn trước khi hủy biến thể cuối cùng ---
            $totalBeforeCancel = max(0,
                $order->orderDetails()
                    ->where('status', 'active')
                    ->sum(DB::raw('price * quantity'))
                + $shippingFee
                - $discount
            );

            // Cập nhật trạng thái hủy cho biến thể
            $detail->status = 'cancelled';
            $detail->save();

            // Cộng lại số lượng tồn kho từng sản phẩm bị hủy
            if ($detail->variant_id) {
                $variant = \App\Models\ProductVariant::find($detail->variant_id);
                if ($variant) {
                    $variant->increment('stock', $detail->quantity);
                }
            } else {
                $product = \App\Models\Product::find($detail->product_id);
                if ($product) {
                    $product->increment('stock', $detail->quantity);
                }
            }

            // Kiểm tra còn biến thể nào active không
            $remainingItemsCount = $order->orderDetails()->where('status', 'active')->count();
            $isLastCancelled = $remainingItemsCount === 0;

            // Tính subtotal các sản phẩm còn active
            $subtotal = $order->orderDetails()
                ->where('status', 'active')
                ->sum(DB::raw('price * quantity'));

            // Cập nhật tổng tiền đơn hàng = subtotal + phí ship - giảm giá
            $newTotal = max(0, $subtotal + $shippingFee - $discount);

            // Nếu hủy toàn bộ, cập nhật trạng thái đơn và usage_count coupon
            if ($isLastCancelled) {
                if ($order->status_id != 5) {
                    $order->status_id = 5; // Đã hủy bởi shop
                }

                $orderCoupons = OrderCoupon::where('order_id', $orderId)->get();
                foreach ($orderCoupons as $orderCoupon) {
                    $coupon = Coupon::find($orderCoupon->coupon_id);
                    if ($coupon) {
                        $coupon->decrement('usage_count');
                    }
                }

                $newTotal = 0;
            }

            $order->total = $newTotal;
            $order->save();

            // Hoàn tiền nếu thanh toán bằng ví
            if ($order->payment_method != 'cod') {
                $wallet = $order->user->wallet;

                if ($isLastCancelled) {
                    // Hoàn tiền đúng tổng tiền đơn hàng trước khi hủy biến thể cuối cùng
                    $refundAmount = $totalBeforeCancel;
                } else {
                    // Hoàn tiền đúng số tiền biến thể bị hủy
                    $refundAmount = $detail->price * $detail->quantity;
                }

                // Cộng tiền vào ví
                $wallet->balance += $refundAmount;
                $wallet->save();

                // Ghi log hoàn tiền
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->wallet_id,
                    'amount' => $refundAmount,
                    'type' => 'refund',
                    'description' => $isLastCancelled
                        ? 'Hoàn tiền toàn bộ đơn hàng #'.$order->order_code
                        : 'Hoàn tiền sản phẩm bị hủy trong đơn hàng #'.$order->order_code,
                    'status' => 'completed',
                ]);
            }

            // Tính tổng tiền trả về client
            $totalAfterRefund = $order->total;
            $detail->cancel_reason = $cancelReason;
            $detail->save();
            return response()->json([
                'message' => 'Hủy sản phẩm thành công.',
                'total' => $totalAfterRefund,
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi hủy sản phẩm đơn hàng: '.$e->getMessage());

            return response()->json(['error' => 'Lỗi máy chủ khi hủy sản phẩm.'], 500);
        }
    }

    public function ajaxUpdateStatus(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        if (! $order) {
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
            if (! in_array($newStatusId, [2, 7])) { // Chỉ cho phép chuyển sang trạng thái "Đang vận chuyển" (2) hoặc "Đã giao hàng" (7)
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
