<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAddresses;

class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Lấy người dùng hiện tại

        // Kiểm tra người dùng đã đăng nhập hay chưa
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem địa chỉ.');
        }

        // Lấy tất cả địa chỉ của người dùng
        $addresses = $user->userAddresses; // Lấy tất cả địa chỉ từ quan hệ userAddresses

        return view('address.index', compact('addresses')); // Trả về view với dữ liệu địa chỉ
    }
    public function setDefault($address_id)
    {
        $address = UserAddresses::findOrFail($address_id);
    
        if ($address->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thay đổi địa chỉ này.'], 403);
        }
    
        // Đặt tất cả địa chỉ khác thành không mặc định
        UserAddresses::where('user_id', Auth::id())->update(['is_default' => false]);
    
        // Đặt địa chỉ được chọn làm mặc định
        $address->update(['is_default' => true]);
    
        return response()->json(['success' => true, 'message' => 'Địa chỉ đã được chọn làm mặc định.']);
    }
    

    // Hiển thị form tạo địa chỉ mới
    public function create()
    {
        return view('address.create');
    }

    // Xử lý lưu địa chỉ mới
    public function store(Request $request)
    {
        try {
            $request->validate([
                'address_name'    => 'nullable|string|max:255',
                'recipient_name'  => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:15',
                'city'            => 'required|string|max:100',
                'district'        => 'required|string|max:100',
                'ward'            => 'required|string|max:100',
                'street_address'  => 'required|string|max:255',
                'is_default'      => 'nullable|boolean',
            ]);
    
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng chưa đăng nhập'
                ], 401);
            }
    
            if ($request->is_default) {
                UserAddresses::where('user_id', $user->user_id)->update(['is_default' => 0]);
            }
    
            $newAddress = UserAddresses::create([
                'user_id'         => $user->user_id,
                'address_name'    => $request->address_name,
                'recipient_name'  => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'city'            => $request->city,
                'district'        => $request->district,
                'ward'            => $request->ward,
                'street_address'  => $request->street_address,
                'is_default'      => $request->has('is_default') ? 1 : 0,
            ]);
    
            return response()->json([
                'success'    => true,
                'message'    => 'Đã thêm địa chỉ thành công!',
                'newAddress' => $newAddress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function edit($address_id)
    {
        $address = UserAddresses::findOrFail($address_id);

        // Kiểm tra xem người dùng có quyền sửa địa chỉ này không
        if ($address->user_id !== Auth::id()) {
            return redirect()->route('cart.checkout')->with('error', 'Bạn không có quyền sửa địa chỉ này.');
        }

        return view('address.edit', compact('address'));
    }

    public function update(Request $request, $address_id)
    {
        $address = UserAddresses::findOrFail($address_id);
    
        if ($address->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền sửa địa chỉ này.'], 403);
        }
    
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'ward'           => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'city'           => 'required|string|max:255',
        ]);
    
        $address->update($request->all());
    
        return response()->json(['success' => true, 'message' => 'Cập nhật địa chỉ thành công.']);
    }
    

public function destroy($address_id)
{
    $address = UserAddresses::find($address_id);

    if ($address && $address->user_id == Auth::id()) {
        // Kiểm tra xem địa chỉ có phải là mặc định không
        if ($address->is_default) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa địa chỉ mặc định.']);
        }

        $address->delete();
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Địa chỉ không tồn tại hoặc bạn không có quyền xóa.']);
}

    
}
