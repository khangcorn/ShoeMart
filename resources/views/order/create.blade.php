@extends('client.layout')

@section('content')
{{-- @if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-md">
        {{ session('success') }}
    </div>
@endif --}}
@if(session('success'))
<div id="topNotification" class="bg-green-500 text-white p-4 text-center font-semibold">
    {{ session('success') }}
</div>
<script>
    setTimeout(() => {
        document.getElementById('topNotification').style.display = 'none';
    }, 5000);
</script>
@endif
@if(session('error'))
<div id="topNotification" class="bg-red-500 text-white p-4 text-center font-semibold">
    {{ session('error') }}
</div>
<script>
    setTimeout(() => {
        document.getElementById('topNotification').style.display = 'none';
    }, 5000);
</script>
@endif
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Thanh toán</h2>

    <!-- Form đặt hàng -->
    <form id="orderForm" action="{{ route('order.store') }}" method="POST">
        @csrf
        <!-- Chọn địa chỉ giao hàng -->
      <div class="mb-6">
    <h3 class="text-xl font-semibold mb-2">Địa chỉ giao hàng</h3>
    @php
        $user = Auth::user();
        if ($user) {
            $user->load('userAddresses');
            $defaultAddress = $user->userAddresses->where('is_default', true)->first();
        } else {
            $defaultAddress = null;
        }
    @endphp

    <div id="selectedAddress" class="border p-4 rounded-md mb-2">
        @if($defaultAddress)
            <span>{{ $defaultAddress->street_address }}, {{ $defaultAddress->ward }}, {{ $defaultAddress->district }}, {{ $defaultAddress->city }}</span>
            <input type="hidden" name="address_id" id="selectedAddressId" value="{{ $defaultAddress->address_id }}">
        @else
            <span id="addressError" class="text-red-500">Bạn chưa chọn địa chỉ giao hàng.</span>
            <input type="hidden" name="address_id" id="selectedAddressId" value="">
        @endif
    </div>

    <div class="flex justify-between">
        <button type="button" class="bg-blue-500 text-white py-2 px-4 rounded-md" onclick="openAddressPopup()">
            Thay đổi địa chỉ nhận hàng
        </button>
    </div>
</div>

       <!-- Phương thức thanh toán -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Phương thức thanh toán</h3>
            <select name="payment_method" class="w-full p-2 border border-gray-300 rounded-md">
                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                <option value="wallet">Thanh toán qua ví</option>
            </select>
        </div>



    
    
    
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-2">Mã giảm giá</h3>
    
        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <input type="text" name="codes" id="couponInput"
                   class="w-full sm:w-1/2 p-2 border border-gray-300 rounded-md"
                   placeholder="Nhập mã giảm giá (cách nhau dấu phẩy)">
      
        </div>
    
        <a href="{{ route('vouchers.index') }}?cart_detail_ids={{ request()->query('cart_detail_ids') }}" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
            🔍 Xem danh sách mã giảm giá 
        </a>
    
        <div id="couponResult" class="mt-3 text-sm text-gray-700"></div>
    

    </div>
    
    <input type="hidden" name="order_coupon_id" id="orderCouponIdInput" value="">
    <input type="hidden" name="shipping_coupon_id" id="shippingCouponIdInput" value="">

        

        <!-- Thông tin giỏ hàng -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Thông tin giỏ hàng</h3>
          <ul class="space-y-2">
    @foreach($cartItems as $item)
        @php
            $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price_sale ?? $item->product->price;
        @endphp
        <li class="flex items-center gap-4">
            <a href="{{ route('products.detail', $item->product->product_id) }}">

                <img src="{{ asset('storage/' . $item->product->images->first()->image_url) }}" alt="{{ $item->product->name }}" class="w-20 h-20 mr-4">
            </a>
           
            <div>
                <span class="font-semibold">{{ $item->product->name }}</span>
                - Số lượng: {{ $item->quantity }} x {{ number_format($price, 0, ',', '.') }} đ =
                <span class="font-bold">{{ number_format($price * $item->quantity, 0, ',', '.') }} đ</span>
            </div>
        </li>
    @endforeach
