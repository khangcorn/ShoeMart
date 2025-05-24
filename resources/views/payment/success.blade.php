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
{{-- 1	
Ngân hàng: NCB
Số thẻ: 9704198526191432198
Tên chủ thẻ:NGUYEN VAN A
Ngày phát hành:07/15
Mật khẩu OTP:123456
Thành công
2	
Ngân hàng: NCB
Số thẻ: 9704195798459170488
Tên chủ thẻ:NGUYEN VAN A
Ngày phát hành:07/15
Thẻ không đủ số dư
3	
Ngân hàng: NCB
Số thẻ: 9704192181368742
Tên chủ thẻ:NGUYEN VAN A
Ngày phát hành:07/15
Thẻ chưa kích hoạt
4	
Ngân hàng: NCB
Số thẻ: 9704193370791314
Tên chủ thẻ:NGUYEN VAN A
Ngày phát hành:07/15
Thẻ bị khóa
5	
Ngân hàng: NCB
Số thẻ: 9704194841945513
Tên chủ thẻ:NGUYEN VAN A
Ngày phát hành:07/15
Thẻ bị hết hạn
6	
Loại thẻ quốc tếVISA (No 3DS)
Số thẻ: 4456530000001005
CVC/CVV: 123
Tên chủ thẻ:NGUYEN VAN A
Ngày hết hạn:12/26
Email:test@gmail.com
Địa chỉ:22 Lang Ha
Thành phố:Ha Noi
Thành công
7	
Loại thẻ quốc tếVISA (3DS)
Số thẻ: 4456530000001096
CVC/CVV: 123
Tên chủ thẻ:NGUYEN VAN A
Ngày hết hạn:12/26
Email:test@gmail.com
Địa chỉ:22 Lang Ha
Thành phố:Ha Noi
Thành công
8	
Loại thẻ quốc tếMasterCard (No 3DS)
Số thẻ: 5200000000001005
CVC/CVV: 123
Tên chủ thẻ:NGUYEN VAN A
Ngày hết hạn:12/26
Email:test@gmail.com
Địa chỉ:22 Lang Ha
Thành phố:Ha Noi
Thành công
9	
Loại thẻ quốc tếMasterCard (3DS)
Số thẻ: 5200000000001096
CVC/CVV: 123
Tên chủ thẻ:NGUYEN VAN A
Ngày hết hạn:12/26
Email:test@gmail.com
Địa chỉ:22 Lang Ha
Thành phố:Ha Noi
Thành công
10	
Loại thẻ quốc tếJCB (No 3DS)
Số thẻ: 3337000000000008
CVC/CVV: 123
Tên chủ thẻ:NGUYEN VAN A
Ngày hết hạn:12/26
Email:test@gmail.com
Địa chỉ:22 Lang Ha
Thành phố:Ha Noi
Thành công
11	
Loại thẻ quốc tếJCB (3DS)
Số thẻ: 3337000000200004
CVC/CVV: 123
Tên chủ thẻ:NGUYEN VAN A
Ngày hết hạn:12/24
Email:test@gmail.com
Địa chỉ:22 Lang Ha
Thành phố:Ha Noi
Thành công
12	
Loại thẻ ATM nội địaNhóm Bank qua NAPAS
Số thẻ: 9704000000000018
Số thẻ: 9704020000000016
Tên chủ thẻ:NGUYEN VAN A
Ngày phát hành:03/07
OTP:otp
Thành công
12	
Loại thẻ ATM nội địaEXIMBANK
Số thẻ: 9704310005819191
Tên chủ thẻ:NGUYEN VAN A
Ngày hết hạn:10/26
Thành công --}}