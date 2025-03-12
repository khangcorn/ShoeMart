<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function showCart(Request $request)
    {
        // Lấy danh sách tất cả người dùng có giỏ hàng
        $users = User::has('cart')->get();

        // Kiểm tra nếu có user_id được chọn, lấy giỏ hàng tương ứng
        $selectedUserId = $request->input('user_id', $users->first()->user_id ?? null);
        $cart = Cart::where('user_id', operator: $selectedUserId)->with('details.product')->first();

        return view('cart.index', compact('users', 'cart', 'selectedUserId'));
    }
}