</ul>

  <div class="order-summary mt-4">
    <!-- Tổng tiền giỏ hàng -->
    <p class="summary-item text-lg font-bold">
        Tổng tiền giỏ hàng:
        <span id="cartTotal" class="amount">
            {{ number_format($total, 0, ',', '.') }} đ
        </span>
    </p>

    <!-- Phí vận chuyển -->
    <div class="shipping-info">
        <p id="shippingFeeText" class="summary-item text-lg font-bold">Phí vận chuyển:
              @if($shippingFeeValue > 0)
                {{ number_format($shippingFeeValue, 0, ',', '.') }} đ
            @else
                Phí vận chuyển không xác định.
            @endif
        </p>
       
        <input type="hidden" id="shippingFeeValue" name="shipping_fee" value="{{ $shippingFeeValue }}">
        <input type="hidden" id="shippingId" name="shipping_id" value="{{ $shippingId }}">
    </div>

    <!-- Giảm giá đơn hàng -->
    <p class="summary-item text-lg font-bold">
        Giảm giá đơn hàng:
        <span id="orderDiscount" class="discount">
            {{ number_format($orderDiscount ?? 0, 0, ',', '.') }} đ
        </span>
    </p>

    <!-- Giảm giá phí vận chuyển -->
    <p class="summary-item text-lg font-bold">
        Giảm giá phí vận chuyển:
        <span id="shippingDiscount" class="discount">
            {{ number_format($shippingDiscount ?? 0, 0, ',', '.') }} đ
        </span>
    </p>

    <!-- Tổng tiền thanh toán -->
    <p class="summary-item mt-4 text-lg font-bold">
        <strong>Tổng tiền thanh toán:</strong>
        <span id="totalPrice" class="total-price">
            {{ number_format(
                max(0, $total + ($shippingFeeValue ?? 0) - ($orderDiscount ?? 0) - ($shippingDiscount ?? 0)),
                0,
                ',',
                '.'
            ) }} đ
        </span>
    </p>
</div>

</p>


        </div>
        <input type="hidden" name="order_discount" id="orderDiscountInput" value="0">
        <input type="hidden" name="shipping_discount" id="shippingDiscountInput" value="0">

        @foreach($cartDetailIds as $id)
        <input type="hidden" name="cart_detail_ids[]" value="{{ $id }}">
        @endforeach
    
        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded-md" onclick="return validateOrder()">
                Xác nhận đơn hàng
            </button>
            <a href="{{ route('cart.index') }}" class="bg-gray-300 text-gray-800 py-2 px-6 rounded-md hover:bg-gray-400 transition">
                Quay lại giỏ hàng
            </a>
        </div>
    </form>
  
</div>
@endsection

<div id="addressFormPopup" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-md w-96">
        <h2 id="addressFormTitle" class="text-xl font-semibold mb-4">Thêm địa chỉ mới</h2>
        
        <form id="addressForm">
            @csrf
            <input type="hidden" id="addressId" name="address_id">
            
            <div class="mb-2">
                <label class="block font-semibold">Tên địa chỉ:</label>
                <input type="text" id="addressName" name="address_name" class="w-full p-2 border rounded-md" required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Người nhận:</label>
                <input type="text" id="recipientName" name="recipient_name" class="w-full p-2 border rounded-md" required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Địa chỉ:</label>
                <input type="text" id="streetAddress" name="street_address" class="w-full p-2 border rounded-md" required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Số điện thoại:</label>
                <input type="tel" id="recipientPhone" name="recipient_phone" class="w-full p-2 border rounded-md" required>
            </div>

            <!-- Chọn Tỉnh -->
            <div class="mb-2">
                <label class="block font-semibold">Tỉnh/Thành phố:</label>
                <select id="city" name="city" class="w-full p-2 border rounded-md" required>
                    <option value="">Chọn tỉnh/thành phố</option>
                </select>
            </div>

            <!-- Chọn Huyện -->
            <div class="mb-2">
                <label class="block font-semibold">Quận/Huyện:</label>
                <select id="district" name="district" class="w-full p-2 border rounded-md" required>
                    <option value="">Chọn quận/huyện</option>
                </select>
            </div>

            <!-- Chọn Xã -->
            <div class="mb-2">
                <label class="block font-semibold">Xã:</label>
                <select id="ward" name="ward" class="w-full p-2 border rounded-md" required>
                    <option value="">Chọn xã</option>
                </select>
            </div>

            <div class="mt-4 flex justify-between">
                <button type="button" onclick="saveAddress()" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                    Lưu
                </button>
                <button type="button" onclick="closeAddressForm()" class="bg-gray-400 text-black px-4 py-2 rounded-md">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Khu vực hiển thị thông báo lỗi -->
<div id="error-message" class="alert alert-danger" style="display: none;"></div>

