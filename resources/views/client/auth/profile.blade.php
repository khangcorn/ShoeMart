@extends('client.layout')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md my-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md my-4">
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
                    {{ $address ? $address->street_address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->province : 'Chưa cập nhật' }}
                </p>
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
</script>
@endsection
