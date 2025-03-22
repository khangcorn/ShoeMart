 <!-- Hiển thị thông báo -->
 @extends('client.layout')

@section('content')
 <div class="container">
    <h2>Thông tin cá nhân</h2>
    <p><strong>Username:</strong> {{ $user->username }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Địa chỉ:</strong> 
        {{ $address ? $address->street_address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->province : 'Chưa cập nhật' }}
    </p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h3>Cập nhật địa chỉ</h3>
    <form action="{{ route('update-address') }}" method="POST">
        @csrf
        <div>
            <label for="city">Tỉnh/Thành phố:</label>
            <select class="form-select form-select-sm mb-3" id="city" name="city">
                <option value="" selected>Chọn tỉnh thành</option>           
            </select>
            
            <label for="district">Quận/Huyện:</label>
            <select class="form-select form-select-sm mb-3" id="district" name="district">
                <option value="" selected>Chọn quận huyện</option>
            </select>
    
            <label for="ward">Phường/Xã:</label>
            <select class="form-select form-select-sm" id="ward" name="ward">
                <option value="" selected>Chọn phường xã</option>
            </select>

            <label for="ward">Địa chỉ:</label>
       
             <input type="text" class="form-label" name="street_address" id="street_address">

            </select>
        </div>  
        <button type="submit" class="btn btn-primary">Lưu địa chỉ</button>
    </form>
    
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <a href="{{route('password.request')}}" class=""> Quên Mật khẩu </a>

</div>



<h3>Cập nhật Avatar</h3>
<form action="{{ route('update-avatar') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="avatar">Chọn ảnh đại diện:</label>
        <input type="file" name="avatar" accept="image/*">
    </div>  
    <button type="submit" class="btn btn-primary">Cập nhật</button>
</form>

@if(Auth::user()->avatar)
    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="Avatar" width="150">
@endif

// Cập nhật địa chỉ 

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
    var citis = document.getElementById("city");
    var districts = document.getElementById("district");
    var wards = document.getElementById("ward");

    var Parameter = {
        url: "https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json", 
        method: "GET", 
        responseType: "json", 
    };

    axios(Parameter).then(function (response) {
        const data = response.data;
        loadCities(data);

        citis.onchange = function () {
            loadDistricts(data, this.value);
            wards.length = 1; // Reset phường xã khi chọn lại tỉnh
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
        districts.length = 1; // Reset quận huyện
        if (!cityId) return;
        
        let cityData = data.find(city => city.Id === cityId);
        for (const district of cityData.Districts) {
            districts.options[districts.options.length] = new Option(district.Name, district.Id);
        }
    }

    function loadWards(data, cityId, districtId) {
        wards.length = 1; // Reset phường xã
        if (!cityId || !districtId) return;

        let cityData = data.find(city => city.Id === cityId);
        let districtData = cityData.Districts.find(district => district.Id === districtId);
        for (const ward of districtData.Wards) {
            wards.options[wards.options.length] = new Option(ward.Name, ward.Id);
        }
    }
</script>
@endsection