<!-- Popup danh sách địa chỉ -->
<div id="addressPopup" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-md w-96">
        <h2 class="text-xl font-semibold mb-4">Thay đổi địa chỉ nhận hàng</h2>
        <div id="addressList">
            @foreach($user->userAddresses as $address)
            <div class="border p-4 rounded-md mb-2 address-item" data-address-id="{{ $address->address_id }}">
                    <label class="inline-flex items-center">
                        <input type="radio" name="address_id" value="{{ $address->address_id }}"
                            {{ $address->is_default ? 'checked' : '' }} onchange="selectAddress(this)">
                        <span class="ml-2">
                            <strong>{{ $address->address_name }}</strong> - {{ $address->recipient_name }} <br>
                            {{ $address->street_address }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                        </span>
                    </label>
                        <button class="bg-yellow-500 text-black text-sm px-2 py-1 rounded-md"
                        onclick="editAddress({{ $address->address_id }}, '{{ $address->address_name }}', '{{ $address->recipient_name }}',{{  $address->recipient_phone }}, '{{ $address->street_address }}', '{{ $address->ward }}', '{{ $address->district }}', '{{ $address->city }}')">
                        Sửa
                        </button>    
                               
                </div>
            @endforeach
        </div>
  <!-- Báo lỗi khi chưa có địa chỉ -->
    @if(empty($address))
    <span id="addressError" class="text-red-500">Bạn chưa có địa chỉ giao hàng.</span>
    <input type="hidden" name="address_id" id="selectedAddressId" value="">
    @endif

    <!-- Các nút hành động -->
    <div class="mt-4 flex justify-end gap-4 flex-wrap" id="addressButtonsWrapper">
    <button onclick="openAddAddressForm()" class="bg-green-500 text-black px-4 py-2 rounded-md">
        Thêm địa chỉ mới
    </button>

    <div id="addressActionButtons" class="{{ empty($address) ? 'hidden' : '' }} flex gap-4">
        <button
            onclick="confirmAddressSelection()"
            class="bg-blue-500 text-white px-4 py-2 rounded-md">
            Chọn địa chỉ làm mặc định
        </button>
    </div>
<button type="button"
            onclick="closeAddressPopup()"
            class="bg-gray-100 text-black px-4 py-2 rounded-md border border-gray-5000">
            Đóng
        </button>
    </div>



    
        
    </div>
</div>

<script>
    // Mở popup chọn địa chỉ
    function openAddressPopup() {
        document.getElementById('addressPopup').classList.remove('hidden');
    }

    // Đóng popup chọn địa chỉ
    function closeAddressPopup() {
        document.getElementById('addressPopup').classList.add('hidden');
    }

   

    // Đóng form thêm/sửa địa chỉ và quay lại danh sách
    function closeAddressForm() {
        document.getElementById("addressFormPopup").classList.add("hidden");
        document.getElementById("addressPopup").classList.remove("hidden");
    }

    // Ẩn popup danh sách khi sửa địa chỉ
  
async function loadCities(selectedCity = null) {
    const citySelect = document.getElementById("city");

    // Gọi API lấy danh sách tỉnh thành
    const response = await fetch('https://provinces.open-api.vn/api/?depth=1');
    const cities = await response.json();

    // Xóa hết các option cũ
    citySelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';

    // Thêm các tỉnh thành vào select
    cities.forEach(c => {
        const option = document.createElement("option");
        option.value = c.name;
        option.textContent = c.name;
        if (selectedCity && c.name === selectedCity) {
            option.selected = true;
        }
        citySelect.appendChild(option);
    });
}

// Hàm load danh sách quận huyện khi chọn tỉnh thành
async function loadDistricts(cityName, selectedDistrict = null) {
    const districtSelect = document.getElementById("district");

    // Gọi API lấy thông tin tỉnh thành với thông tin sâu hơn (bao gồm cả quận/huyện)
    const response = await fetch('https://provinces.open-api.vn/api/?depth=2');
    const cities = await response.json();

    // Tìm tỉnh thành tương ứng
    const city = cities.find(c => c.name === cityName);
    if (!city) return;

    // Lọc các quận huyện của tỉnh thành đó
    const districts = city.districts;

    // Xóa hết các option cũ
    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';

    // Thêm các quận huyện vào select
    districts.forEach(d => {
        const option = document.createElement("option");
        option.value = d.name;
        option.textContent = d.name;
        if (selectedDistrict && d.name === selectedDistrict) {
            option.selected = true;
        }
        districtSelect.appendChild(option);
    });
}

// Hàm load danh sách xã/phường khi chọn quận/huyện
async function loadWards(cityName, districtName, selectedWard = null) {
    const wardSelect = document.getElementById("ward");

    // Gọi API lấy thông tin tỉnh thành với thông tin sâu hơn (bao gồm cả xã/phường)
    const response = await fetch('https://provinces.open-api.vn/api/?depth=3');
    const cities = await response.json();

    // Tìm tỉnh thành và quận huyện tương ứng
    const city = cities.find(c => c.name === cityName);
    if (!city) return;

    const district = city.districts.find(d => d.name === districtName);
    if (!district) return;

    // Lọc các xã/phường của quận huyện đó
    const wards = district.wards;

    // Xóa hết các option cũ
    wardSelect.innerHTML = '<option value="">Chọn xã</option>';

    // Thêm các xã/phường vào select
    wards.forEach(w => {
        const option = document.createElement("option");
        option.value = w.name;
        option.textContent = w.name;
        if (selectedWard && w.name === selectedWard) {
            option.selected = true;
        }
        wardSelect.appendChild(option);
    });
}

// Khi chọn tỉnh thành, load các quận huyện
document.getElementById("city").addEventListener("change", function() {
    const selectedCity = this.value;
    loadDistricts(selectedCity);
    loadWards(selectedCity, null);  // Reset xã/phường khi đổi tỉnh thành
});

// Khi chọn quận huyện, load các xã/phường
document.getElementById("district").addEventListener("change", function() {
    const selectedCity = document.getElementById("city").value;
    const selectedDistrict = this.value;
    loadWards(selectedCity, selectedDistrict);
});


// Gọi hàm để tải tỉnh/thành phố khi mở form
document.getElementById("addressFormPopup").addEventListener("show", function() {
    loadCities();
});
 function editAddress(id, name, recipient, phone, street, ward, district, city) {
    // Ẩn form hiện tại và hiển thị form chỉnh sửa
    document.getElementById("addressPopup").classList.add("hidden");
    document.getElementById("addressFormPopup").classList.remove("hidden");

    // Cập nhật tiêu đề form và các giá trị các input
    document.getElementById("addressFormTitle").innerText = "Chỉnh sửa địa chỉ";
    document.getElementById("addressId").value = id;
    document.getElementById("addressName").value = name;
    document.getElementById("recipientName").value = recipient;
    document.getElementById("recipientPhone").value = phone;
    document.getElementById("streetAddress").value = street;

    // Load lại danh sách tỉnh/thành phố rồi chọn giá trị
     loadCities(city); // Hàm load danh sách tỉnh và chọn tỉnh

    // Load các quận huyện cho tỉnh đã chọn
     loadDistricts(city, district);

    // Load các xã/phường cho quận đã chọn
    loadWards(city, district, ward);
}
 // Mở form thêm địa chỉ mới
    function openAddAddressForm() {
        const addressCount = document.querySelectorAll("#addressList > div").length;
        if (addressCount >= 3) {
            alert("Bạn chỉ có thể lưu tối đa 3 địa chỉ!");
            return;
        }

        document.getElementById("addressPopup").classList.add("hidden");
        document.getElementById("addressFormPopup").classList.remove("hidden");

        document.getElementById("addressFormTitle").innerText = "Thêm địa chỉ mới";
        ['addressId', 'addressName', 'recipientName', 'streetAddress', 'ward', 'district', 'city'].forEach(id => {
            document.getElementById(id).value = "";
        });
        loadCities();
    }

    // Gửi form lưu địa chỉ qua fetch
    function saveAddress() {
    const addressStoreUrl = "{{ route('address.store') }}";
    let form = document.getElementById("addressForm");
    let formData = new FormData(form);

    // Lấy ID địa chỉ nếu đang sửa
    let addressId = document.getElementById('addressId').value;
    let isEditing = addressId !== "";

    // Gán thêm _method nếu là PUT
    if (isEditing) {
        formData.append('_method', 'PUT');
    }

    // Kiểm tra checkbox "đặt làm mặc định"
    let isDefault = document.querySelector('input[name="is_default"]:checked') ? 1 : 0;
    formData.append('is_default', isDefault);

    let url = isEditing ? `/address/${addressId}` : addressStoreUrl;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Địa chỉ đã được lưu!");

            // Cập nhật giao diện
            updateAddressList(data.newAddress);

            // Nếu là mặc định thì cập nhật checked
            if (data.newAddress.is_default) {
                document.querySelectorAll('input[name="address_id"]').forEach(radio => {
                    radio.checked = (radio.value == data.newAddress.address_id);
                });
            }

            // Đóng form thêm/sửa
            document.getElementById("addressFormPopup").classList.add("hidden");
            document.getElementById("addressPopup").classList.remove("hidden");
        } else {
            alert("Lỗi khi lưu địa chỉ: " + data.message);
        }
    })
    .catch(error => {
        console.error("Lỗi:", error);
        alert("Có lỗi xảy ra.");
    });
}

