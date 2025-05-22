@extends('client.layout')

@section('title', 'Thanh toán thành công')

@section('content')
<div class="max-w-xl mx-auto mt-20 p-6 bg-white rounded shadow text-center">
    <h1 class="text-3xl font-bold mb-4 text-green-600">Thanh toán thành công!</h1>
    <p class="mb-6">Cảm ơn bạn đã thanh toán đơn hàng #{{ $order->order_id ?? '' }}.</p>
    <p>Bạn sẽ được chuyển hướng về trang chi tiết đơn hàng trong <span id="countdown">10</span> giây.</p>

    <a href="{{ isset($order) ? route('order.show', $order->order_id) : url('/') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
        Xem ngay
    </a>
</div>

<script>
    let timeLeft = 10;
    const countdownEl = document.getElementById('countdown');

    const timer = setInterval(() => {
        timeLeft--;
        countdownEl.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(timer);
            window.location.href = "{{ isset($order) ? route('order.show', $order->order_id) : url('/') }}";
        }
    }, 1000);
</script>
@endsection
