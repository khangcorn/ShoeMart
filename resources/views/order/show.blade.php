@extends('client.layout')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Chi tiết đơn hàng #{{ $order->order_code }}</h1>

    <div class="mb-4">
        <h3 class="text-xl font-semibold">Thông tin người nhận</h3>
        @if ($order->userAddresses)
            <p><strong>Họ tên:</strong> {{ $order->userAddresses->recipient_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->userAddresses->recipient_phone }}</p>
            <p><strong>Địa chỉ:</strong> 
                <span>
                    <strong>{{ $order->userAddresses->address_name }}</strong> <br>
                    {{ $order->userAddresses->street_address }}, {{ $order->userAddresses->ward }}, {{ $order->userAddresses->district }}, {{ $order->userAddresses->city }}
                    </span>
                </p>
            @else
                <p>Không có địa chỉ</p>
            @endif
    </div>
    
    <div class="mb-4">
        <h3 class="text-xl font-semibold">Danh sách sản phẩm</h3>
        <ul>
            @foreach($order->orderDetails as $detail)
                <li>
                    {{ $detail->product->name }} 
    
                    @if ($detail->variant)
                        @foreach ($detail->variant->attributes as $attribute)
                            - {{ $attribute->variantAttribute->attribute_name }}: {{ $attribute->variantAttribute->attribute_value }} 
                        @endforeach
                    @endif
                    
                    - Số lượng: {{ $detail->quantity }} 
                    - Giá: {{ number_format($detail->price, 0, ',', '.') }} đ
                    - Tổng: {{ number_format($detail->total_price, 0, ',', '.') }} đ
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mb-4">
        <h3 class="text-xl font-semibold">Trạng thái đơn hàng</h3>
        <p>{{ $order->status ? $order->status->name : 'Chưa có trạng thái' }}</p>
    
        {{-- Hiển thị lý do từ chối hoàn tiền nếu có --}}
        @if ($order->refund && $order->refund->status === 'rejected')
            <div class="mt-2 bg-red-100 text-red-700 p-3 rounded-md">
                <strong>Hoàn tiền đã bị từ chối.</strong><br>
                <span>Lý do: {{ $order->refund->note }}</span>
            </div>
        @endif
    </div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold">Phương thức thanh toán</h3>
        <p>{{ $order->payment_method ?? 'Chưa có thông tin' }}</p>
    </div>
    

    @php
    $subtotal = $order->orderDetails->sum('total_price');
    $discount = $order->discount_amount ?? 0;
    $shipping = $order->shipping_fee ?? 0;
    $shippingDiscount = $order->shipping_discount ?? 0; // Giảm giá phí vận chuyển
    $finalTotal = $subtotal + $shipping - $discount - $shippingDiscount;
@endphp

<div class="mt-4">
    <p>Tạm tính: {{ number_format($subtotal, 0, ',', '.') }} đ</p>
    <p>Phí vận chuyển: {{ number_format($shipping, 0, ',', '.') }} đ</p>

    @if (isset($discount) && $discount > 0)
        <p class="text-green-600">Giảm giá đơn hàng: -{{ number_format($discount, 0, ',', '.') }} đ</p>
    @endif

    @if (isset($shippingDiscount) && $shippingDiscount > 0)
    <p class="text-green-600">Giảm giá phí vận chuyển: -{{ number_format($shippingDiscount, 0, ',', '.') }} đ</p>
@endif


    <p class="font-bold text-lg mt-2">
        Tổng thanh toán: {{ number_format($finalTotal, 0, ',', '.') }} đ
    </p>
</div>

    

    <div class="mb-4">
        <h3 class="text-xl font-semibold">Thời gian đặt hàng</h3>
        <p>{{ $order->created_at->format('H:i d/m/Y') }}</p>
    </div>

    <!-- Phương thức thanh toán -->
    <div class="mb-4">
        <h3 class="text-xl font-semibold">Phương thức thanh toán</h3>
        <p>
            @if($order->payment_method === 'cod')
                Thanh toán khi nhận hàng (COD)
            @elseif($order->payment_method === 'wallet')
                Thanh toán qua ví
            @else
                Chưa có phương thức thanh toán
            @endif
        </p>
    </div>

    <!-- Nút Quay lại -->
    <div class="mt-4">
        <a href="{{ route('order.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Quay lại danh sách đơn hàng</a>
    </div>
@endsection
