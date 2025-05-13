<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAddresses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem địa chỉ.');
        }

        $addresses = $user->userAddresses;
        return view('address.index', compact('addresses'));
    }
    public function setDefault($address_id)
    {
        DB::beginTransaction();  // Bắt đầu transaction
    
        try {
            // Tìm địa chỉ theo ID, nếu không có sẽ trả về lỗi 404
            $address = UserAddresses::findOrFail($address_id);
    
            // Kiểm tra xem địa chỉ có phải của người dùng hiện tại không
            if ($address->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền thay đổi địa chỉ này.'], 403);
            }
    
            // Đặt tất cả địa chỉ khác của người dùng thành không mặc định
            $updateOtherAddresses = UserAddresses::where('user_id', Auth::id())
                ->where('address_id', '!=', $address_id) // Đảm bảo không thay đổi địa chỉ hiện tại
                ->update(['is_default' => 0]);
    
            // Log thông tin các địa chỉ khác đã bị thay đổi
            Log::info("Cập nhật các địa chỉ khác thành không mặc định: " . $updateOtherAddresses);
    
            // Đặt địa chỉ hiện tại thành mặc định
            $address->is_default = 1;  // Đặt là 1 thay vì true
            $saved = $address->save(); // Lưu lại thay đổi
    
            // Log kết quả của việc lưu
            Log::info("Kết quả lưu địa chỉ: " . ($saved ? "Thành công" : "Thất bại"));
    
            // Nếu save() không thành công, bạn có thể kiểm tra thêm lỗi
            if (!$saved) {
                $errors = $address->getErrors(); // Nếu có lỗi validate sẽ trả về
                Log::error('Lỗi khi lưu địa chỉ: ' . json_encode($errors));
            }
    
            // Kiểm tra nếu lưu thành công
            if ($saved) {
                // Commit giao dịch nếu thành công
                DB::commit();
    
                // Lấy lại dữ liệu địa chỉ mặc định từ cơ sở dữ liệu
                $address->refresh(); // Làm mới đối tượng sau khi cập nhật
                Log::info("Địa chỉ mặc định hiện tại sau khi làm mới: ", [$address]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Địa chỉ đã được chọn làm mặc định.',
                    'address' => $address
                ]);
            } else {
                // Rollback nếu có lỗi khi lưu
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Không thể cập nhật địa chỉ.'], 500);
            }
    
        } catch (\Exception $e) {
            // Rollback nếu có lỗi bất kỳ
            DB::rollBack();
    
            // Log lỗi
            Log::error("Lỗi khi cập nhật địa chỉ: " . $e->getMessage());
            
            return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra.'], 500);
        }
    }
    
    
    
    

    public function create()
    {
        return view('address.create');
    }

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi validate.',
                'errors'  => $e->errors()
            ], 422);
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
    
        try {
            $request->validate([
                'recipient_name'  => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:15',
                'street_address'  => 'required|string|max:255',
                'ward'            => 'required|string|max:255',
                'district'        => 'required|string|max:255',
                'city'            => 'required|string|max:255',
                'is_default'      => 'nullable|boolean',
            ]);
    
            // Nếu is_default là true, reset các địa chỉ khác
            if ($request->has('is_default') && $request->is_default) {
                UserAddresses::where('user_id', Auth::id())->update(['is_default' => 0]);
                $address->is_default = 1;
            }
    
            // Cập nhật các trường khác (trừ is_default)
            $address->update($request->except('is_default'));
    
            // Lưu nếu is_default được cập nhật bên trên
            if ($request->has('is_default') && $request->is_default) {
                $address->save();
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật địa chỉ thành công.',
                'newAddress' => $address,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi validate.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi cập nhật.',
            ], 500);
        }
    }
    

    public function destroy($address_id)
    {
        $address = UserAddresses::find($address_id);

        if ($address && $address->user_id == Auth::id()) {
            if ($address->is_default) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa địa chỉ mặc định.']);
            }

            $address->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Địa chỉ không tồn tại hoặc bạn không có quyền xóa.']);
    }
}
