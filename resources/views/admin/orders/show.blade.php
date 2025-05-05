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

<a href="{{ route('admin.orders.index') }}" class="inline-block mb-4 text-blue-600 hover:underline text-sm">
    ← Quay lại danh sách đơn hàng
</a>
<div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-700 mb-4">Chi tiết đơn hàng: {{ $order->order_code }}</h1>

    <div class="mb-6">
        <p class="mb-2"><strong class="text-gray-600">Khách hàng:</strong> {{ $order->user->username ?? 'N/A' }}</p>
        <p><strong class="text-gray-600">Trạng thái hiện tại:</strong> 
            <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-sm font-medium">
                {{ $order->status->name ?? 'Chưa rõ' }}
            </span>
        </p>
    </div>

    @php
        $lockedStatuses = [3,4, 5, 6, 8]; // các trạng thái không cho đổi
    @endphp

@if (!in_array($order->status_id, $lockedStatuses))
    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Cập nhật trạng thái:</label>
            <select name="status_id" class="border rounded p-2">
                <option value="1" {{ $order->status_id == 1 ? 'selected' : '' }}>Đơn hàng mới</option>
                <option value="2" {{ $order->status_id == 2 ? 'selected' : '' }}>Đang vận chuyển</option>
                <option value="7" {{ $order->status_id == 7 ? 'selected' : '' }}>Đã giao hàng</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
            Cập nhật
        </button>
    </form>

    {{-- Form hủy đơn (phải tách ra ngoài) --}}
    @if ($order->status_id == 1)
        <form method="POST" action="{{ route('admin.orders.cancel', $order->order_id) }}" class="inline-block">
            @csrf
            @method('PUT')
            <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium">
                Hủy đơn hàng
            </button>
        </form>
    @endif
@else
    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 mb-6 rounded">
        Không thể thay đổi trạng thái của đơn hàng này.
    </div>
@endif


    <h2 class="text-xl font-semibold text-gray-800 mb-3">Sản phẩm trong đơn</h2>
    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg">
        @foreach ($order->orderDetails as $detail)
            <li class="p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-800 font-medium">{{ $detail->product->name }}</p>
                        <p class="text-gray-500 text-sm">SL: {{ $detail->quantity }}</p>
                    </div>
                    <div class="text-right text-gray-700 font-semibold">
                        {{ number_format($detail->price, 0, ',', '.') }} đ
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
