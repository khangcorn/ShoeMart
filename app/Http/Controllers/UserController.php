<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserAddresses;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    public function showRegisterForm()
    {
        return view('client.auth.register');
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    // Xử lý đăng ký
    public function register(Request $request)
{

    // Validate input data
    $validator = Validator::make($request->all(), [
        'username' => 'required|max:255',
        'password' => 'required|string|min:6',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password_confirmation' => 'required|string|min:6',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ]);
  
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Create user
    $user = User::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
    ]);

    return redirect()->route('login')->with('success', 'User registered successfully');
}

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('profile');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    // Xử lý đăng xuất
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.form')->with('success', 'Đã đăng xuất.');
    }

    // Hiển thị trang hồ sơ
    public function profile()
    {
        $user = Auth::user();
        $address = UserAddresses::where('user_id', $user->user_id)->first();
        return view('client.auth.profile', compact('user', 'address'));
    }

    // Cập nhật địa chỉ
    public function updateAddress(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'street_address' => 'required|string|max:255',
        ]);

        // Lấy dữ liệu từ API
        $jsonData = file_get_contents("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json");
        $location = json_decode($jsonData, true);

        // Tìm thành phố
        $city = collect($location)->firstWhere('Id', $request->city);

        if (!$city) {
            return back()->withErrors(['city' => 'Thành phố không tồn tại!']);
        }

        // Tìm quận huyện trong thành phố
        $district = collect($city['Districts'])->firstWhere('Id', $request->district);

        if (!$district) {
            return back()->withErrors(['district' => 'Quận huyện không tồn tại!']);
        }

        // Tìm phường xã trong quận huyện
        $ward = collect($district['Wards'])->firstWhere('Id', $request->ward);

        if (!$ward) {
            return back()->withErrors(['ward' => 'Phường xã không tồn tại!']);
        }

        UserAddresses::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'city' => $city['Name'],
                'district' => $district['Name'],
                'ward' => $ward['Name'],
                'street_address' => $request->street_address,
                'is_default' => true,
            ]
        );

        return back()->with('success', 'Cập nhật địa chỉ thành công.');
    }
   
}
