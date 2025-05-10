@extends('client.layout')

@section('content')
<div class="container mx-auto p-6 flex flex-col items-center justify-center text-center">
    {{-- Icon check thành công --}}
    <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center mb-4 animate-bounce">
        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h2 class="text-2xl font-semibold text-green-700 mb-2">Đặt hàng thành công!</h2>

    @if(session('success'))
        <p class="text-gray-700 mb-4">{{ session('success') }}</p>
    @endif

    <p class="text-gray-600 mb-6">
        Bạn sẽ được chuyển về <strong>trang chủ</strong> sau <span id="countdown" class="font-bold">10</span> giây...
    </p>

    <div>
        <a href="{{ route('cart.index') }}" class="bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition">
            Quay lại giỏ hàng
        </a>
         {{-- Kiểm tra nếu biến $order tồn tại để hiển thị nút xem chi tiết đơn hàng --}}
        @if(isset($order) && $order)
            <a href="{{ route('order.show', ['order_id' => $order->order_id]) }}" class="bg-green-500 text-white py-2 px-6 rounded-md hover:bg-green-600 transition ml-4">
                Xem chi tiết đơn hàng
            </a>
        <a href="{{ url('/') }}" class="bg-gray-500 text-white py-2 px-6 rounded-md hover:bg-gray-600 transition ml-4">
            Về trang chủ
        </a>

       
        @endif
    </div>

</div>

{{-- Script chuyển hướng sau 10 giây --}}
<script>
    let seconds = 10;
    const countdownElement = document.getElementById('countdown');

    const countdown = setInterval(function () {
        seconds--;
        countdownElement.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(countdown);
            window.location.href = "{{ url('/') }}"; // Chuyển hướng về trang chủ sau 10 giây
        }
    }, 1000);
</script>
@endsection
