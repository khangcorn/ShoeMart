@extends('client.layout')

@section('content')
@if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-md">
        {{ session('success') }}
    </div>
@endif
    <h1 class="text-2xl font-semibold mb-4">Danh sách đơn hàng của bạn</h1>

    <table class="table-auto w-full border border-gray-300 mb-4">
        <thead>
            <tr>
                <th class="border px-4 py-2">Mã đơn hàng</th>
                <th class="border px-4 py-2">Tổng tiền</th>
                <th class="border px-4 py-2">Trạng thái</th>
                <th class="border px-4 py-2">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td class="border px-4 py-2">{{ $order->order_code }}</td>
                    <td class="border px-4 py-2">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                    <td class="border px-4 py-2">{{ $order->status->name }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('order.show', $order->order_id) }}" class="text-blue-500">Xem chi tiết</a>
                        @if($order->status->status_id != 3 && $order->status->status_id != 5) 
                            <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-red-500">Hủy đơn</button>
                            </form>
                        @else
                            <span class="text-gray-500">Đã hủy</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Hiển thị phân trang -->
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
