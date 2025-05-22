<?php

// app/Http/Controllers/BankAccountController.php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function showForm()
    {
        return view('wallet.link-bank');
    }

    public function store(Request $request)
    {
        // Validate thông tin liên kết ngân hàng
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder_name' => 'required|string',
        ]);

        // Tạo liên kết ngân hàng cho người dùng
        $bankAccount = new BankAccount;
        $bankAccount->user_id = Auth::id();
        $bankAccount->bank_name = $request->bank_name;
        $bankAccount->account_number = $request->account_number;
        $bankAccount->account_holder_name = $request->account_holder_name;
        $bankAccount->save();

        return redirect()->route('wallet.index')->with('success', 'Liên kết ngân hàng thành công!');
    }
}
