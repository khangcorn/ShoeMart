@extends('client.layout')

@section('content')



<style>
    .notification {
    background-color: #f8f9fa;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    margin: 10px 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    opacity: 1;
    transition: opacity 1s ease-in-out;
}

/* Khi ẩn thông báo */
.notification.hide {
    opacity: 0;
    pointer-events: none;
}

/* Tạo hiệu ứng cho thông báo */
.notification p {
    margin: 0;
    font-size: 14px;
}

.notification p:first-child {
    font-weight: bold;
}

</style>
<div class="container mx-auto p-6">
    @php
    $latestNotification = auth()->user()->unreadNotifications()->latest()->first();
@endphp

@if ($latestNotification)
    <div id="flash-message" class="notification">
        <p>{{ $latestNotification->data['message'] }}</p>
        <p>Thời gian: {{ $latestNotification->created_at->format('d/m/Y H:i') }}</p>
    </div>

    @php
        $latestNotification->markAsRead(); // đánh dấu là đã đọc
    @endphp
@endif

    <div  class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        @if (session('success'))
        <div id="flash-message" class="fixed top-5 right-5 bg-green-100 text-green-800 px-4 py-2 rounded shadow z-50">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div id="flash-message" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md my-4">
            {{ session('error') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div id="flash-message" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md my-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    


        <!-- Thông tin cá nhân -->
        <div class="flex justify-between gap-6 p-6 bg-white rounded-lg shadow-md">
            <!-- Thông tin cá nhân -->
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-2">Thông tin cá nhân</h2>
                <p class="text-gray-700"><strong>Username:</strong> {{ $user->username }}</p>
                <p class="text-gray-700"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="text-gray-700"><strong>Địa chỉ:</strong> 
                    {{ $address ? $address->street_address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city : 'Chưa cập nhật' }}
                </p>
            </div>


<!-- Modal Nạp tiền qua tài khoản liên kết -->
<div id="depositWithBankModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-md shadow-md w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Nạp tiền qua tài khoản ngân hàng liên kết</h3>
        <form action="{{ route('wallet.deposit') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-medium">Số tiền muốn nạp:</label>
                <input type="number" name="amount" min="10000" class="w-full p-2 border rounded" required>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2 font-medium">Chọn ngân hàng liên kết:</label>
                <select name="bank_id" class="w-full p-2 border rounded" required>
                    <option value="">Chọn ngân hàng</option>
                    @foreach ($user->banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->bank_name }} - Số tài khoản: {{ $bank->account_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="document.getElementById('depositWithBankModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Nạp tiền</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Rút Tiền -->
<div id="withdrawModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-md shadow-md w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Rút Tiền</h3>
        <form action="{{ route('wallet.withdraw') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-medium">Số tiền:</label>
                <input type="number" name="amount" class="w-full p-2 border rounded" min="1000" required>
            </div>
            @if ($errors->has('amount'))
    <div class="alert alert-danger">
        {{ $errors->first('amount') }}
    </div>
@endif
            <div class="mb-4">
                <label class="block mb-2 font-medium">Chọn tài khoản ngân hàng:</label>
                <select name="user_bank_id" class="w-full p-2 border rounded" required>
                    <option value="">Chọn ngân hàng</option>
                    @foreach ($user->banks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->bank_name }} - Số tài khoản: {{ $bank->account_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="document.getElementById('withdrawModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-gray-500 text-white rounded-md ">Rút tiền</button>
            </div>
        </form>
    </div>
</div>



<!-- Modal Liên kết Ngân hàng -->
<div id="linkBankModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-md shadow-md w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Liên kết ngân hàng</h3>
        <form action="{{ route('wallet.link-bank') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-medium">Ngân hàng:</label>
                <select name="bank" class="w-full p-2 border rounded" required>
                    <option value="">Chọn ngân hàng</option>
                    <option value="vietcombank">Vietcombank</option>
                    <option value="vpbank">VPBank</option>
                    <option value="techcombank">Techcombank</option>
                    <option value="mbbank">MB Bank</option>
                    <!-- Add more banks if needed -->
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-medium">Số tài khoản:</label>
                <input type="text" name="account_number" class="w-full p-2 border rounded" required>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="document.getElementById('linkBankModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

        
            <!-- Avatar -->

           
        
            <!-- Cập nhật Avatar -->
            <div class="flex justify-between bg-gray-100 p-6 rounded-lg shadow-md ">
                <div class=" p-6">
                    <h3 class="text-xl font-semibold mb-2">Avatar</h3>
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" 
                             alt="Avatar" class="w-20 h-20 rounded border shadow-lg">
                    @else
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                            No Avatar
                        </div>
                    @endif
                </div>
        
                <form action="{{ route('update-avatar') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                    <h3 class="text-xl font-semibold">Cập nhật Avatar</h3>
                    @csrf
                    <input type="file" name="avatar" accept="image/*" class="w-full p-2 border rounded mb-3">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">Cập nhật</button>
                </form>
            </div>
      
        </div>
        

        <!-- Cập nhật địa chỉ -->
        <div class="bg-gray-100 p-6 rounded-lg shadow-md mt-6">
            <h3 class="text-xl font-semibold">Cập nhật địa chỉ</h3>
            <form action="{{ route('update-address') }}" method="POST" class="mt-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Tỉnh/Thành phố:</label>
                        <select id="city" name="city" class="w-full p-2 border rounded mb-3">
                            <option value="" selected>Chọn tỉnh thành</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Quận/Huyện:</label>
                        <select id="district" name="district" class="w-full p-2 border rounded mb-3">
                            <option value="" selected>Chọn quận huyện</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Phường/Xã:</label>
                        <select id="ward" name="ward" class="w-full p-2 border rounded mb-3">
                            <option value="" selected>Chọn phường xã</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Địa chỉ:</label>
                        <input type="text" name="street_address" id="street_address" class="w-full p-2 border rounded mb-3">
                    </div>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Lưu địa chỉ</button>
            </form>
        </div>
<!-- Ví người dùng -->
<div class="bg-gray-100 p-6 rounded-lg shadow-md mt-6">
    <h3 class="text-xl font-semibold mb-4">Ví của bạn</h3>

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-lg font-medium">Số dư hiện tại:</p>
            <p class="text-3xl text-green-600 font-bold">
                {{ number_format($wallet?->balance ?? 0, 0, ',', '.') }}₫
            </p>
            
        </div>
        <div class="flex gap-4">
            <!-- Nạp tiền -->
          <!-- Button Nạp tiền qua tài khoản liên kết -->
            <button onclick="document.getElementById('depositWithBankModal').classList.remove('hidden')" 
            class="bg-blue-500 text-white px-4 py-2 rounded-md mt-4">
            Nạp tiền 
            </button>

           <!-- Rút tiền -->
           <button onclick="document.getElementById('withdrawModal').classList.remove('hidden')" 
           class="bg-blue-500 text-white px-4 py-2 rounded-md mt-4">
                Rút tiền
            </button>
        </div>
    </div>
<!-- Ngân hàng liên kết -->
<div class="bg-white p-6 rounded-lg shadow-md mt-6">
    <h4 class="text-lg font-semibold mb-3">Ngân hàng liên kết</h4>
    @if (isset($user->banks) && $user->banks->isNotEmpty())
        <ul class="list-disc pl-6">
            @foreach ($user->banks as $bank)
                <li class="flex items-center justify-between">
                    <!-- Sử dụng logo ngân hàng -->
                    <div class="flex items-center">
                        <img src="{{ asset('images/banks/' . strtolower(str_replace(' ', '_', $bank->bank_name)) . '.jpg') }}" alt="{{ $bank->bank_name }}" class="w-6 h-6 mr-2">
                        {{ $bank->bank_name }} - Số tài khoản: {{ $bank->account_number }}
                    </div>
                    <!-- Nút Hủy Liên Kết -->
                    <form action="{{ route('wallet.unlink-bank', $bank->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy liên kết ngân hàng này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            Hủy liên kết
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500">Chưa có ngân hàng liên kết.</p>
    @endif
    <button onclick="document.getElementById('linkBankModal').classList.remove('hidden')" 
            class="bg-green-500 text-white px-4 py-2 rounded-md mt-4">
        Liên kết ngân hàng
    </button>
</div>



    <!-- Lịch sử giao dịch -->
    <h4 class="text-lg font-semibold mb-3">Lịch sử giao dịch</h4>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-md">
            <thead>
                <tr>
                    <th class="px-4 py-2">Mã giao dịch</th>
                    <th class="px-4 py-2">Loại</th>
                    <th class="px-4 py-2">Số tiền</th>
                    <th class="px-4 py-2">Mô tả</th>
                    <th class="px-4 py-2">Trạng thái</th>
                    <th class="px-4 py-2">Ngày tạo</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($transactions as $transaction)
                    <tr class="text-center border-t">
                        <td class="px-4 py-2">{{ $transaction->transaction_id }}</td>
                        <td class="px-4 py-2">
                            @if ($transaction->type == 'deposit')
                                Nạp tiền
                            @elseif ($transaction->type == 'withdraw')
                                Rút tiền
                            @elseif ($transaction->type == 'refund')
                                Hoàn tiền
                            @else
                                Thanh toán đơn hàng
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ number_format($transaction->amount, 0, ',', '.') }} ₫</td>
                        <td class="px-4 py-2">{{ $transaction->description }}</td>
                        <td class="px-4 py-2">
                            @if ($transaction->status == 'pending')
                                <span class="text-yellow-500 font-semibold">Chờ duyệt</span>
                            @elseif ($transaction->status == 'approved')
                                <span class="text-blue-500 font-semibold">Đã duyệt</span>
                            @elseif ($transaction->status == 'completed')
                                <span class="text-green-600 font-semibold">Hoàn tất</span>
                            @elseif ($transaction->status == 'rejected')
                                <span class="text-red-500 font-semibold">Đã từ chối</span>
                            @elseif ($transaction->status == 'failed')
                                <span class="text-red-500 font-semibold">Thất bại</span>
                            @else
                                <span class="text-gray-500 font-semibold">{{ ucfirst($transaction->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    
                @endforeach
                
            </tbody>
        </table>
<div class="mt-4">
    {{ $transactions->links() }}
</div>

        
    </div>



        <!-- Logout -->
        <div class="bg-white p-6 rounded-lg shadow-md mt-6 text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Đăng xuất</button>
            </form>
            <a href="{{ route('password.request') }}" class="block mt-4 text-blue-500 hover:underline">Quên mật khẩu?</a>
        </div>
    </div>
</div>

<!-- Script xử lý địa chỉ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
    var citis = document.getElementById("city");
    var districts = document.getElementById("district");
    var wards = document.getElementById("ward");

    axios.get("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json")
        .then(response => {
            const data = response.data;
            loadCities(data);

            citis.onchange = function () {
                loadDistricts(data, this.value);
                wards.length = 1;
            };
            
            districts.onchange = function () {
                loadWards(data, citis.value, this.value);
            };
        });

    function loadCities(data) {
        for (const city of data) {
            citis.options[citis.options.length] = new Option(city.Name, city.Id);
        }
    }

    function loadDistricts(data, cityId) {
        districts.length = 1;
        if (!cityId) return;
        let cityData = data.find(city => city.Id === cityId);
        for (const district of cityData.Districts) {
            districts.options[districts.options.length] = new Option(district.Name, district.Id);
        }
    }

    function loadWards(data, cityId, districtId) {
        wards.length = 1;
        if (!cityId || !districtId) return;
        let cityData = data.find(city => city.Id === cityId);
        let districtData = cityData.Districts.find(district => district.Id === districtId);
        for (const ward of districtData.Wards) {
            wards.options[wards.options.length] = new Option(ward.Name, ward.Id);
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(function (notification) {
        setTimeout(function () {
            notification.classList.add('hide');
        }, 10000); // 10 giây
    });
});

        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.remove();
            }
        }, 5000); // 5000ms = 5 giây

</script>
@endsection
