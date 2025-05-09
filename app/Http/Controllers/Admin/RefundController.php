<?php

namespace App\Http\Controllers\Admin;

use App\Models\Refund;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu hoàn tiền
     */
    public function index()
    {
        // Lấy tất cả yêu cầu hoàn tiền và nạp quan hệ order, user, approvedBy (nếu cần)
        $refunds = Refund::with(['order', 'user', 'approvedBy'])->get();
    
        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Duyệt yêu cầu hoàn tiền
     */
    public function approve($refundId)
    {
        $refund = Refund::findOrFail($refundId);

        // Kiểm tra trạng thái của yêu cầu hoàn tiền
        if ($refund->status != 'pending') {
            return back()->with('error', 'Yêu cầu hoàn tiền không hợp lệ hoặc đã được xử lý.');
        }

        // Duyệt yêu cầu hoàn tiền
        $refund->status = 'approved';
        $refund->approved_at = now();
        $refund->approved_by = auth()->id(); // Lưu id admin duyệt

        // Cập nhật lại trạng thái đơn hàng nếu cần
        $refund->order->status_id = 7; // Giả sử trạng thái "Đã hoàn tiền" là 7
        $refund->order->save();

        $refund->save();

        // Redirect lại trang danh sách và hiển thị thông báo thành công
        return redirect()->route('refunds.index')->with('success', 'Yêu cầu hoàn tiền đã được duyệt.');
    }

    /**
     * Từ chối yêu cầu hoàn tiền
     */
    public function reject($refundId, Request $request)
    {
        $refund = Refund::findOrFail($refundId);
    
        if ($refund->status != 'pending') {
            return back()->with('error', 'Yêu cầu hoàn tiền không hợp lệ hoặc đã được xử lý.');
        }
    
        $refund->status = 'rejected';
        $refund->note = $request->input('note'); // Ghi chú lý do từ chối
        $refund->approved_at = now();
        $refund->approved_by = auth()->id();
    
        $refund->order->status_id = 8; // Trạng thái bị từ chối
        $refund->order->save();
    
        $refund->save();
    
        return redirect()->route('refunds.index')->with('success', 'Yêu cầu hoàn tiền đã bị từ chối.');
    }
    
}
