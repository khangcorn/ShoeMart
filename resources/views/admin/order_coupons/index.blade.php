@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Order Coupons</h2>
        <a href="{{ route('order-coupons.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            + Thêm mới
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-700 border border-green-400 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                    <th class="py-3 px-4 text-left">ID</th>
                    <th class="py-3 px-4 text-left">Order</th>
                    <th class="py-3 px-4 text-left">Coupon</th>
                    <th class="py-3 px-4 text-left">Giảm giá</th>
                    <th class="py-3 px-4 text-left">Ngày tạo</th>
                    <th class="py-3 px-4 text-left">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @foreach($orderCoupons as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $item->order_coupon_id }}</td>
                        <td class="py-2 px-4">#{{ $item->order_id }}</td>
                        <td class="py-2 px-4">#{{ $item->coupon_id }}</td>
                        <td class="py-2 px-4">{{ number_format($item->applied_amount, 0, ',', '.') }} đ</td>
                        <td class="py-2 px-4">{{ $item->created_at }}</td>
                        <td class="py-2 px-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('order-coupons.edit', $item) }}"
                                   class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition text-xs">
                                    Sửa
                                </a>
                                <form action="{{ route('order-coupons.destroy', $item) }}" method="POST" onsubmit="return confirm('Xác nhận xóa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if($orderCoupons->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">Không có dữ liệu.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
