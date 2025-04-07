@extends('client.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Thanh Toán Đơn Hàng</h2>
    <p>Mã đơn hàng: <strong>{{ $order->order_code }}</strong></p>
    <p>Tổng tiền cần thanh toán: <strong>{{ number_format($order->total, 0, ',', '.') }} đ</strong></p>
    
    <!-- Form thanh toán sử dụng Stripe Elements -->
    <form id="payment-form">
        <div id="card-element"><!-- Stripe sẽ render Element tại đây --></div>
        <button id="submit" class="mt-4 bg-blue-500 text-white py-2 px-6 rounded-md">
            Thanh toán
        </button>
        <div id="error-message" class="text-red-500 mt-2"></div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Lấy public key từ config
    const stripe = Stripe("{{ config('services.stripe.public') }}");
    const elements = stripe.elements();
    
    // Tạo một Element hiển thị thông tin thẻ
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');
    
    // Xử lý form submit
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        
        const {token, error} = await stripe.createToken(cardElement);
        
        if (error) {
            document.getElementById('error-message').textContent = error.message;
        } else {
            // Gửi token tới server để xử lý thanh toán
            fetch("{{ route('order.processPayment', $order->order_id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ token: token.id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Thanh toán thành công!");
                    window.location.href = "{{ route('order.success') }}";
                } else {
                    document.getElementById('error-message').textContent = data.error;
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
        }
    });
</script>
@endsection
