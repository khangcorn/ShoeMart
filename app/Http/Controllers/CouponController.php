<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\OrderCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    // Hiển thị danh sách mã giảm giá
    public function index()
    {
        $coupons = Coupon::all();
        return view('admin.coupons.index', compact('coupons'));
    }

    // Hiển thị form tạo mới mã giảm giá
    public function create()
    {
        return view('admin.coupons.create');
    }

    // Lưu mã giảm giá mới vào cơ sở dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code|max:50',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric',
            'max_discount_value' => 'nullable|numeric',
            'expiration_date' => 'required|date',
            'usage_limit' => 'required|integer',
            'status' => 'required|in:active,expired,disabled',
        ]);

        Coupon::create($request->all());

        return redirect()->route('coupons.index')->with('success', 'Coupon created successfully');
    }

    // Hiển thị form sửa mã giảm giá
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    // Cập nhật thông tin mã giảm giá
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|max:50|unique:coupons,code,' . $id,
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric',
            'max_discount_value' => 'nullable|numeric',
            'expiration_date' => 'required|date',
            'usage_limit' => 'required|integer',
            'status' => 'required|in:active,expired,disabled',
        ]);

        $coupon = Coupon::findOrFail($id);
        $coupon->update($request->all());

        return redirect()->route('coupons.index')->with('success', 'Coupon updated successfully');
    }

    // Xóa mã giảm giá
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('coupons.index')->with('success', 'Coupon deleted successfully');
    }
    // app/Http/Controllers/CouponController.php
    public function check(Request $request)
    {
        $code = $request->input('code');
    
        $coupon = Coupon::where('code', $code)->where('usage_limit', '>', 0)->first();
    
        if ($coupon) {
            return response()->json([
                'valid' => true,
                'discount_amount' => $coupon->discount_amount,
            ]);
        }
    
        return response()->json(['valid' => false]);
    }
    

}
