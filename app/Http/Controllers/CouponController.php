<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\OrderCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    // Hiển thị danh sách mã giảm giá
    public function index()
    {
        $coupons = Coupon::all();
        foreach ($coupons as $coupon) {
            // Kiểm tra giá trị của expiration_date 
            $coupon->expiration_date = Carbon::parse($coupon->expiration_date)
                                            ->setTimezone('Asia/Ho_Chi_Minh');
        }
        
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
            'apply_to' => 'required|in:order,shipping',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric',
            'max_discount_value' => 'nullable|numeric',
            'expiration_date' => 'required|date',
            'usage_limit' => 'required|integer',
            'min_order_value' => 'nullable|numeric', 
            'status' => 'required|in:active,expired,disabled',
        ]);

        $data = $request->except('expiration_date');
        $data['expiration_date'] = \Carbon\Carbon::parse($request->expiration_date);
    
        Coupon::create($data);

        return redirect()->route('coupons.index')->with('success', 'Coupon created successfully');
    }

    // Hiển thị form sửa mã giảm giá
    public function edit($coupon_id)
    {
        $coupon = Coupon::findOrFail($coupon_id);
    
        // Chuyển đổi múi giờ của expiration_date trước khi trả về view
        $coupon->expiration_date = Carbon::parse($coupon->expiration_date)->setTimezone('Asia/Ho_Chi_Minh');
        return view('admin.coupons.edit', compact('coupon'));
    }

    // Cập nhật thông tin mã giảm giá
    public function update(Request $request, $coupon_id)
    {
        $request->validate([
            'code' => 'required|max:50|unique:coupons,code,' . $coupon_id . ',coupon_id', // 
            'apply_to' => 'required|in:order,shipping',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric',
            'max_discount_value' => 'nullable|numeric',
            'expiration_date' => 'required|date',
            'usage_limit' => 'required|integer',
            'status' => 'required|in:active,expired,disabled',
        ]);
    
        $coupon = Coupon::findOrFail($coupon_id);

        $data = $request->except('expiration_date');
        $data['expiration_date'] = \Carbon\Carbon::parse($request->expiration_date);
    
        $coupon->update($data);
    
        return redirect()->route('coupons.index')->with('success', 'Coupon updated successfully');
    }
    

    // Xóa mã giảm giá
    public function destroy($coupon_id)
    {
        $coupon = Coupon::findOrFail($coupon_id);
        $coupon->delete();

        return redirect()->route('coupons.index')->with('success', 'Coupon deleted successfully');
    }
    // app/Http/Controllers/CouponController.php
public function check(Request $request)
{
    $data = json_decode($request->getContent(), true);

    // Log dữ liệu nhận được
    Log::info('Dữ liệu nhận được từ frontend: ', $data);

    // Lấy các mã giảm giá từ request (chuỗi mã giảm giá, ví dụ: MGG-06,MGG-02)
    $codes = $data['codes'] ?? null;

    if (!$codes) {
        return response()->json(['valid_coupons' => [], 'message' => 'Không tìm thấy mã.']);
    }

    // Tách mã giảm giá cách nhau bởi dấu phẩy
    $couponCodes = explode(',', $codes);

    // Khởi tạo mảng để lưu thông tin các mã giảm giá hợp lệ
    $validCoupons = [];

    // Kiểm tra từng mã giảm giá
    foreach ($couponCodes as $code) {
        // Loại bỏ khoảng trắng trước và sau mã giảm giá
        $code = trim($code);

        // Tìm mã giảm giá trong cơ sở dữ liệu
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['valid_coupons' => [], 'message' => "Mã giảm giá '{$code}' không tồn tại."]);
        }

        if ($coupon->status !== 'active') {
            return response()->json(['valid_coupons' => [], 'message' => "Mã giảm giá '{$code}' không hoạt động."]);
        }

        if (now()->gt($coupon->expiration_date)) {
            return response()->json(['valid_coupons' => [], 'message' => "Mã giảm giá '{$code}' đã hết hạn."]);
        }

        if ($coupon->usage_count >= $coupon->usage_limit) {
            return response()->json(['valid_coupons' => [], 'message' => "Mã giảm giá '{$code}' đã hết lượt sử dụng."]);
        }

        // Kiểm tra giá trị đơn hàng
        $orderTotalRaw = $data['order_total'] ?? '0';
        $orderTotal = (int) str_replace('.', '', $orderTotalRaw);

        if ($coupon->min_order_value > $orderTotal) {
            return response()->json(['valid_coupons' => [], 'message' => "Giá trị đơn hàng chưa đủ để sử dụng mã giảm giá '{$code}'."]);
        }

        // Nếu mã giảm giá hợp lệ, thêm vào mảng validCoupons
        $validCoupons[] = [
            'coupon_id' => $coupon->coupon_id,
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'max_discount_value' => $coupon->max_discount_value,
            'apply_to' => $coupon->apply_to,
            'usage_limit' => $coupon->usage_limit,
            'usage_count' => $coupon->usage_count,
        ];
    }

    // Trả về các mã giảm giá hợp lệ
    return response()->json([
        'valid_coupons' => $validCoupons,
        'message' => 'Các mã giảm giá hợp lệ.'
    ]);
}

    
    
    
// app/Http/Controllers/CouponController.php
public function validateCoupons(Request $request)
{
    $codes = explode(',', $request->input('codes'));
    $codes = array_map('trim', $codes);

    $validCoupons = [];
    $discountTotal = 0;

    foreach ($codes as $code) {
        $coupon = Coupon::where('code', $code)
                        ->where('usage_limit', '>', 0)
                        ->where('expiration_date', '>=', now())
                        ->first();

        if ($coupon) {
            $validCoupons[] = $coupon;

            if ($coupon->discount_type == 'fixed') {
                $discountTotal += $coupon->discount_value; // Fixed discount
            } elseif ($coupon->discount_type == 'percentage') {
                $discountTotal += ($coupon->discount_value / 100); // Phần trăm giảm giá, cần tính trên giá trị tổng
            }
        }
    }

    return response()->json([
        'valid_coupons' => $validCoupons,
        'discount_total' => $discountTotal
    ]);
}



}
