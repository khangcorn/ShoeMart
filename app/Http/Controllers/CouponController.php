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
    public function show(string $id)
    {
        // Nếu bạn muốn sử dụng id trong phương thức show, cần đảm bảo là bạn truy vấn bằng id
        $coupon = Coupon::findOrFail($id);
        return view('coupon.show-cp', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Coupon $coupon)
    // {
    //     return view('coupon.edit-cp',compact('coupon'));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    public function update(Request $request, Coupon $coupon)
{
    $coupon->update($request->all());
    return redirect()->route('coupon.index')->with('thongbao', 'Cập nhật coupon thành công!');
}
public function edit($id)
{
    // Lấy coupon bằng id
    $coupon = Coupon::findOrFail($id);  // Sử dụng id thay vì id
    
    // Trả về view để chỉnh sửa coupon
    return view('coupon.edit-cp', compact('coupon'));  // Thay đổi tên view nếu cần
}


/**
 * Remove the specified resource from storage.
 */
public function destroy(Coupon $coupon)
{
    $coupon->delete();
    return redirect()->route('coupon.index')->with('thongbao', 'Xóa coupon thành công!');
}

}
