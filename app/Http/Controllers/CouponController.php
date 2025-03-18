<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupon = Coupon::paginate();
        return view('coupon.index-cp', compact('coupon'))->with('i', (request()->input('page', 1) - 1));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coupon.create-cp');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Coupon::create($request->all());
        return redirect()->route('coupon.index')->with('thongbao','Thêm mới coupon thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $coupon_id) // Thay đổi tham số từ 'id' thành 'coupon_id'
    {
        // Truy vấn bằng coupon_id thay vì id
        $coupon = Coupon::findOrFail($coupon_id);  // Dùng coupon_id thay vì id
        return view('coupon.show-cp', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($coupon_id)  // Thay đổi từ 'id' thành 'coupon_id'
    {
        // Truy vấn bằng coupon_id
        $coupon = Coupon::findOrFail($coupon_id);  // Dùng coupon_id thay vì id
        return view('coupon.edit-cp', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $coupon_id)  // Thay đổi từ 'coupon' thành 'coupon_id'
    {
        // Truy vấn coupon bằng coupon_id
        $coupon = Coupon::findOrFail($coupon_id);  // Dùng coupon_id thay vì id
        $coupon->update($request->all());
        return redirect()->route('coupon.index')->with('thongbao', 'Cập nhật coupon thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($coupon_id)  // Thay đổi từ 'coupon' thành 'coupon_id'
    {
        // Truy vấn coupon bằng coupon_id
        $coupon = Coupon::findOrFail($coupon_id);  // Dùng coupon_id thay vì id
        $coupon->delete();
        return redirect()->route('coupon.index')->with('thongbao', 'Xóa coupon thành công!');
    }
}
