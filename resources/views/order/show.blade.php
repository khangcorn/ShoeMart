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
               @if ($order->userAddresses)
    {{ $order->userAddresses->street_address }}, {{ $order->userAddresses->ward }}, {{ $order->userAddresses->district }}, {{ $order->userAddresses->city }}<strong>({{ $order->userAddresses->address_name }})</strong> <br>
@else
    <p>Địa chỉ không tồn tại</p>
@endif
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
        @php
    $image = null;

    if ($detail->variant) {
        // Ưu tiên ảnh có variant_id trùng khớp
        $image = $detail->product->images->firstWhere('variant_id', $detail->variant->variant_id);
    }

    // Nếu không có ảnh của biến thể, dùng ảnh chính (main)
    if (!$image) {
        $image = $detail->product->images->firstWhere('type', 'main');
    }
@endphp

        @if ($detail->product->images->isNotEmpty())
            <a href="{{ route('products.detail', $detail->product->product_id) }}" target="_blank">
            <img src="{{ asset('storage/' . ($image->image_url ?? 'default.jpg')) }}"
     alt="{{ $detail->product->name }}"
     class="w-16 h-16 object-cover rounded-md border transition-transform duration-200 hover:scale-105 hover:shadow-md">
            </a>
               
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

            {{-- Hiển thị trạng thái hủy --}}
          @if ($detail->status === 'cancelled')
    <p class="text-red-600 font-semibold text-sm mt-2">
        ❌ Sản phẩm đã bị hủy bởi shop, vui lòng liên hệ Admin
    </p>
    @if (!empty($detail->cancel_reason))
        <p class="text-red-500 italic text-xs mt-1">
            Lý do hủy: {{ $detail->cancel_reason }}
        </p>
    @endif
@endif

        </div>
    </li>
@endforeach

        </ul>
    </div>

  <div class="mb-6">
    <h3 class="text-2xl font-semibold text-gray-700 mb-2">Trạng thái đơn hàng</h3>
    <p class="text-gray-600">
        {{ $order->status ? $order->status->name : 'Chưa có trạng thái' }}
    </p>

    @if(($order->status_id == 3 || $order->status_id == 5) && $order->cancel_reason)
    <p class="text-sm text-red-600 mt-2 italic">
        Lý do hủy đơn: {{ $order->cancel_reason }}
    </p>
@endif

</div>

    
@php
    $isCancelled = $order->status_id == 5; // hoặc $order->status?->code == 'cancelled'

    $allDetails = $order->orderDetails;
    $validDetails = $order->orderDetails->filter(fn($detail) => $detail->status !== 'cancelled');

    // Nếu đơn hàng đã bị hủy -> tính trên toàn bộ sản phẩm
    $subtotal = $isCancelled ? $allDetails->sum('total_price') : $validDetails->sum('total_price');
    $discount = $order->discount_amount ?? 0;
    $shipping = $order->shipping_fee ?? 0;
    $shippingDiscount = $order->shipping_discount ?? 0;

    $finalTotal = $subtotal + $shipping - $discount - $shippingDiscount;
@endphp



<div class="mb-6">
    <p class="text-lg text-gray-800">Tạm tính: <span class="font-bold text-gray-900">{{ number_format($subtotal, 0, ',', '.') }} đ</span></p>
    <p class="text-lg text-gray-800">Phí vận chuyển: <span class="font-bold text-gray-900">{{ number_format($shipping, 0, ',', '.') }} đ</span></p>

    @if ($discount > 0)
        <p class="text-green-600">Giảm giá đơn hàng: <span class="font-bold">-{{ number_format($discount, 0, ',', '.') }} đ</span></p>
    @endif

    @if ($shippingDiscount > 0)
        <p class="text-green-600">Giảm giá phí vận chuyển: <span class="font-bold">-{{ number_format($shippingDiscount, 0, ',', '.') }} đ</span></p>
    @endif

    <p class="font-bold text-2xl mt-4 text-gray-900">
        Tổng thanh toán: {{ number_format(max($finalTotal, 0), 0, ',', '.') }} đ
    </p>

    @if ($isCancelled)
    <p class="text-sm text-red-500 italic">Đơn hàng đã bị huỷ — đây là tổng tiền ban đầu trước khi huỷ.</p>
@endif

</div>


    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Thời gian đặt hàng</h3>
        <p class="text-gray-600">{{ $order->created_at->format('H:i d/m/Y') }}</p>
    </div>

 <div class="mb-6">
    <h3 class="text-2xl font-semibold text-gray-700 mb-2">Phương thức thanh toán</h3>
    <p class="text-gray-600">
        @switch($order->payment_method)
            @case('cod')
                Thanh toán khi nhận hàng (COD)
                @break
            @case('wallet')
                Thanh toán qua ví
                @break
            @case('vnpay')
                Thanh toán qua VNPay
                @break
            @case('momo')
                Thanh toán qua MoMo
                @break
            @default
                Chưa có phương thức thanh toán
        @endswitch
    </p>
</div>

    <div class="mt-6">
        <a href="{{ route('order.index') }}" class="bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600 transition">Quay lại danh sách đơn hàng</a>
    </div>
</div>
@endsection
