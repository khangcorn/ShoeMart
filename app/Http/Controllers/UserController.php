<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\User;
use App\Models\UserAddresses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function showRegisterForm()
    {
        return view('client.auth.register');
    }

    // Hiển thị form đăng nhập
public function showLoginForm(Request $request)
{
    if ($request->has('redirect')) {
        session()->put('_intended', $request->input('redirect'));
        Log::info('Lưu redirect param vào session cart_intended: ' . $request->input('redirect'));
    }

    return view('client.auth.login');
}


protected function authenticated(Request $request, $user)
{

    return redirect()->route('profile');
}


    // Xử lý đăng ký
    public function register(Request $request)
    {

        // Validate input data
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:255|unique:users,username',
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

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {

        $now = now()->timestamp;
        $redirectTo = null;

        $cartUrl = session('cart.intended');
        $cartTime = session('cart.intended_time');

        $favUrl = session('favorite.intended');
        $favTime = session('favorite.intended_time');

        $wishlistUrl = session('wishlist.intended');
        $wishlistTime = session('wishlist.intended_time');

        $checkoutUrl = session('checkout.intended');
        $checkoutTime = session('checkout.intended_time');

        Log::info('Session trước khi xử lý redirect:', [
            'cart.intended' => $cartUrl,
            'cart.intended_time' => $cartTime,
            'favorite.intended' => $favUrl,
            'favorite.intended_time' => $favTime,
            'wishlist.intended' => $wishlistUrl,
            'wishlist.intended_time' => $wishlistTime,
            'checkout.intended' => $checkoutUrl,
            'checkout.intended_time' => $checkoutTime,
        ]);

        // Mảng chứa url và thời gian tương ứng nếu tồn tại
        $urls = [];

        if ($cartUrl && $cartTime && ($now - $cartTime) <= 300) {
            $urls['cart'] = ['url' => $cartUrl, 'time' => $cartTime];
        }
        if ($favUrl && $favTime && ($now - $favTime) <= 300) {
            $urls['favorite'] = ['url' => $favUrl, 'time' => $favTime];
        }
        if ($wishlistUrl && $wishlistTime && ($now - $wishlistTime) <= 300) {
            $urls['wishlist'] = ['url' => $wishlistUrl, 'time' => $wishlistTime];
        }
        if ($checkoutUrl && $checkoutTime && ($now - $checkoutTime) <= 300) {
            $urls['checkout'] = ['url' => $checkoutUrl, 'time' => $checkoutTime];
        }

        if (!empty($urls)) {
            // Lấy phần tử có thời gian lớn nhất (gần nhất)
            $latest = collect($urls)->sortByDesc('time')->first();
            $redirectTo = $latest['url'];

            // Xóa hết các session liên quan
            session()->forget([
                'cart.intended', 'cart.intended_time',
                'favorite.intended', 'favorite.intended_time',
                'wishlist.intended', 'wishlist.intended_time',
                'checkout.intended', 'checkout.intended_time',
                '_intended'
            ]);

            Log::info('Redirect chọn URL gần nhất, đã xóa session redirect.');
        } else {
            // Fallback về _intended hoặc profile
            $redirectTo = session()->pull('_intended', route('profile'));
            Log::info('Redirect dùng fallback _intended hoặc profile.');
        }

        Log::info('Redirect sau login về:', ['url' => $redirectTo]);

        return redirect()->to($redirectTo);
    }

    return back()->withErrors([
        'error' => 'Thông tin đăng nhập không chính xác.',
    ]);
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
        $address = UserAddresses::where('user_id', $user->user_id)
            ->orderByDesc('is_default') // Ưu tiên is_default = true
            ->orderBy('user_id')              // Nếu không có thì lấy theo id tăng dần (địa chỉ đầu tiên)
            ->first();

        $wallet = $user->wallet; // Nếu bạn có quan hệ User -> Wallet (hasOne)
        $transactions = $wallet ? $wallet->transactions()->latest()->limit(10)->get() : collect(); // Lấy lịch sử giao dịch ví

        return view('client.auth.profile', compact('user', 'address', 'wallet', 'transactions'));

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
        $jsonData = file_get_contents('https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json');
        $location = json_decode($jsonData, true);

        // Tìm thành phố
        $city = collect($location)->firstWhere('Id', $request->city);

        if (! $city) {
            return back()->withErrors(['city' => 'Thành phố không tồn tại!']);
        }

        // Tìm quận huyện trong thành phố
        $district = collect($city['Districts'])->firstWhere('Id', $request->district);

        if (! $district) {
            return back()->withErrors(['district' => 'Quận huyện không tồn tại!']);
        }

        // Tìm phường xã trong quận huyện
        $ward = collect($district['Wards'])->firstWhere('Id', $request->ward);

        if (! $ward) {
            return back()->withErrors(['ward' => 'Phường xã không tồn tại!']);
        }

        // Tìm địa chỉ mặc định hiện có của user
        $defaultAddress = UserAddresses::where('user_id', $user->user_id)
            ->where('is_default', true)
            ->first();

        if ($defaultAddress) {
            // Cập nhật địa chỉ mặc định hiện có
            $defaultAddress->update([
                'city' => $city['Name'],
                'district' => $district['Name'],
                'ward' => $ward['Name'],
                'street_address' => $request->street_address,
            ]);
        } else {
            // Tạo mới địa chỉ mặc định nếu chưa có
            UserAddresses::create([
                'user_id' => $user->user_id,
                'city' => $city['Name'],
                'district' => $district['Name'],
                'ward' => $ward['Name'],
                'street_address' => $request->street_address,
                'is_default' => true,
            ]);
        }

        return back()->with('success', 'Cập nhật địa chỉ thành công.');
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                Storage::delete('public/avatars/'.$user->avatar);
            }

            // Lưu avatar mới
            $avatarName = time().'.'.$request->avatar->extension();
            $request->avatar->storeAs('public/avatars', $avatarName);

            // Cập nhật avatar trong database
            $user->update(['avatar' => $avatarName]);
        }

        return back()->with('success', 'Avatar cập nhật thành công.');
    }
}
