@extends('admin.layout')

@section('content')

<div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Danh sách đơn hàng</h1>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr class="border-b border-gray-300">
                    <th class="px-4 py-3 border">Mã đơn</th>
                    <th class="px-4 py-3 border">Người đặt</th>
                    <th class="px-4 py-3 border">Trạng thái</th>
                    <th class="px-4 py-3 border">Ngày đặt</th>
                    <th class="px-4 py-3 border">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $order->order_code }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user->username ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->status->name ?? 'Chưa rõ' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order->order_id) }}"
                               class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-md shadow">
                                👁️ Xem
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links('pagination::tailwind') }}
    </div>
</div>
@endsection