function updateAddressList(newAddress) {
    let addressList = document.getElementById("addressList");

    // Tìm phần tử địa chỉ cũ theo ID
    let existingAddress = document.querySelector(`#addressList [data-address-id="${newAddress.address_id}"]`);

    // Nếu có địa chỉ cũ thì xóa hẳn ra khỏi danh sách
    if (existingAddress) {
        existingAddress.remove();
    }   

    // Tạo HTML mới cho địa chỉ
    let newAddressHTML = `
        <div class="border p-4 rounded-md mb-2 address-item" data-address-id="${newAddress.address_id}">
            <label class="inline-flex items-center">
                <input type="radio" name="address_id" value="${newAddress.address_id}" 
                    ${newAddress.is_default ? 'checked' : ''} onchange="selectAddress(this)">
                <span class="ml-2">
                    <strong class="address-name">${newAddress.address_name}</strong> - 
                    <span class="recipient-name">${newAddress.recipient_name}</span><br>
                    <span class="street-address">${newAddress.street_address}</span>, 
                    <span class="ward">${newAddress.ward}</span>, 
                    <span class="district">${newAddress.district}</span>, 
                    <span class="city">${newAddress.city}</span>
                </span>
            </label>

            <button class="bg-yellow-500 text-black text-sm px-2 py-1 rounded-md"
                onclick="editAddress(${newAddress.address_id}, '${newAddress.address_name}', '${newAddress.recipient_name}', ${newAddress.recipient_phone}, '${newAddress.street_address}', '${newAddress.ward}', '${newAddress.district}', '${newAddress.city}')">
                Sửa
            </button>
        </div>
    `;
    let addressError = document.querySelector('#addressError');
    if (addressError) {
        addressError.remove();
    }
    // Chèn địa chỉ mới lên đầu danh sách
    addressList.insertAdjacentHTML('afterbegin', newAddressHTML);
        // Cập nhật giá trị của selectedAddressId

    // Kiểm tra số lượng địa chỉ và ẩn nút "Thêm địa chỉ mới" nếu đủ 3
    let addressCount = document.querySelectorAll("#addressList .address-item").length;
    if (addressCount >= 3) {
        document.querySelector("button[onclick='openAddAddressForm()']").style.display = "none";
    }
    // Hiện nút chọn làm mặc định + hủy
document.getElementById("addressActionButtons")?.classList.remove("hidden");


}



