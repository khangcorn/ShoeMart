@extends('client.layout')

@section('content')

<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Danh sách mã giảm giá</h2>

    {{-- Tách mã đơn hàng --}}
    @php
        $orderCoupons = $coupons->where('apply_to', 'order');
        $shippingCoupons = $coupons->where('apply_to', 'shipping');
    @endphp

    {{-- Mã giảm giá cho đơn hàng --}}
    <h3 class="text-xl font-bold mt-6 mb-2">🎁 Mã giảm cho đơn hàng</h3>
    @if($orderCoupons->isEmpty())
        <p class="text-gray-500">Hiện không có mã giảm giá cho đơn hàng.</p>
    @else
        <ul class="space-y-4">
            @foreach($orderCoupons as $coupon)
            <li class="relative border p-4 rounded-md shadow-sm {{ 
                ($coupon->status !== 'active' || $coupon->usage_limit == 0 || ($coupon->usage_count >= $coupon->usage_limit) || \Carbon\Carbon::parse($coupon->expiration_date)->isPast()) 
                ? 'expired' 
                : '' 
            }}">
            
                    <h4 class="text-lg font-bold">Mã voucher: {{ $coupon->code }}</h4>
                    <p class="text-sm">
                        Giảm giá
                        @if ($coupon->discount_type === 'percentage')
                            {{ intval($coupon->discount_value) }}%
                            @if ($coupon->max_discount_value)
                                (Tối đa {{ number_format($coupon->max_discount_value, 0, ',', '.') }}đ)
                            @endif
                        @elseif ($coupon->discount_type === 'fixed')
                            tối đa : {{ number_format($coupon->discount_value, 0, ',', '.') }}đ 
                        @else
                            Không rõ loại giảm giá
                        @endif
                    </p>

                    @if($coupon->min_order_value)
                        <p class="text-sm text-gray-500">Dành cho đơn hàng từ {{ number_format($coupon->min_order_value, 0, ',', '.') }}đ</p>
                    @endif

                    @if($coupon->status !== 'active')
                    <p class="text-xs text-red-500">Mã giảm giá không hoạt động</p>
                    @elseif($coupon->usage_limit == 0 || ($coupon->usage_count >= $coupon->usage_limit))
                        <p class="text-xs text-red-500">Đã hết lượt sử dụng</p>
                    @elseif(\Carbon\Carbon::parse($coupon->expiration_date)->isPast())
                        <p class="text-xs text-red-500">Đã quá hạn</p>
                    @else
                        <p class="text-xs text-green-500">Số lượng có hạn</p>
                    @endif
                    <p class="text-xs text-gray-500">
                        Hết hạn: {{ \Carbon\Carbon::parse($coupon->expiration_date)->format('d/m/Y H:i:s') }}
                    </p>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Mã giảm giá cho phí vận chuyển --}}
    <h3 class="text-xl font-bold mt-10 mb-2">🚚 Mã giảm cho phí vận chuyển</h3>
    @if($shippingCoupons->isEmpty())
        <p class="text-gray-500">Hiện không có mã giảm giá cho phí vận chuyển.</p>
    @else
        <ul class="space-y-4">
            @foreach($shippingCoupons as $coupon)
            <li class="relative border p-4 rounded-md shadow-sm {{ 
                ($coupon->status !== 'active' || $coupon->usage_limit == 0 || ($coupon->usage_count >= $coupon->usage_limit) || \Carbon\Carbon::parse($coupon->expiration_date)->isPast()) 
                ? 'expired' 
                : '' 
            }}">
            
                    <h4 class="text-lg font-bold">Mã voucher: {{ $coupon->code }}</h4>
                    <p class="text-sm">
                        Giảm giá:
                        @if ($coupon->discount_type === 'percentage')
                            {{ intval($coupon->discount_value) }}%
                            @if ($coupon->max_discount_value)
                                (Tối đa {{ number_format($coupon->max_discount_value, 0, ',', '.') }}đ)
                            @endif
                        @elseif ($coupon->discount_type === 'fixed')
                            {{ number_format($coupon->discount_value, 0, ',', '.') }}đ
                        @else
                            Không rõ loại giảm giá
                        @endif
                    </p>

                    @if($coupon->min_order_value)
                        <p class="text-sm text-gray-500">Dành cho đơn hàng từ {{ number_format($coupon->min_order_value, 0, ',', '.') }}đ</p>
                    @endif

                    @php
                    $expirationDate = \Carbon\Carbon::parse($coupon->expiration_date);
                    @endphp
                    
                    @if($coupon->usage_limit == 0 || ($coupon->usage_count >= $coupon->usage_limit))
                        <p class="text-xs text-red-500">Đã hết lượt sử dụng</p>
                    @elseif($expirationDate->isPast())
                        <p class="text-xs text-red-500">Đã quá hạn</p>
                    @else
                        <p class="text-xs text-green-500">Số lượng có hạn</p>
                    @endif
                    
                    <p class="text-xs text-gray-500">
                        Hết hạn: {{ $expirationDate->format('d/m/Y H:i:s') }}
                    </p>
                
                </li>
            @endforeach
        </ul>
    @endif
    <a href="{{ session('previous_url', url('/checkout')) }}"
   class="inline-block mb-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700  mt-4 transition">
    ← Quay lại
</a>

</div>
<style>
    li.expired::after {
    content: '';
    position: absolute;
    inset: 0;
    background-color: rgba(255, 255, 255, 0.6);
    border-radius: 0.375rem; /* rounded-md */
}

</style>


@endsection
