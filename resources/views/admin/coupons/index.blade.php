@extends('admin.layout')

@section('title', 'Coupons')

@section('content')
<style>
    /* CSS nếu cần thêm */
#button {
    position: absolute;
    top: 10px; /* Điều chỉnh khoảng cách từ trên */
    right: 10px; /* Điều chỉnh khoảng cách từ bên phải */
    background: transparent; /* Đảm bảo nút không có nền */
    border: none; /* Bỏ đường viền */
    font-size: 20px; /* Điều chỉnh kích thước icon */
    cursor: pointer; /* Thêm con trỏ chuột khi hover */
}

</style>
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="flex justify-between items-center px-6 py-4 bg-blue-600 text-white rounded-t-lg">
        <h4 class="text-lg font-semibold">Coupons</h4>
         @if(auth()->user()->hasPermission('create_coupons'))
        <a href="{{ route('coupons.create') }}" class="bg-white text-blue-600 px-3 py-1 rounded-md text-sm font-medium hover:bg-gray-100 shadow">
            + Add Coupon
        </a>
        @endif
    </div>

    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('success') }}
                <button id="button" type="button" class="absolute top-2 right-2 text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                    &times;
                </button>
                
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr class="border-b border-gray-300">
                        <th class="border px-4 py-3">#</th>
                        <th class="border px-4 py-3">Code</th>
                        <th class="border px-4 py-3">Apply</th>
                        <th class="border px-4 py-3">Type</th>
                        <th class="border px-4 py-3">Value</th>
                        <th class="border px-4 py-3">Max Discount</th>
                        <th class="border px-4 py-3">Usage Limit</th>
                        <th class="border px-4 py-3">Usage count</th>
                        <th class="border px-4 py-3">Min order</th>
                        <th class="border px-4 py-3">Expiration</th>
                        <th class="border px-4 py-3">Status</th>
                        <th class="border px-4 py-3">Actions</th>
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
                            <td class="px-4 py-3 text-gray-700 capitalize">{{ $coupon->usage_limit }}</td>
                            <td class="px-4 py-3 text-gray-700 capitalize">{{ $coupon->usage_count }}</td>
                            <td class="px-4 py-3 text-gray-700 capitalize">{{ (int) $coupon->min_order_value }}đ</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $coupon->expiration_date_formatted }}
                            </td>
                            
                                                       
                            
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
                                        ✏️ Edit
                                    </a>
                                    @endif
                                     @if(auth()->user()->hasPermission('delete_coupons'))
                                    <form action="{{ route('coupons.destroy', $coupon->coupon_id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 shadow">
                                            🗑️ Delete
                                        </button>                               
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-gray-500">No coupons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
