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
                    <strong>{{ $defaultAddress->address_name }}</strong> - {{ $defaultAddress->recipient_name }} <br>
                    {{ $defaultAddress->street_address }}, {{ $defaultAddress->ward }}, {{ $defaultAddress->district }}, {{ $defaultAddress->city }}
                    <input type="hidden" name="address_id" id="selectedAddressId" value="{{ $defaultAddress->address_id }}">
                @else
                    <span class="text-red-500">Bạn chưa chọn địa chỉ giao hàng.</span>
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
                <option value="bank_transfer">Chuyển khoản</option>
                <option value="credit_card">Thẻ tín dụng</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>

        <!-- Phí vận chuyển -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Phí vận chuyển</h3>
            <select name="shipping_id" class="w-full p-2 border border-gray-300 rounded-md" onchange="updateShippingFee(this)">
                @foreach($shippingFees as $fee)
                    <option value="{{ $fee->shipping_id }}" data-fee="{{ $fee->fee }}">
                        {{ number_format($fee->fee, 0, ',', '.') }} đ
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="shipping_fee" id="shipping_fee" value="{{ $shippingFees->first()->fee ?? 10000 }}">
        </div>

        <!-- Thông tin giỏ hàng -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Thông tin giỏ hàng</h3>
            <ul class="space-y-2">
                @foreach($cartItems as $item)
                    @php
                        $price = $item->variant->price_sale ?? $item->variant->price ?? $item->product->price_sale ?? $item->product->price;
                    @endphp
                    <li>
                        <span class="font-semibold">{{ $item->product->name }}</span>
                        - Số lượng: {{ $item->quantity }} x {{ number_format($price, 0, ',', '.') }} đ =
                        <span class="font-bold">{{ number_format($price * $item->quantity, 0, ',', '.') }} đ</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-4 text-lg font-bold">
                Tổng tiền: <span id="totalPrice">{{ number_format($total + ($shippingFees->first()->fee ?? 0), 0, ',', '.') }}</span> đ
            </p>
        </div>

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

<div id="addressFormPopup" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center" >
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
                <input type="text" id="recipientPhone" name="recipient_phone" class="w-full p-2 border rounded-md" required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Xã:</label>
                <input type="text" id="ward" name="ward" class="w-full p-2 border rounded-md" required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Quận/Huyện:</label>
                <input type="text" id="district" name="district" class="w-full p-2 border rounded-md" required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Thành phố:</label>
                <input type="text" id="city" name="city" class="w-full p-2 border rounded-md" required>
            </div>

            <div class="mt-4 flex justify-between">
                <button type="button" onclick="saveAddress()" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                    Lưu
                </button>
                <button type="button" onclick="closeAddressForm()" class="bg-gray-400 text-white px-4 py-2 rounded-md">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Popup danh sách địa chỉ -->
