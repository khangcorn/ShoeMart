<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserBank;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use App\Notifications\WithdrawApprovedNotification;
use App\Notifications\WithdrawRejectedNotification;
use App\Notifications\WithdrawRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawRequestController extends Controller
{
    public function index()
    {
        $withdrawRequests = WithdrawRequest::with('user')->latest()->paginate(20);
        return view('admin.withdraw.index', compact('withdrawRequests'));
    }

    public function update(Request $request, $id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
    
        // Kiểm tra trạng thái của yêu cầu
        if ($withdraw->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý.');
        }
    
        // Lấy ví của người dùng
        $wallet = Wallet::where('user_id', $withdraw->user_id)->firstOrFail();
        $userBank = $withdraw->userBank;
    
        // Mô tả giao dịch rút tiền
        $description = $userBank 
            ? 'Rút tiền về tài khoản: ' . $userBank->bank_name . ' - ' . $userBank->account_number
            : 'Rút tiền về tài khoản không xác định';
    
        // Tìm transaction liên quan
        $transaction = WalletTransaction::where('wallet_id', $wallet->wallet_id)
            ->where('type', 'withdraw')
            ->where('amount', $withdraw->amount)
            ->where('status', 'pending')
            ->latest()
            ->first();
    
        if (!$transaction) {
            return back()->with('error', 'Không tìm thấy giao dịch rút tiền tương ứng.');
        }
    
    
        // Kiểm tra yêu cầu chấp nhận (approve)
        if ($request->input('action') === 'approve') {
            // Kiểm tra số dư ví
            if ($wallet->balance < $withdraw->amount) {
                // Nếu số dư ví không đủ, trả về thông báo lỗi
                return back()->with('error', 'Số dư ví không đủ để thực hiện giao dịch rút tiền.');
            }
    
            // Trừ tiền trong ví
            $wallet->decrement('balance', $withdraw->amount);
            $withdraw->status = 'approved';
            $withdraw->save();
    
            // Cập nhật giao dịch
            $transaction->status = 'completed';
            $transaction->description = $description;
            $transaction->save();
    
            // Gửi thông báo mới cho người dùng
            $withdraw->user->notify(new WithdrawRequestNotification('Yêu cầu rút tiền đã được phê duyệt.'));
    
        // Kiểm tra yêu cầu từ chối (reject)
        } elseif ($request->input('action') === 'reject') {
            // Hoàn lại tiền nếu đã trừ trước đó khi tạo yêu cầu
            $withdraw->status = 'rejected';
            $withdraw->save();
    
            // Cập nhật giao dịch
            $transaction->status = 'rejected';
            $transaction->description = $description;
            $transaction->save();
    
            // Gửi thông báo mới cho người dùng
            $withdraw->user->notify(new WithdrawRequestNotification('Yêu cầu rút tiền đã bị từ chối.'));
        }
    
        return back()->with('success', 'Xử lý yêu cầu thành công.');
    }
    
    
    
    public function store(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1000',
        'user_bank_id' => 'required|exists:user_banks,id',
        'note' => 'nullable|string|max:255',
    ]);

    $user = auth()->user();
    $wallet = $user->wallet;

    if ($wallet->balance < $request->amount) {
        return back()->with('error', 'Số dư không đủ để thực hiện yêu cầu rút tiền.');
    }

    $withdraw = WithdrawRequest::create([
        'user_id' => $user->user_id,
        'amount' => $request->amount,
        'status' => 'pending',
        'note' => $request->note,
        'user_bank_id' => $request->user_bank_id,
    ]);

    // Tạo bản ghi trong wallet_transactions để hiển thị trong lịch sử
    $userBank = UserBank::find($request->user_bank_id);

    WalletTransaction::create([
        'wallet_id' => $wallet->wallet_id,
        'type' => 'withdraw',
        'amount' => $request->amount,
        'description' => 'Yêu cầu rút tiền về tài khoản: ' . $userBank->bank_name . ' - ' . $userBank->account_number,
        'status' => 'pending',
        'bank_account' => $userBank->account_number,
    ]);

    return back()->with('success', 'Yêu cầu rút tiền đã được gửi thành công.');
}

    
}

