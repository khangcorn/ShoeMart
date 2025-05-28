@extends('client.layout')

@section('content')
    {{-- @if (session('success'))
    <div class="bg-green-500 text-white p-4 rounded-md">
        {{ session('success') }}
    </div>
@endif --}}
    @if (session('success'))
        <div id="topNotification" class="bg-green-500 text-white p-4 text-center font-semibold">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('topNotification').style.display = 'none';
            }, 5000);
        </script>
    @endif

    @if (session('error'))
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
      
        <h2 class="text-2xl font-semibold mb-4">Thông tin thanh toán</h2>

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
                    @if ($defaultAddress)
                        <span>{{ $defaultAddress->street_address }}, {{ $defaultAddress->ward }},
                            {{ $defaultAddress->district }}, {{ $defaultAddress->city }}</span>
                        <input type="hidden" name="address_id" id="selectedAddressId"
                            value="{{ $defaultAddress->address_id }}">
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
                    <option value="vnpay">Thanh toán qua VNPay</option>
                </select>
            </div>

            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Mã giảm giá</h3>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <input type="text" name="codes" id="couponInput"
                        class="w-full sm:w-1/2 p-2 border border-gray-300 rounded-md"
                        placeholder="Nhập mã giảm giá (cách nhau dấu phẩy)">
                </div>

                <!-- Popup -->
                <div x-data="{
                    open: false,
                    selectedOrderCoupon: null,
                    selectedShippingCoupon: null,
                    initSelectedCoupons() {
                        // Lấy mảng codes từ input, loại bỏ khoảng trắng
                        let codes = (document.getElementById('couponInput').value || '')
                            .split(',')
                            .map(c => c.trim())
                            .filter(c => c);
                        // Tìm xem mỗi code có radio tương ứng hay không
                        this.selectedOrderCoupon = codes.find(c =>
                            document.querySelector(`input[name='orderCoupon'][value='${c}']`)
                        ) || null;
                        this.selectedShippingCoupon = codes.find(c =>
                            document.querySelector(`input[name='shippingCoupon'][value='${c}']`)
                        ) || null;
                    },
                }">
                    <button type="button" @click="initSelectedCoupons(); open = true"
                        class="mt-2 px-4 py-2 bg-green-600 text-black rounded hover:bg-green-700">
                        📜 Xem danh sách giảm giá
                    </button>

                    <div x-show="open" x-transition
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                        style="display:none" @keydown.escape.window="open = false" @click.outside="open = false">
                        <div
                            class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto relative">
                            <button type="button" @click="open = false"
                                class="absolute top-2 right-2 text-2xl font-bold hover:text-gray-700">&times;</button>

                            <!-- Nội dung có thể scroll -->
                            <div class="p-6 overflow-y-auto" style="max-height: 90vh;">
                                <h2 class="text-2xl font-semibold mb-4">Danh sách mã giảm giá</h2>

                                @php
                                    $orderCoupons = $coupons->where('apply_to', 'order');
                                    $shippingCoupons = $coupons->where('apply_to', 'shipping');
                                @endphp

                                {{-- Mã giảm giá cho đơn hàng --}}
                                <h3 class="text-xl font-bold mt-6 mb-2">🎁 Mã giảm cho đơn hàng</h3>
                                @if ($orderCoupons->isEmpty())
                                    <p class="text-gray-500">Hiện không có mã giảm giá cho đơn hàng.</p>
                                @else
                                    <ul class="space-y-4">
                                        @foreach ($orderCoupons as $coupon)
                                            @php
                                                $disabled =
                                                    $coupon->status !== 'active' ||
                                                    $coupon->usage_limit == 0 ||
                                                    $coupon->usage_count >= $coupon->usage_limit ||
                                                    \Carbon\Carbon::parse($coupon->expiration_date)->isPast();
                                            @endphp
                                            <li class="border p-4 rounded-md shadow-sm flex items-center justify-between">
                                                <label class="flex flex-row-reverse items-center cursor-pointer flex-1"
                                                    :class="{ 'opacity-50 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }} }">
                                                    <input type="radio" name="orderCoupon"
                                                        :disabled="{{ $disabled ? 'true' : 'false' }}"
                                                        x-model="selectedOrderCoupon"
                                                        @click="selectedOrderCoupon = selectedOrderCoupon === '{{ $coupon->code }}' ? null : '{{ $coupon->code }}'"
                                                        value="{{ $coupon->code }}" class="ml-3" />

                                                    <div>
                                                        <h4 class="text-lg font-bold">Mã voucher: {{ $coupon->code }}</h4>
                                                        <p class="text-sm">
                                                            Giảm giá
                                                            @if ($coupon->discount_type === 'percentage')
                                                                {{ intval($coupon->discount_value) }}%
                                                                @if ($coupon->max_discount_value)
                                                                    (Tối đa
                                                                    {{ number_format($coupon->max_discount_value, 0, ',', '.') }}đ)
                                                                @endif
                                                            @elseif ($coupon->discount_type === 'fixed')
                                                                tối đa:
                                                                {{ number_format($coupon->discount_value, 0, ',', '.') }}đ
                                                            @else
                                                                Không rõ loại giảm giá
                                                            @endif
                                                        </p>

                                                        @if ($coupon->min_order_value)
                                                            <p class="text-sm text-gray-500">Dành cho đơn hàng từ
                                                                {{ number_format($coupon->min_order_value, 0, ',', '.') }}đ
                                                            </p>
                                                        @endif

                                                        @if ($coupon->status !== 'active')
                                                            <p class="text-xs text-red-500">Mã giảm giá không hoạt động</p>
                                                        @elseif($coupon->usage_limit == 0 || $coupon->usage_count >= $coupon->usage_limit)
                                                            <p class="text-xs text-red-500">Đã hết lượt sử dụng</p>
                                                        @elseif(\Carbon\Carbon::parse($coupon->expiration_date)->isPast())
                                                            <p class="text-xs text-red-500">Đã quá hạn</p>
                                                        @else
                                                            <p class="text-xs text-green-500">Số lượng có hạn</p>
                                                        @endif

                                                        <p class="text-xs text-gray-500">
                                                            Hết hạn:
                                                            {{ \Carbon\Carbon::parse($coupon->expiration_date)->format('d/m/Y H:i:s') }}
                                                        </p>
                                                    </div>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                {{-- Mã giảm giá cho phí vận chuyển --}}
                                <h3 class="text-xl font-bold mt-10 mb-2">🚚 Mã giảm cho phí vận chuyển</h3>
                                @if ($shippingCoupons->isEmpty())
                                    <p class="text-gray-500">Hiện không có mã giảm giá cho phí vận chuyển.</p>
                                @else
                                    <ul class="space-y-4">
                                        @foreach ($shippingCoupons as $coupon)
                                            @php
                                                $disabled =
                                                    $coupon->status !== 'active' ||
                                                    $coupon->usage_limit == 0 ||
                                                    $coupon->usage_count >= $coupon->usage_limit ||
                                                    \Carbon\Carbon::parse($coupon->expiration_date)->isPast();
                                            @endphp
                                            <li class="border p-4 rounded-md shadow-sm flex items-center justify-between">
                                                <div>
                                                    <label class="flex items-center cursor-pointer"
                                                        :class="{ 'opacity-50 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }} }">
                                                        <input type="radio" name="shippingCoupon"
                                                            :disabled="{{ $disabled ? 'true' : 'false' }}"
                                                            x-model="selectedShippingCoupon"
                                                            @click="selectedShippingCoupon = selectedShippingCoupon === '{{ $coupon->code }}' ? null : '{{ $coupon->code }}'"
                                                            value="{{ $coupon->code }}" class="ml-3" />

                                                        <div>
                                                            <h4 class="text-lg font-bold">Mã voucher: {{ $coupon->code }}
                                                            </h4>
                                                            <p class="text-sm">
                                                                Giảm giá:
                                                                @if ($coupon->discount_type === 'percentage')
                                                                    {{ intval($coupon->discount_value) }}%
                                                                    @if ($coupon->max_discount_value)
                                                                        (Tối đa
                                                                        {{ number_format($coupon->max_discount_value, 0, ',', '.') }}đ)
                                                                    @endif
                                                                @elseif ($coupon->discount_type === 'fixed')
                                                                    {{ number_format($coupon->discount_value, 0, ',', '.') }}đ
                                                                @else
                                                                    Không rõ loại giảm giá
                                                                @endif
                                                            </p>

                                                            @if ($coupon->min_order_value)
                                                                <p class="text-sm text-gray-500">Dành cho đơn hàng từ
                                                                    {{ number_format($coupon->min_order_value, 0, ',', '.') }}đ
                                                                </p>
                                                            @endif

                                                            @php
                                                                $expirationDate = \Carbon\Carbon::parse(
                                                                    $coupon->expiration_date,
                                                                );
                                                            @endphp

                                                            @if ($coupon->usage_limit == 0 || $coupon->usage_count >= $coupon->usage_limit)
                                                                <p class="text-xs text-red-500">Đã hết lượt sử dụng</p>
                                                            @elseif($expirationDate->isPast())
                                                                <p class="text-xs text-red-500">Đã quá hạn</p>
                                                            @else
                                                                <p class="text-xs text-green-500">Số lượng có hạn</p>
                                                            @endif

                                                            <p class="text-xs text-gray-500">
                                                                Hết hạn: {{ $expirationDate->format('d/m/Y H:i:s') }}
                                                            </p>
                                                        </div>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif



                                <div class="mt-6 flex justify-end">
                                    <button type="button" id="btnApplyPopup"
                                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Áp dụng mã giảm giá
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div id="couponResult" class="mt-3 text-sm text-gray-700"></div>
                <!-- Các input ẩn để backend nhận coupon_id, discount -->
                <input type="hidden" id="orderCouponIdInput" name="order_coupon_id" value="">
                <input type="hidden" id="shippingCouponIdInput" name="shipping_coupon_id" value="">
                <input type="hidden" id="orderDiscountInput" name="order_discount" value="0">
                <input type="hidden" id="shippingDiscountInput" name="shipping_discount" value="0">


                <!-- Thông tin giỏ hàng -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">Thông tin giỏ hàng</h3>
                    <ul class="space-y-2">
                        @foreach ($cartItems as $item)
                            @php
                                $price =
                                    $item->variant->price_sale ??
                                    ($item->variant->price ?? ($item->product->price_sale ?? $item->product->price));
                            @endphp
                            <li class="flex items-center gap-4">
                                <a href="{{ route('products.detail', $item->product->product_id) }}" target="_blank">

                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_url) }}"
                                        alt="{{ $item->product->name }}" class="w-20 h-20 mr-4">
                                </a>

                                <div>
                                    <span class="font-semibold">{{ $item->product->name }}</span>
                                    - Số lượng: {{ $item->quantity }} x {{ number_format($price, 0, ',', '.') }} đ =
                                    <span class="font-bold">{{ number_format($price * $item->quantity, 0, ',', '.') }}
                                        đ</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="order-summary mt-4">
                        <!-- Tổng tiền giỏ hàng -->
                        <p class="summary-item text-lg font-semibold">
                            Tổng tiền giỏ hàng:
                            <span id="cartTotal" class="amount">
                                {{ number_format($total, 0, ',', '.') }} đ
                            </span>
                        </p>

                        <!-- Phí vận chuyển -->
                        <div class="shipping-info">
                            <p id="shippingFeeText" class="summary-item text-lg font-semibold">Phí vận chuyển:
                                @if ($shippingFeeValue > 0)
                                    {{ number_format($shippingFeeValue, 0, ',', '.') }} đ
                                @else
                                    Phí vận chuyển không xác định.
                                @endif
                            </p>

                            <input type="hidden" id="shippingFeeValue" name="shipping_fee"
                                value="{{ $shippingFeeValue }}">
                            <input type="hidden" id="shippingId" name="shipping_id" value="{{ $shippingId }}">
                        </div>

                        <!-- Giảm giá đơn hàng -->
                        <p class="summary-item text-lg font-semibold">
                            Giảm giá đơn hàng:
                            <span id="orderDiscount" class="discount">
                                {{ number_format($orderDiscount ?? 0, 0, ',', '.') }} đ
                            </span>
                        </p>

                        <!-- Giảm giá phí vận chuyển -->
                        <p class="summary-item text-lg font-semibold">
                            Giảm giá phí vận chuyển:
                            <span id="shippingDiscount" class="discount">
                                {{ number_format($shippingDiscount ?? 0, 0, ',', '.') }} đ
                            </span>
                        </p>

                        <!-- Tổng tiền thanh toán -->
                        <p class="summary-item mt-4 text-lg font-semibold">
                            <span>Tổng tiền thanh toán:</span>
                            <span id="totalPrice" class="total-price">
                                {{ number_format(
                                    max(0, $total + ($shippingFeeValue ?? 0) - ($orderDiscount ?? 0) - ($shippingDiscount ?? 0)),
                                    0,
                                    ',',
                                    '.',
                                ) }}
                                đ
                            </span>
                        </p>
                    </div>

                    </p>


                </div>


                @foreach ($cartDetailIds as $id)
                    <input type="hidden" name="cart_detail_ids[]" value="{{ $id }}">
                @endforeach

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded-md"
                        onclick="return validateOrder()">
                        Xác nhận đơn hàng
                    </button>
                    <a href="{{ route('cart.index') }}"
                        class="bg-gray-300 text-gray-800 py-2 px-6 rounded-md hover:bg-gray-400 transition">
                        Quay lại giỏ hàng
                    </a>
                </div>
        </form>

    </div>
