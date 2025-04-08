@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-700 mb-4">Chi tiết đơn hàng: {{ $order->order_code }}</h1>

    <div class="mb-6">
        <p class="mb-2"><strong class="text-gray-600">Khách hàng:</strong> {{ $order->user->name ?? 'N/A' }}</p>
        <p><strong class="text-gray-600">Trạng thái hiện tại:</strong> 
            <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-sm font-medium">
                {{ $order->status->name ?? 'Chưa rõ' }}
            </span>
        </p>
    </div>

    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Cập nhật trạng thái:</label>
            <select name="status_id" id="status_id" class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @foreach ($statuses as $status)
                    <option value="{{ $status->status_id }}" @if($order->status_id == $status->status_id) selected @endif>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
            Cập nhật
        </button>
    </form>

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
