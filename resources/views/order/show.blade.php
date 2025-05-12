@extends('client.layout')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">Chi tiết đơn hàng #{{ $order->order_code }}</h1>

    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Thông tin người nhận</h3>
        @if ($order->userAddresses)
            <p class="text-gray-600"><strong>Họ tên:</strong> {{ $order->userAddresses->recipient_name }}</p>
            <p class="text-gray-600"><strong>Số điện thoại:</strong> {{ $order->userAddresses->recipient_phone }}</p>
            <p class="text-gray-600"><strong>Địa chỉ:</strong>
                <span class="block mt-1">
                    <strong>{{ $order->userAddresses->address_name }}</strong> <br>
                    {{ $order->userAddresses->street_address }}, {{ $order->userAddresses->ward }}, {{ $order->userAddresses->district }}, {{ $order->userAddresses->city }}
                </span>
            </p>
        @else
            <p class="text-red-500">Không có địa chỉ</p>
        @endif
    </div>

    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Danh sách sản phẩm</h3>
        <ul class="space-y-4">
            @foreach($order->orderDetails as $detail)
                <li class="flex items-center space-x-4">
                    <!-- Hiển thị ảnh sản phẩm nếu có -->
                    @if ($detail->product->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $detail->product->images->first()->image_url) }}" alt="{{ $detail->product->name }}" class="w-16 h-16 object-cover rounded-md">
                    @else
                        <span class="text-gray-500">Không có ảnh sản phẩm</span>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-800">{{ $detail->product->name }}</p>
    
                        @if ($detail->variant)
                            <p class="text-sm text-gray-600">
                                @foreach ($detail->variant->attributes as $attribute)
                                    - {{ $attribute->variantAttribute->attribute_name }}: {{ $attribute->variantAttribute->attribute_value }} 
                                @endforeach
                            </p>
                        @endif
                        
                        <p class="text-sm text-gray-600">Số lượng: {{ $detail->quantity }}</p>
                        <p class="text-sm text-gray-600">Giá: {{ number_format($detail->price, 0, ',', '.') }} đ</p>
                        <p class="text-sm text-gray-600">Tổng: {{ number_format($detail->total_price, 0, ',', '.') }} đ</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Trạng thái đơn hàng</h3>
        <p class="text-gray-600">{{ $order->status ? $order->status->name : 'Chưa có trạng thái' }}</p>
    </div>
    

    @php
        $subtotal = $order->orderDetails->sum('total_price');
        $discount = $order->discount_amount ?? 0;
        $shipping = $order->shipping_fee ?? 0;
        $shippingDiscount = $order->shipping_discount ?? 0; // Giảm giá phí vận chuyển
        $finalTotal = $subtotal + $shipping - $discount - $shippingDiscount;
    @endphp

    <div class="mb-6">
        <p class="text-lg text-gray-800">Tạm tính: <span class="font-bold text-gray-900">{{ number_format($subtotal, 0, ',', '.') }} đ</span></p>
        <p class="text-lg text-gray-800">Phí vận chuyển: <span class="font-bold text-gray-900">{{ number_format($shipping, 0, ',', '.') }} đ</span></p>

        @if (isset($discount) && $discount > 0)
            <p class="text-green-600">Giảm giá đơn hàng: <span class="font-bold">-{{ number_format($discount, 0, ',', '.') }} đ</span></p>
        @endif

        @if (isset($shippingDiscount) && $shippingDiscount > 0)
            <p class="text-green-600">Giảm giá phí vận chuyển: <span class="font-bold">-{{ number_format($shippingDiscount, 0, ',', '.') }} đ</span></p>
        @endif

        <p class="font-bold text-2xl mt-4 text-gray-900">
            Tổng thanh toán: {{ number_format($finalTotal, 0, ',', '.') }} đ
        </p>
    </div>

    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Thời gian đặt hàng</h3>
        <p class="text-gray-600">{{ $order->created_at->format('H:i d/m/Y') }}</p>
    </div>

    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Phương thức thanh toán</h3>
        <p class="text-gray-600">
            @if($order->payment_method === 'cod')
                Thanh toán khi nhận hàng (COD)
            @elseif($order->payment_method === 'wallet')
                Thanh toán qua ví
            @else
                Chưa có phương thức thanh toán
            @endif
        </p>
    </div>

    <div class="mt-6">
        <a href="{{ route('order.index') }}" class="bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600 transition">Quay lại danh sách đơn hàng</a>
    </div>
</div>
@endsection