@endsection

<div id="addressFormPopup" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-md w-full max-w-3xl">
        <h2 id="addressFormTitle" class="text-xl font-semibold mb-4">Thêm địa chỉ mới</h2>

        <form id="addressForm">
            @csrf
            <input type="hidden" id="addressId" name="address_id">

            <div class="mb-2">
                <label class="block font-semibold">Tên địa chỉ:</label>
                <input type="text" id="addressName" name="address_name" class="w-full p-2 border rounded-md"
                    required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Người nhận:</label>
                <input type="text" id="recipientName" name="recipient_name" class="w-full p-2 border rounded-md"
                    required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Địa chỉ:</label>
                <input type="text" id="streetAddress" name="street_address" class="w-full p-2 border rounded-md"
                    required>
            </div>
            <div class="mb-2">
                <label class="block font-semibold">Số điện thoại:</label>
                <input type="tel" id="recipientPhone" name="recipient_phone"
                    class="w-full p-2 border rounded-md" required>
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
                <button type="button" onclick="closeAddressForm()"
                    class="bg-gray-400 text-black px-4 py-2 rounded-md">
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
    <div class="bg-white p-6 rounded-md w-full max-w-3xl">

        <h2 class="text-xl font-semibold mb-4">Thay đổi địa chỉ nhận hàng</h2>
        <div id="addressList">
            @foreach ($user->userAddresses as $address)
                <div class="border p-4 rounded-md mb-2 address-item" data-address-id="{{ $address->address_id }}">
                    <label class="inline-flex items-center">
                        <input type="radio" name="address_id" value="{{ $address->address_id }}"
                            {{ $address->is_default ? 'checked' : '' }} onchange="selectAddress(this)">
                        <span class="ml-2">
                            <strong>{{ $address->address_name }}</strong> - {{ $address->recipient_name }} <br>
                            {{ $address->street_address }}, {{ $address->ward }}, {{ $address->district }},
                            {{ $address->city }}
                        </span>
                    </label>
                    <button class="bg-yellow-500 text-black text-sm px-2 py-1 rounded-md"
                        onclick="editAddress(
                                    {{ $address->address_id }},
                                    '{{ $address->address_name ?? '' }}',
                                    '{{ $address->recipient_name ?? '' }}',
                                    '{{ $address->recipient_phone ?? '' }}',
                                    '{{ $address->street_address ?? '' }}',
                                    '{{ $address->ward ?? '' }}',
                                    '{{ $address->district ?? '' }}',
                                    '{{ $address->city ?? '' }}'
                                )">
                        Sửa
                    </button>

                </div>
            @endforeach
        </div>
        <!-- Báo lỗi khi chưa có địa chỉ -->
        @if (empty($address))
            <span id="addressError" class="text-red-500">Bạn chưa có địa chỉ giao hàng.</span>
            <input type="hidden" name="address_id" id="selectedAddressId" value="">
        @endif

        <!-- Các nút hành động -->
        <div class="mt-4 flex justify-end gap-4 flex-wrap" id="addressButtonsWrapper">
            <button onclick="openAddAddressForm()" class="bg-green-500 text-black px-4 py-2 rounded-md">
                Thêm địa chỉ mới
            </button>

            <div id="addressActionButtons" class="{{ empty($address) ? 'hidden' : '' }} flex gap-4">
                <button onclick="confirmAddressSelection()" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                    Chọn địa chỉ làm mặc định
                </button>
            </div>
            <button type="button" onclick="closeAddressPopup()"
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
        loadWards(selectedCity, null); // Reset xã/phường khi đổi tỉnh thành
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
        ['addressId', 'addressName', 'recipientName', 'streetAddress', 'recipientPhone', 'ward', 'district', 'city']
        .forEach(id => {
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
                    <span class="recipient-phone">${newAddress.recipient_phone}</span>,  
                    <span class="ward">${newAddress.ward}</span>, 
                    <span class="district">${newAddress.district}</span>, 
                    <span class="city">${newAddress.city}</span>
                </span>
            </label>
                <button class="bg-yellow-500 text-black text-sm px-2 py-1 rounded-md"
                onclick="
                editAddress(
                    ${newAddress.address_id},
                    '${(newAddress.address_name || '').replace(/'/g, "\\'")}',
                    '${(newAddress.recipient_name || '').replace(/'/g, "\\'")}',
                    '${(newAddress.recipient_phone || '').replace(/'/g, "\\'")}',
                    '${(newAddress.street_address || '').replace(/'/g, "\\'")}',
                    '${(newAddress.ward || '').replace(/'/g, "\\'")}',
                    '${(newAddress.district || '').replace(/'/g, "\\'")}',
                    '${(newAddress.city || '').replace(/'/g, "\\'")}'
                )
                ">
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
            note.className =
                "fixed top-0 left-0 w-full bg-green-500 text-white p-4 text-center font-semibold shadow-md z-50";
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

    document.addEventListener('DOMContentLoaded', () => {
        async function applyCoupons(codes) {
            const toNumber = str => parseInt((str || '0').replace(/\D/g, ''), 10);
            const totalPriceEl = document.getElementById('totalPrice');
            const orderTotal = toNumber(totalPriceEl?.innerText);

            if (!codes) {
                document.getElementById('couponResult').innerText = 'Vui lòng nhập hoặc chọn mã.';
                return;
            }

            try {
                const res = await fetch('/check-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        codes: codes,
                        order_total: orderTotal
                    })
                });
                const data = await res.json();
                const resultEl = document.getElementById('couponResult');

                if (data.valid_coupons?.length) {
                    document.querySelectorAll('.applied-coupon').forEach(el => el.remove());

                    let msg = '',
                        totalDisc = 0;
                    let orderDisc = 0,
                        shippingDisc = 0;
                    let usedOrder = false,
                        usedShip = false;
                    const cartTotal = toNumber(document.getElementById('cartTotal')?.innerText);
                    const shippingFeeValue = document.getElementById('shippingFeeValue')?.value || '0';
                    const shippingFee = parseInt(shippingFeeValue, 10) || 0;

                    for (const item of data.valid_coupons) {
                        if (item.usage_count >= item.usage_limit) {
                            msg += `<span class="text-red-500">❌ ${item.code} đã hết lượt.</span><br>`;
                            continue;
                        }

                        let disc = 0;
                        const percentage = parseFloat(item.discount_value);
                        const maxDiscount = parseFloat(item.max_discount_value ?? 0);

                        if (item.discount_type === 'percentage') {
                            if (item.apply_to === 'order') {
                                disc = (cartTotal * percentage) / 100;
                                if (!isNaN(maxDiscount) && maxDiscount > 0 && disc > maxDiscount) {
                                    disc = maxDiscount;
                                }
                                // Giới hạn không vượt quá tổng đơn
                                if (disc > cartTotal) {
                                    disc = cartTotal;
                                }

                            } else if (item.apply_to === 'shipping') {
                                if (shippingFee <= 0) {
                                    msg +=
                                        `<span class="text-red-500">❌ Mã ${item.code}: vui lòng chọn địa chỉ để tính phí ship trước.</span><br>`;
                                    continue;
                                }

                                disc = (shippingFee * percentage) / 100;
                                if (!isNaN(maxDiscount) && maxDiscount > 0 && disc > maxDiscount) {
                                    disc = maxDiscount;
                                }
                                // Giới hạn không vượt quá phí ship
                                if (disc > shippingFee) {
                                    disc = shippingFee;
                                }
                            }

                        } else {
                            disc = parseFloat(item.discount_value ?? 0);

                            if (item.apply_to === 'shipping') {
                                if (shippingFee <= 0) {
                                    msg +=
                                        `<span class="text-red-500">❌ Mã ${item.code}: vui lòng chọn địa chỉ để tính phí ship trước.</span><br>`;
                                    continue;
                                }
                                if (disc > shippingFee) {
                                    disc = shippingFee;
                                }
                            }

                            if (item.apply_to === 'order') {
                                if (disc > cartTotal) {
                                    disc = cartTotal;
                                }
                            }
                        }

                        if (item.apply_to === 'order') {
                            if (usedOrder) {
                                msg += `<span class="text-red-500">❌ Chỉ 1 mã đơn hàng.</span><br>`;
                                continue;
                            }
                            orderDisc = disc;
                            usedOrder = true;
                            document.getElementById('orderCouponIdInput').value = item.coupon_id;
                        } else {
                            if (usedShip) {
                                msg += `<span class="text-red-500">❌ Chỉ 1 mã vận chuyển.</span><br>`;
                                continue;
                            }
                            shippingDisc = disc;
                            usedShip = true;
                            document.getElementById('shippingCouponIdInput').value = item.coupon_id;
                        }

                        msg += `✔️ ${item.code}: Giảm ${disc.toLocaleString('vi-VN')} đ<br>`;
                        totalDisc += disc;

                        const h = document.createElement('input');
                        h.type = 'hidden';
                        h.name = 'coupons[]';
                        h.value = item.code;
                        h.classList.add('applied-coupon');
                        document.querySelector('form').appendChild(h);
                    }

                    // cập nhật giá trị input
                    document.getElementById('orderDiscountInput').value = isNaN(orderDisc) ? 0 : Math.floor(
                        orderDisc);
                    document.getElementById('shippingDiscountInput').value = isNaN(shippingDisc) ? 0 : Math
                        .floor(shippingDisc);
                    document.getElementById('orderDiscount').innerText =
                        `${Math.floor(orderDisc).toLocaleString('vi-VN')} đ`;
                    document.getElementById('shippingDiscount').innerText =
                        `${Math.floor(shippingDisc).toLocaleString('vi-VN')} đ`;

                    updateTotalPrice();

                    resultEl.innerHTML = `
          ${msg}
          <strong>Giảm đơn: ${Math.floor(orderDisc).toLocaleString('vi-VN')} đ</strong><br>
          <strong>Giảm ship: ${Math.floor(shippingDisc).toLocaleString('vi-VN')} đ</strong><br>
          <strong>Tổng giảm: ${Math.floor(totalDisc).toLocaleString('vi-VN')} đ</strong>
        `;
                    setTimeout(() => {
                        resultEl.innerHTML = '';
                    }, 5000);
                } else {
                    let err = data.message || (data.errors || []).join('<br>') || 'Mã không hợp lệ';
                    document.getElementById('couponResult').innerHTML =
                        `<span class="text-red-500">❌ ${err}</span>`;
                }
            } catch (e) {
                console.error(e);
                document.getElementById('couponResult').innerHTML =
                    `<span class="text-red-500">Lỗi khi kiểm tra mã.</span>`;
            }
        }

        const applyBtnManual = document.createElement('button');
        applyBtnManual.type = 'button';
        applyBtnManual.textContent = 'Áp dụng';
        applyBtnManual.className = 'mt-2 bg-green-500 text-white py-1 px-3 rounded-md ml-2';
        document.querySelector('#couponInput').after(applyBtnManual);

        applyBtnManual.addEventListener('click', () => {
            const v = document.getElementById('couponInput').value.trim();
            applyCoupons(v);
        });

        document.getElementById('btnApplyPopup').addEventListener('click', () => {
            const orderCode = document.querySelector('input[name="orderCoupon"]:checked')?.value;
            const shipCode = document.querySelector('input[name="shippingCoupon"]:checked')?.value;
            const codes = [orderCode, shipCode].filter(Boolean).join(',');
            document.getElementById('couponInput').value = codes;
            applyCoupons(codes);
        });

        function updateTotalPrice() {
            const toNumber = str => parseInt((str || '0').replace(/\D/g, ''), 10);

            const cartTotalText = document.getElementById('cartTotal')?.innerText || '0';
            const cartTotal = toNumber(cartTotalText);

            const shippingFeeValue = document.getElementById('shippingFeeValue')?.value || '0';
            const shippingFee = parseInt(shippingFeeValue, 10) || 0;

            const orderDiscValue = document.getElementById('orderDiscountInput')?.value || '0';
            const shippingDiscValue = document.getElementById('shippingDiscountInput')?.value || '0';
            const orderDiscount = parseInt(orderDiscValue, 10) || 0;
            const shippingDiscount = parseInt(shippingDiscValue, 10) || 0;

            console.log({
                cartTotal,
                shippingFee,
                orderDiscount,
                shippingDiscount
            });

            const total = Math.max(0, cartTotal + shippingFee - orderDiscount - shippingDiscount);

            document.getElementById('totalPrice').innerText = `${total.toLocaleString('vi-VN')} đ`;
        }

        updateTotalPrice();
    });



    document.addEventListener('DOMContentLoaded', function() {
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
                data.forEach(function(province) {
                    let option = document.createElement("option");
                    option.value = province.code; // dùng "code" chứ không phải "id"
                    option.textContent = province.name;
                    citySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching provinces:', error));

        // Khi chọn Tỉnh => load Quận/Huyện
        citySelect.addEventListener('change', function() {
            const selectedCityCode = this.value;
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn xã</option>';

            if (selectedCityCode) {
                fetch(`https://provinces.open-api.vn/api/p/${selectedCityCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.districts.forEach(function(district) {
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
        districtSelect.addEventListener('change', function() {
            const selectedDistrictCode = this.value;
            wardSelect.innerHTML = '<option value="">Chọn xã</option>';

            if (selectedDistrictCode) {
                fetch(`https://provinces.open-api.vn/api/d/${selectedDistrictCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.wards.forEach(function(ward) {
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



    function updateShippingFee(addressId) {
          function updateTotalPrice() {
            const toNumber = str => parseInt((str || '0').replace(/\D/g, ''), 10);

            const cartTotalText = document.getElementById('cartTotal')?.innerText || '0';
            const cartTotal = toNumber(cartTotalText);

            const shippingFeeValue = document.getElementById('shippingFeeValue')?.value || '0';
            const shippingFee = parseInt(shippingFeeValue, 10) || 0;

            const orderDiscValue = document.getElementById('orderDiscountInput')?.value || '0';
            const shippingDiscValue = document.getElementById('shippingDiscountInput')?.value || '0';
            const orderDiscount = parseInt(orderDiscValue, 10) || 0;
            const shippingDiscount = parseInt(shippingDiscValue, 10) || 0;

            console.log({
                cartTotal,
                shippingFee,
                orderDiscount,
                shippingDiscount
            });

            const total = Math.max(0, cartTotal + shippingFee - orderDiscount - shippingDiscount);

            document.getElementById('totalPrice').innerText = `${total.toLocaleString('vi-VN')} đ`;
        }
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
                        shippingFeeText.innerText =
                            `Phí vận chuyển: ${parseFloat(data.shipping_fee).toLocaleString()} đ`;
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




    }
</script>
<script src="//unpkg.com/alpinejs" defer></script>
