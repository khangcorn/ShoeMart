<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundRequestController extends Controller
{
    public function index()
    {
        $refundRequests = RefundRequest::with(['user', 'order'])->latest()->get();
        return view('admin.refunds.index', compact('refundRequests'));
    }

    public function approve($id)
    {
        $refund = RefundRequest::with('user', 'order.orderDetails')->findOrFail($id); // load orderDetails qua order
    
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý.');
        }
    
        DB::transaction(function () use ($refund) {
            $refund->status = 'approved';
            $refund->approved_by = auth()->id();
            $refund->approved_at = now();
            $refund->save();
    
            $order = $refund->order;
            $order->status_id = 8;
            $order->save();
    
            // ✅ Cộng lại số lượng sản phẩm
            foreach ($order->orderDetails as $detail) {
                $variant = ProductVariant::find($detail->variant_id);
                if ($variant) {
                    $variant->stock += $detail->quantity;
                    $variant->save();
                }
            }
    
            // Ghi lịch sử và cộng tiền ví
            $wallet = $refund->user->wallet;
    
            \App\Models\WalletTransaction::create([
                'wallet_id' => $wallet->wallet_id,
                'amount' => $order->total,
                'type' => 'refund',
                'description' => 'Hoàn tiền cho đơn hàng #' . $order->order_code,
                'status' => 'completed',
            ]);
    
            $wallet->balance += $refund->amount;
            $wallet->save();
        });
    
        return back()->with('success', 'Đã duyệt hoàn tiền thành công và cập nhật kho.');
    }
    
    public function reject($id)
    {
        $refund = RefundRequest::findOrFail($id);

        if ($refund->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý.');
        }

        $refund->status = 'rejected';
        $refund->approved_by = auth()->id();
        $refund->approved_at = now(); // Cập nhật thời gian duyệt
        $refund->save();

        return back()->with('success', 'Đã từ chối yêu cầu hoàn tiền.');
    }
}
