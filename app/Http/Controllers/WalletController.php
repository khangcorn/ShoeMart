<?php

namespace App\Http\Controllers;

use App\Models\UserBank;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // Xem số dư và lịch sử ví
    public function index()
    {
        $wallet = Wallet::where('user_id', Auth::id())->with('transactions')->first();
        return response()->json($wallet);
    }

    // Nạp tiền vào ví
   // WalletController.php

public function deposit(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:10000|max:100000000',
        'bank_id' => 'required|exists:user_banks,id',
    ],[
        'amount.required' => 'Bạn chưa nhập số tiền.',
        'amount.numeric' => 'Số tiền là một số.',
        'amount.min' => 'Số tiền nạp ít nhất là 10000.',
        'amount.max' => 'Số tiền nạp nhiều nhất là 100000000.',
    ]);

    // Lấy thông tin ngân hàng từ bảng user_banks
    $userBank = UserBank::find($request->bank_id);

    if (!$userBank) {
        return response()->json(['message' => 'Ngân hàng không được liên kết với tài khoản'], 400);
    }

    DB::beginTransaction();
    try {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['balance' => 0]
        );

        // Tạo giao dịch
        $transaction = WalletTransaction::create([
            'wallet_id' => $wallet->wallet_id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'description' => 'Nạp tiền từ tài khoản ngân hàng: ' . $userBank->bank_name . ' - ' . $userBank->account_number,
            'status' => 'completed',
            'bank_account' => $userBank->account_number,
        ]);

        // Cộng tiền vào ví
        $wallet->increment('balance', $request->amount);

        DB::commit();
        return back()->with('success', 'Nạp tiền thành công! Số dư hiện tại: ' . number_format($wallet->balance, 0, ',', '.') . ' ₫');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Lỗi nạp tiền: ' . $e->getMessage());
    }
}


    // Rút tiền khỏi ví
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:100000000',
            'bank_id' => 'required|exists:user_banks,id', // Kiểm tra rằng tài khoản ngân hàng tồn tại
        ],[
            'amount.required' => 'Bạn chưa nhập số tiền.',
            'amount.numeric' => 'Số tiền là một số.',
            'amount.min' => 'Số tiền rút ít nhất là 10000.',
            'amount.max' => 'Số tiền rút nhiều nhất là 100000000.',
        ]);
    
        $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
    
        if ($wallet->balance < $request->amount) {
            return back()->withErrors(['amount' => 'Số dư không đủ để rút tiền'])->withInput();
        }
    
        $userBank = UserBank::findOrFail($request->bank_id); // Lấy thông tin ngân hàng liên kết
    
        DB::beginTransaction();
        try {
            // Trừ tiền trong ví
            $wallet->decrement('balance', $request->amount);
    
            // Tạo giao dịch rút tiền
            WalletTransaction::create([
                'wallet_id' => $wallet->wallet_id,
                'type' => 'withdraw',
                'amount' => $request->amount,
                'description' => 'Rút tiền về tài khoản: ' . $userBank->bank_name . ' - ' . $userBank->account_number,
                'status' => 'pending', // Admin xác nhận sau
                'bank_account' => $userBank->account_number,
            ]);
    
            DB::commit();
            return back()->with('success', 'Rút tiền thành công! Số dư hiện tại: ' . number_format($wallet->balance, 0, ',', '.') . ' ₫');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi nạp tiền: ' . $e->getMessage());
        }
    }
    

    // Liên kết ngân hàng
    public function linkBank(Request $request)
    {
        $request->validate([
            'bank' => 'required',
            'account_number' => 'required|numeric|regex:/^\d{10,20}$/',  // Chấp nhận chuỗi và chỉ cho phép 10-20 chữ số
        ], [
            'account_number.required' => 'Số tài khoản là bắt buộc.',
            'account_number.numeric' => 'Số tài khoản phải là một số.',
            'account_number.regex' => 'Số tài khoản phải có từ 10 đến 20 chữ số và không chứa ký tự khác.',
        ]);
    
        // Kiểm tra xem ngân hàng đã được liên kết với số tài khoản này chưa
        $existingBank = UserBank::where('user_id', auth()->user()->user_id)
                                ->where('bank_name', $request->bank)
                                ->first();
    
        if ($existingBank) {
            return back()->withErrors(['bank' => 'Ngân hàng này đã được liên kết.']);
        }
    
        // Lưu thông tin ngân hàng vào bảng user_banks
        $userBank = new UserBank([
            'user_id' => auth()->user()->user_id,  // Sử dụng id của người dùng
            'bank_name' => $request->bank,
            'account_number' => $request->account_number,
        ]);
        $userBank->save();
    
        return back()->with('success', 'Ngân hàng đã được liên kết thành công!');
    }
    public function unlinkBank(UserBank $bank)
{
    // Kiểm tra xem người dùng có quyền xóa ngân hàng này không
    if ($bank->user_id !== auth()->user()->user_id) {
        return back()->withErrors(['error' => 'Không thể xóa ngân hàng không phải của bạn.']);
    }

    // Xóa ngân hàng
    $bank->delete();

    return back()->with('success', 'Ngân hàng đã được hủy liên kết thành công!');
}

    
}