<div id="addressPopup" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-md w-96">
        <h2 class="text-xl font-semibold mb-4">Thay đổi địa chỉ nhận hàng</h2>
        <div id="addressList">
            @foreach($user->userAddresses as $address)
                <div class="border p-4 rounded-md mb-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="address_id" value="{{ $address->address_id }}"
                            {{ $address->is_default ? 'checked' : '' }} onchange="selectAddress(this)">
                        <span class="ml-2">
                            <strong>{{ $address->address_name }}</strong> - {{ $address->recipient_name }} <br>
                            {{ $address->street_address }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                        </span>
                    </label>
                    <button class="bg-yellow-500 text-black text-sm px-2 py-1 rounded-md"
                    onclick="editAddress({{ $address->address_id }}, '{{ $address->address_name }}', '{{ $address->recipient_name }}', '{{ $address->street_address }}', '{{ $address->ward }}', '{{ $address->district }}', '{{ $address->city }}')">
                    Sửa
                </button>                
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex justify-end gap-4">
            <button onclick="confirmAddressSelection()" 
                class="bg-blue-500 text-white px-4 py-2 rounded-md">
                Chọn địa chỉ làm mặc định
            </button>
            <button onclick="openAddAddressForm()" class="bg-green-500 text-black px-4 py-2 rounded-md">
                Thêm địa chỉ mới
            </button>
        </div>
        
    </div>
</div>

    <script>
        
    // Mở popup danh sách địa chỉ
    function openAddressPopup() {
        document.getElementById('addressPopup').classList.remove('hidden');
    }

    // Ẩn popup danh sách khi thêm địa chỉ
    function openAddAddressForm() {
        let addressCount = document.querySelectorAll("#addressList > div").length;
        
        if (addressCount >= 3) {
            alert("Bạn chỉ có thể lưu tối đa 3 địa chỉ!");
            return;
        }

        document.getElementById("addressPopup").classList.add("hidden");
        document.getElementById("addressFormPopup").classList.remove("hidden");

        // Reset form về trạng thái thêm mới
        document.getElementById("addressFormTitle").innerText = "Thêm địa chỉ mới";
        document.getElementById("addressId").value = "";
        document.getElementById("addressName").value = "";
        document.getElementById("recipientName").value = "";
        document.getElementById("streetAddress").value = "";
        document.getElementById("ward").value = "";
        document.getElementById("district").value = "";
        document.getElementById("city").value = "";
    }


    // Ẩn popup danh sách khi sửa địa chỉ
    function editAddress(id, name, recipient, street, ward, district, city) {
        document.getElementById("addressPopup").classList.add("hidden");
        document.getElementById("addressFormPopup").classList.remove("hidden");

        document.getElementById("addressFormTitle").innerText = "Chỉnh sửa địa chỉ";
        document.getElementById("addressId").value = id;
        document.getElementById("addressName").value = name;
        document.getElementById("recipientName").value = recipient;
        document.getElementById("streetAddress").value = street;
        document.getElementById("ward").value = ward;
        document.getElementById("district").value = district;
        document.getElementById("city").value = city;
    }

    // Đóng popup form nhập địa chỉ và quay lại danh sách
    function closeAddressForm() {
        document.getElementById("addressFormPopup").classList.add("hidden");
        document.getElementById("addressPopup").classList.remove("hidden");
    }

    // Đóng popup danh sách địa chỉ
    function closeAddressPopup() {
        document.getElementById("addressPopup").classList.add("hidden");
    }


    function saveAddress() {
        let form = document.getElementById("addressForm");
        let formData = new FormData(form);

        fetch("{{ route('address.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            return response.json().catch(() => {
                return response.text().then(text => {
                    throw new Error("Server response is not JSON: " + text);
                });
            });
        })
        .then(data => {
            console.log("Server Response:", data);
            if (data.success) {
                alert("Địa chỉ đã được lưu!");
                updateAddressList(data.newAddress);
                document.getElementById("addressFormPopup").classList.add("hidden");
                document.getElementById("addressPopup").classList.remove("hidden");
            } else {
                alert("Lỗi khi lưu địa chỉ: " + data.message);
            }
        })
        .catch(error => console.error("Lỗi:", error));
    }






    function updateAddressList(newAddress) {
        let addressList = document.getElementById("addressList");
        
        let newAddressHTML = `
            <div class="border p-4 rounded-md mb-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="address_id" value="${newAddress.address_id}" onchange="selectAddress(this)">
                    <span class="ml-2">
                        <strong>${newAddress.address_name}</strong> - ${newAddress.recipient_name} <br>
                        ${newAddress.street_address}, ${newAddress.ward}, ${newAddress.district}, ${newAddress.city}
                    </span>
                </label>
                <button class="bg-yellow-500 text-black text-sm px-2 py-1 rounded-md"
                    onclick="editAddress(${newAddress.address_id}, '${newAddress.address_name}', '${newAddress.recipient_name}', '${newAddress.street_address}', '${newAddress.ward}', '${newAddress.district}', '${newAddress.city}')">
                    Sửa
                </button>
            </div>
        `;

        addressList.innerHTML = newAddressHTML + addressList.innerHTML;

        // Kiểm tra số lượng địa chỉ và ẩn nút "Thêm địa chỉ mới" nếu đủ 3
        let addressCount = document.querySelectorAll("#addressList > div").length;
        if (addressCount >= 3) {
            document.querySelector("button[onclick='openAddAddressForm()']").style.display = "none";
        }
    }



    function selectAddress(radio) {
    let selectedAddressId = document.getElementById('selectedAddressId');
    let selectedAddressDiv = document.getElementById('selectedAddress');

    if (!selectedAddressDiv) {
        console.error("Không tìm thấy phần tử selectedAddress.");
        return;
    }

    if (!selectedAddressId) {
        selectedAddressId = document.createElement('input');
        selectedAddressId.type = "hidden";
        selectedAddressId.id = "selectedAddressId";
        selectedAddressId.name = "address_id";
        selectedAddressDiv.appendChild(selectedAddressId);
    }

    let selectedText = radio.closest('div').querySelector('span').innerHTML;
    selectedAddressDiv.innerHTML = `<div>${selectedText}</div>`;
    selectedAddressId.value = radio.value;
}

    function confirmAddressSelection() {
        let selectedRadio = document.querySelector('input[name="address_id"]:checked');

        if (selectedRadio) {
            let addressDiv = selectedRadio.closest('div');
            let selectedText = addressDiv.querySelector('span').innerHTML;

            console.log("Địa chỉ được chọn:", selectedText);

            // Cập nhật địa chỉ hiển thị trên giao diện
            document.getElementById('selectedAddress').innerHTML = `
                <div>${selectedText}</div>
                <input type="hidden" name="address_id" id="selectedAddressId" value="${selectedRadio.value}">
            `;

            showNotification("Thay đổi địa chỉ thành công!");

            closeAddressPopup();
        } else {
            alert("Vui lòng chọn địa chỉ trước khi nhấn OK!");
        }
    }




    // Hàm đóng popup
    function closeAddressPopup() {
        document.getElementById('addressPopup').classList.add('hidden');
    }

    function showNotification(message) {
        let notification = document.getElementById("topNotification");

        if (!notification) {
            notification = document.createElement("div");
            notification.id = "topNotification";
            notification.className = "fixed top-0 left-0 w-full bg-green-500 text-white p-4 text-center font-semibold shadow-md";
            document.body.prepend(notification);
        }

        notification.innerText = message;
        notification.style.display = "block";

        // Ẩn sau 5 giây
        setTimeout(() => {
            notification.style.display = "none";
        }, 3000);
    }




    function updateShippingFee(select) {
        let fee = select.options[select.selectedIndex].getAttribute('data-fee');
        document.getElementById('shipping_fee').value = fee;
        let totalPrice = {{ $total }} + parseInt(fee);
        document.getElementById('totalPrice').innerText = totalPrice.toLocaleString('vi-VN') + " đ";
    }

    function validateOrder() {
        if (!document.getElementById('selectedAddressId').value) {
            alert("Vui lòng chọn địa chỉ giao hàng!");
            return false;
        }
        return true;
    }
    </script>