function selectAddress(radio) {
    const selectedAddressId = document.getElementById('selectedAddressId');
    const selectedText = radio.closest('div').querySelector('span').innerHTML;
    const selectedDiv = document.getElementById('selectedAddress');

    console.log("Địa chỉ được chọn: ", selectedText);

    selectedDiv.innerHTML = `
        <div>${selectedText}</div>
        <input type="hidden" name="address_id" id="selectedAddressId" value="${radio.value}">
    `;

    // Gọi hàm updateShippingFee và kiểm tra log
    console.log("Gọi hàm cập nhật phí vận chuyển với address_id:", radio.value);
    updateShippingFee(radio.value);

    // Kiểm tra nếu không có selectedAddressId
    if (!selectedAddressId) {
        const selectedAddressDiv = document.getElementById('selectedAddress');
        const selectedAddressId = document.createElement('input');
        selectedAddressId.type = "hidden";
        selectedAddressId.id = "selectedAddressId";
        selectedAddressId.name = "address_id";
        selectedAddressDiv.appendChild(selectedAddressId);
    }

    // Cập nhật lại giá trị của selectedAddressId
    selectedAddressId.value = radio.value;
}


function confirmAddressSelection() {
    let selectedRadio = document.querySelector('input[name="address_id"]:checked');

    if (selectedRadio) {
        let addressId = parseInt(selectedRadio.value); // Lấy ID từ radio đang chọn
        let addressDiv = selectedRadio.closest('div');
        let selectedText = addressDiv.querySelector('span').innerHTML;

        document.getElementById('selectedAddress').innerHTML = `
            <div>${selectedText}</div>
            <input type="hidden" name="address_id" id="selectedAddressId" value="${addressId}">
        `;

        // Gửi request cập nhật mặc định
        fetch(`/address/${addressId}/set-default`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                showNotification("Địa chỉ mặc định đã được thay đổi!");

                // Cập nhật lại các radio
                document.querySelectorAll('input[name="address_id"]').forEach(input => {
                    input.checked = (parseInt(input.value) === addressId);
                });
            } else {
                alert("Lỗi khi thay đổi địa chỉ mặc định.");
            }
        })
        .catch(error => {
            console.error("Lỗi:", error);
            alert("Đã có lỗi xảy ra.");
        });

        closeAddressPopup();
    } else {
        alert("Vui lòng chọn địa chỉ trước khi nhấn OK!");
    }
}



    function showNotification(message) {
        let note = document.getElementById("topNotification");
        if (!note) {
            note = document.createElement("div");
            note.id = "topNotification";
            note.className = "fixed top-0 left-0 w-full bg-green-500 text-white p-4 text-center font-semibold shadow-md z-50";
            document.body.prepend(note);
        }
        note.innerText = message;
        note.style.display = "block";
        setTimeout(() => note.style.display = "none", 3000);
    }

    // Cập nhật phí vận chuyển (nếu có dropdown chọn)
    function updateShippingFee(select) {
        const fee = parseInt(select.options[select.selectedIndex].dataset.fee || 0);
        document.querySelector('input[name="shipping_fee"]').value = fee;

        const baseTotal = {{ $total }};
        const total = baseTotal + fee;
        document.getElementById('totalPrice').innerText = total.toLocaleString('vi-VN') + " đ";
    }

    // function validateOrder() {
    //     if (!document.getElementById('selectedAddressId').value) {
    //         alert("Vui lòng chọn địa chỉ giao hàng!");
    //         return false;
    //     }
    //     return true;
    // }

    // Xử lý nút áp dụng mã giảm giá
    document.addEventListener('DOMContentLoaded', function () {
         function updateTotalPrice() {
    const toNumber = (str) => parseInt((str || '0').replace(/\D/g, ''));

    const cartTotal = toNumber(document.getElementById('cartTotal')?.innerText);
    const shippingFee = parseInt(document.getElementById('shippingFeeValue')?.value || 0);
    const orderDiscount = parseInt(document.getElementById('orderDiscountInput')?.value || 0);
    const shippingDiscount = parseInt(document.getElementById('shippingDiscountInput')?.value || 0);

    const total = Math.max(0, cartTotal + shippingFee - orderDiscount - shippingDiscount);

    document.getElementById('totalPrice').innerText = `${total.toLocaleString('vi-VN')} đ`;
}
    const applyBtn = document.createElement('button');
    applyBtn.type = 'button';
    applyBtn.textContent = 'Áp dụng';
    applyBtn.className = 'mt-2 bg-green-500 text-white py-1 px-3 rounded-md ml-2';
    document.querySelector('#couponInput').after(applyBtn);

        applyBtn.addEventListener('click', function () {
        const codes = document.getElementById('couponInput').value.trim();
        console.log('Mã giảm giá gửi đến backend:', codes);
        if (!codes) {
            document.getElementById('couponResult').innerText = "Vui lòng nhập mã.";
            return;
        }

        fetch('/check-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
        codes: codes, 
        order_total: document.getElementById('totalPrice').innerText.replace(/,/g, '')  // lấy giá trị tổng đơn hàng
    })

        })
        .then(res => res.json())
        .then(data => {
            console.log(data);  // In ra dữ liệu trả về từ server để kiểm tra

            const couponResult = document.getElementById('couponResult');

            // Kiểm tra nếu có success và các dữ liệu cần thiết
            if (data.valid_coupons && data.valid_coupons.length > 0) {
                let message = '';
                let totalDiscount = 0;
                let orderDiscount = 0;  // Giảm cho đơn hàng
                let shippingDiscount = 0;  // Giảm cho phí vận chuyển

                let orderCouponApplied = false;  // Biến kiểm tra mã giảm giá cho đơn hàng đã được áp dụng chưa
                let shippingCouponApplied = false;  // Biến kiểm tra mã giảm giá cho phí vận chuyển đã được áp dụng chưa

                // Lặp qua mảng valid_coupons và tính tổng giảm giá
                data.valid_coupons.forEach(item => {
                    let discountValue = parseFloat(item.discount_value); // Giá trị giảm giá
                    let maxDiscount = parseFloat(item.max_discount_value); // Giới hạn giảm giá tối đa
                    let discount;

                    // Kiểm tra nếu usage_count bằng usage_limit
                    if (item.usage_count >= item.usage_limit) {
                        message += `<span class="text-red-500">❌ Mã giảm giá ${item.code} đã hết lượt sử dụng.</span><br>`;
                        return; // Dừng lại nếu mã đã hết lượt sử dụng
                    }

                    // Kiểm tra loại giảm giá và tính toán
                    if (item.discount_type === "percentage") {
                        // Tính giá trị giảm giá theo phần trăm
                        discount = (parseFloat('{{ $total }}') * discountValue) / 100;

                        // Kiểm tra nếu discount vượt quá giới hạn giảm giá tối đa
                        if (maxDiscount && discount > maxDiscount) {
                            discount = maxDiscount;
                        }

                        // Phân biệt giảm giá cho đơn hàng và phí vận chuyển
                        if (item.apply_to === 'order') {
                            if (orderCouponApplied) {
                                message += `<span class="text-red-500">❌ Bạn chỉ được áp dụng 1 mã giảm giá cho đơn hàng.</span><br>`;
                                return;  // Dừng lại nếu đã có mã giảm giá cho đơn hàng
                            }
                            orderDiscount += discount;  // Giảm cho đơn hàng
                            orderCouponApplied = true;  // Đánh dấu đã áp dụng mã giảm giá cho đơn hàng
                        } else if (item.apply_to === 'shipping') {
                            if (shippingCouponApplied) {
                                message += `<span class="text-red-500">❌ Bạn chỉ được áp dụng 1 mã giảm giá cho phí vận chuyển.</span><br>`;
                                return;  // Dừng lại nếu đã có mã giảm giá cho phí vận chuyển
                            }
                            shippingDiscount += discount;  // Giảm cho phí vận chuyển
                            shippingCouponApplied = true;  // Đánh dấu đã áp dụng mã giảm giá cho phí vận chuyển
                        }
                    } else {
                        // Nếu là giảm giá cố định
                        discount = discountValue;

                        // Phân biệt giảm giá cho đơn hàng và phí vận chuyển
                        if (item.apply_to === 'order') {
                            if (orderCouponApplied) {
                                message += `<span class="text-red-500">❌ Bạn chỉ được áp dụng 1 mã giảm giá cho đơn hàng.</span><br>`;
                                return;  // Dừng lại nếu đã có mã giảm giá cho đơn hàng
                            }
                            orderDiscount += discount;  // Giảm cho đơn hàng
                            orderCouponApplied = true;  // Đánh dấu đã áp dụng mã giảm giá cho đơn hàng
                            document.getElementById('orderCouponIdInput').value = item.coupon_id;
                        } else if (item.apply_to === 'shipping') {
                            if (shippingCouponApplied) {
                                message += `<span class="text-red-500">❌ Bạn chỉ được áp dụng 1 mã giảm giá cho phí vận chuyển.</span><br>`;
                                return;  // Dừng lại nếu đã có mã giảm giá cho phí vận chuyển
                            }
                            shippingDiscount += discount;  // Giảm cho phí vận chuyển
                            shippingCouponApplied = true;  // Đánh dấu đã áp dụng mã giảm giá cho phí vận chuyển
                            document.getElementById('shippingCouponIdInput').value = item.coupon_id;
                        }
                    }

                    message += `✔️ ${item.code}: Giảm ${discount.toLocaleString('vi-VN')} đ<br>`;
                    totalDiscount += discount;
                });

                // Cập nhật giá trị giảm giá vào input ẩn
                document.getElementById('orderDiscountInput').value = orderDiscount;
                document.getElementById('shippingDiscountInput').value = shippingDiscount;

                // Cập nhật giá trị của các phần tử HTML ngay lập tức
                document.getElementById('orderDiscount').innerText = `${orderDiscount.toLocaleString('vi-VN')} đ`;
                document.getElementById('shippingDiscount').innerText = `${shippingDiscount.toLocaleString('vi-VN')} đ`;

                // Cập nhật lại tổng tiền sau khi áp dụng các giảm giá
                updateTotalPrice();

                // Hiển thị kết quả giảm giá
                couponResult.innerHTML = `
                    ${message}
                    <br>
                    <strong>Giảm cho đơn hàng: ${orderDiscount.toLocaleString('vi-VN')} đ</strong><br>
                    <strong>Giảm cho phí vận chuyển: ${shippingDiscount.toLocaleString('vi-VN')} đ</strong><br>
                    <strong>Tổng giảm: ${totalDiscount.toLocaleString('vi-VN')} đ</strong>
                `;
                setTimeout(() => {
                    couponResult.innerHTML = '';
                }, 5000);

                // Xóa các input cũ (nếu người dùng áp lại mã mới)
                document.querySelectorAll('.applied-coupon').forEach(el => el.remove());

                // Chèn các mã hợp lệ vào form
                data.valid_coupons.forEach(item => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'coupons[]';
                    input.value = item.code;
                    input.classList.add('applied-coupon'); // để tiện xóa sau
                    document.querySelector('form').appendChild(input);
                });
                 
            } else {
    const couponResult = document.getElementById('couponResult');

    let errorMessage = "Lỗi không xác định"; // Mặc định

    // Ưu tiên lấy message cụ thể từ backend (nếu có)
    if (data.message) {
        errorMessage = data.message;
    }

    // Nếu backend có mảng errors, nối lại để hiển thị tất cả
    if (data.errors && Array.isArray(data.errors) && data.errors.length > 0) {
        errorMessage = data.errors.join('<br>');
    }

    // Hiển thị lỗi
    couponResult.innerHTML = `<span class="text-red-500">❌ ${errorMessage}</span>`;
}

        })
        .catch(error => {
            console.error("Lỗi:", error);
            document.getElementById('couponResult').innerHTML = `<span class="text-red-500">Đã xảy ra lỗi khi kiểm tra mã.</span>`;
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    
    if (!citySelect || !districtSelect || !wardSelect) {
        console.warn('Các select chưa xuất hiện trong DOM.');
        return;
    }

    // Load Tỉnh/Thành phố
    fetch('https://provinces.open-api.vn/api/?depth=1')
        .then(response => response.json())
        .then(data => {
            data.forEach(function (province) {
                let option = document.createElement("option");
                option.value = province.code; // dùng "code" chứ không phải "id"
                option.textContent = province.name;
                citySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching provinces:', error));

    // Khi chọn Tỉnh => load Quận/Huyện
    citySelect.addEventListener('change', function () {
        const selectedCityCode = this.value;
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn xã</option>';

        if (selectedCityCode) {
            fetch(`https://provinces.open-api.vn/api/p/${selectedCityCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    data.districts.forEach(function (district) {
                        let option = document.createElement("option");
                        option.value = district.code;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching districts:', error));
        }
    });

    // Khi chọn Quận/Huyện => load Xã/Phường
    districtSelect.addEventListener('change', function () {
        const selectedDistrictCode = this.value;
        wardSelect.innerHTML = '<option value="">Chọn xã</option>';

        if (selectedDistrictCode) {
            fetch(`https://provinces.open-api.vn/api/d/${selectedDistrictCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    data.wards.forEach(function (ward) {
                        let option = document.createElement("option");
                        option.value = ward.code;
                        option.textContent = ward.name;
                        wardSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching wards:', error));
        }
    });
});


    });
  function updateShippingFee(addressId) {
    console.log("Đang gọi API cập nhật phí vận chuyển với address_id:", addressId);

    fetch(`/update-shipping-fee`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            // Đảm bảo CSRF token nếu cần
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            address_id: addressId
        })
    })
    .then(response => {
        console.log("API response status:", response.status);
        
        // Kiểm tra nếu response không phải 200 (OK)
        if (!response.ok) {
            throw new Error('Không thể lấy phí vận chuyển');
        }

        return response.json();
    })
    .then(data => {
        console.log('Dữ liệu trả về từ API:', data);

        // Kiểm tra xem dữ liệu có chứa phí vận chuyển và shipping_id
        if (data.shipping_fee && data.shipping_id) {
            // Cập nhật phí vận chuyển vào UI (giả sử bạn có phần tử #shippingFeeText để hiển thị)
            const shippingFeeText = document.getElementById('shippingFeeText');
            if (shippingFeeText) {
                 shippingFeeText.innerText = `Phí vận chuyển: ${parseFloat(data.shipping_fee).toLocaleString()} đ`;
            } else {
                console.error("Không tìm thấy phần tử #shippingFeeText.");
            }

            // Cập nhật các giá trị hidden input nếu có
            const shippingFeeValue = document.getElementById('shippingFeeValue');
            if (shippingFeeValue) {
                shippingFeeValue.value = data.shipping_fee;
            }

            const shippingId = document.getElementById('shippingId');
            if (shippingId) {
                shippingId.value = data.shipping_id;
            }
               // 🟢 Gọi hàm cập nhật tổng tiền ngay sau khi cập nhật phí ship
        updateTotalPrice();

        } else {
            console.error("Dữ liệu trả về không đúng. Không có phí vận chuyển hoặc ID giao hàng.");
        }
    })
    .catch(error => {
        console.error('Lỗi khi gọi API:', error);
    });
  function updateTotalPrice() {
    const toNumber = (str) => parseInt((str || '0').replace(/\D/g, ''));

    const cartTotal = toNumber(document.getElementById('cartTotal')?.innerText);
    const shippingFee = parseInt(document.getElementById('shippingFeeValue')?.value || 0);
    const orderDiscount = parseInt(document.getElementById('orderDiscountInput')?.value || 0);
    const shippingDiscount = parseInt(document.getElementById('shippingDiscountInput')?.value || 0);

    const total = Math.max(0, cartTotal + shippingFee - orderDiscount - shippingDiscount);

    document.getElementById('totalPrice').innerText = `${total.toLocaleString('vi-VN')} đ`;
}



}
</script>
