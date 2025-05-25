@extends('admin.layout')

@section('title', 'Coupons')

@section('content')
<style>
    #button {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }
</style>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="flex justify-between items-center px-6 py-4 bg-blue-600 text-white rounded-t-lg">
        <h4 class="text-lg font-semibold">Mã giảm giá</h4>
        @if(auth()->user()->hasPermission('create_coupons'))
            <a href="{{ route('coupons.create') }}" class="bg-white text-blue-600 px-3 py-1 rounded-md text-sm font-medium hover:bg-gray-100 shadow">
                + Thêm mã giảm giá
            </a>
        @endif
    </div> <!-- Đóng header -->

    <div class="px-6 py-4"> <!-- Thêm padding cho nội dung bên dưới -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('success') }}
                <button id="button" type="button" onclick="this.parentElement.remove()">
                    &times;
                </button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
                <thead class="text-gray-700">
                    <tr class="border-b border-gray-300">
                        <th class="border px-4 py-3">#</th>
                        <th class="border px-4 py-3">Tên</th>
                        <th class="border px-4 py-3">Áp dụng cho</th>
                        <th class="border px-4 py-3">Loại</th>
                        <th class="border px-4 py-3">Mức giảm</th>
                        <th class="border px-4 py-3">Mức giảm tối đa</th>
                        <th class="border px-4 py-3">Tổng lượt sử dụng tối đa</th>
                        <th class="border px-4 py-3">Đã dùng</th>
                        <th class="border px-4 py-3">Giá trị đơn tối thiểu</th>
                        <th class="border px-4 py-3">Thời hạn sử dụng</th>
                        <th class="border px-4 py-3">Trạng thái</th>
                        <th class="border px-4 py-3">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ $coupon->coupon_id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-700 text-lg">{{ $coupon->code }}</td>
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $coupon->apply_to }}</td>
                            <td class="px-4 py-3 text-gray-700 capitalize">{{ $coupon->discount_type }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $coupon->discount_type === 'percentage' ? number_format($coupon->discount_value, 0, ',', '.') . '%' : number_format($coupon->discount_value, 0, ',', '.') . '₫' }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $coupon->max_discount_value ? number_format($coupon->max_discount_value, 0, ',', '.') . '₫' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $coupon->usage_limit }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $coupon->usage_count }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format((int) $coupon->min_order_value, 0, ',', '.') }}₫</td>
                            <td class="px-4 py-3 text-gray-700">{{ $coupon->expiration_date_formatted }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 text-xs rounded {{
                                    $coupon->status === 'active' ? 'bg-green-100 text-green-700' :
                                    ($coupon->status === 'expired' ? 'bg-yellow-100 text-yellow-700' :
                                    'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($coupon->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center space-x-2">
                                    @if(auth()->user()->hasPermission('edit_coupons'))
                                        <a href="{{ route('coupons.edit', $coupon->coupon_id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-black text-xs font-medium rounded-md hover:bg-yellow-600 shadow">
                                            ✏️ Sửa
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('delete_coupons'))
                                        <form action="{{ route('coupons.destroy', $coupon->coupon_id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 shadow">
                                                🗑️ Xóa
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-4 text-gray-500">Không có mã giảm giá nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center">
            {{ $coupons->links('pagination::tailwind') }}
        </div>
    </div> <!-- end content inside white box -->
</div> <!-- end white box -->

@endsection
