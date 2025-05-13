@extends('admin.layout')

@section('content')
@if(session('success'))
    <div id="flash-message" class="fixed top-5 right-5 bg-green-100 text-green-800 px-4 py-2 rounded shadow z-50">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.remove();
            }
        }, 5000); // 5000ms = 5 giây
    </script>
@endif


@if(session('error'))
    <div id="flash-error" class="bg-red-500 text-white p-4 mb-4 rounded-md">
        {{ session('error') }}
    </div>

    <script>
        setTimeout(() => {
            const flashError = document.getElementById('flash-error');
            if (flashError) {
                flashError.remove();
            }
        }, 5000); // 5 giây
    </script>
@endif

<a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 mb-4 text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline transition duration-150">  
    ← Quay lại danh sách đơn hàng
</a>
<div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-700 mb-4">Chi tiết đơn hàng: {{ $order->order_code }}</h1>

    <div class="mb-6 space-y-2">
        <p>
            <strong class="text-gray-600">Người đặt:</strong>
            {{ $order->user ? $order->user->username . ' - ' . $order->user->phone . ' - ' . $order->user->email : 'N/A' }}
        </p>
        <p>
            <strong class="text-gray-600">Người nhận:</strong>
            {{ $order->userAddresses ? $order->userAddresses->recipient_name . ' - ' . $order->userAddresses->recipient_phone : 'N/A' }}
        </p>
        
        <p>
            <strong class="text-gray-600">Địa chỉ nhận hàng:</strong>
            <span>
                
                {{ $order->userAddresses->street_address }}, {{ $order->userAddresses->ward }}, {{ $order->userAddresses->district }}, {{ $order->userAddresses->city }}<strong>({{ $order->userAddresses->address_name }})</strong> <br>
            </span>
        </p>

        <p>
            <strong class="text-gray-600">Trạng thái hiện tại:</strong>
            <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-sm font-medium">
                {{ $order->status->name ?? 'Chưa rõ' }}
            </span>
        </p>
    </div>

    <h2 class="text-xl font-semibold text-gray-800 mb-3">Sản phẩm trong đơn</h2>
    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg">
        @foreach ($order->orderDetails as $detail)
            <li class="p-4 flex items-center space-x-4">
                   <!-- Hiển thị ảnh sản phẩm nếu có -->
                   @if ($detail->product->images->isNotEmpty())
                   <img src="{{ asset('storage/' . $detail->product->images->first()->image_url) }}" alt="{{ $detail->product->name }}" class="w-20 h-20 mr-4">
                    @else
                        <span>Không có ảnh sản phẩm</span>
                    @endif
                
                <div class="flex-1">
                    <p class="text-gray-800 font-medium">{{ $detail->product->name }}</p>
                    <p class="text-gray-500 text-sm">Số lượng: {{ $detail->quantity }}</p>
                </div>

                <div class="text-right text-gray-700 font-semibold">
                    {{ number_format($detail->price, 0, ',', '.') }} đ
                </div>
            </li>
        @endforeach
    </ul>

    <div class="mt-6 text-right">
        <p class="text-lg font-bold text-gray-800">
            Tổng tiền đơn hàng:
            <span class="text-green-600">
                {{ number_format($order->total, 0, ',', '.') }} đ
            </span>
        </p>
    </div>
</div>

@endsection
