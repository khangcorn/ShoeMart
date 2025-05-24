@extends('admin.layout')

@section('title', 'Create Coupon')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-4">Tạo mã giảm giá</h1>

    <form action="{{ route('coupons.store') }}" method="POST" class="bg-white shadow-md p-6 rounded-lg">
        @csrf

        {{-- Code --}}
        <div class="mb-4">
            <label for="code" class="block text-gray-700">Tên:</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('code') border-red-500 @enderror">
            @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Apply To --}}
        <div class="mb-4">
            <label for="apply_to" class="block text-gray-700">Áp dụng cho:</label>
            <select name="apply_to" id="apply_to" class="w-full px-4 py-2 mt-2 border rounded-lg @error('apply_to') border-red-500 @enderror">
                <option value="order" {{ old('apply_to') == 'order' ? 'selected' : '' }}>Order (Giảm giá đơn hàng)</option>
                <option value="shipping" {{ old('apply_to') == 'shipping' ? 'selected' : '' }}>Shipping (Giảm phí vận chuyển)</option>
            </select>
            @error('apply_to')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Discount Type --}}
        <div class="mb-4">
            <label for="discount_type" class="block text-gray-700">Loại</label>
            <select name="discount_type" id="discount_type" class="w-full px-4 py-2 mt-2 border rounded-lg @error('discount_type') border-red-500 @enderror">
                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
            </select>
            @error('discount_type')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Discount Value --}}
        <div class="mb-4">
            <label for="discount_value" class="block text-gray-700">Mức giảm:</label>
            <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('discount_value') border-red-500 @enderror">
            @error('discount_value')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

       {{-- Max Discount Value --}}
            <div class="mb-4" id="max_discount_value">
                <label for="max_discount_value" class="block text-gray-700">Mức giảm tối đa:</label>
                <input type="number" name="max_discount_value" id="max_discount_value" value="{{ old('max_discount_value') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('max_discount_value') border-red-500 @enderror">
                @error('max_discount_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


        {{-- Expiration Date --}}
        <div class="mb-4">
            <label for="expiration_date" class="block text-gray-700">Thời hạn sử dụng:</label>
            <input type="datetime-local" name="expiration_date" id="expiration_date"
                   value="{{ old('expiration_date', isset($coupon->expiration_date) ? \Carbon\Carbon::parse($coupon->expiration_date)->format('Y-m-d\TH:i') : '') }}"
                   class="w-full px-4 py-2 mt-2 border rounded-lg @error('expiration_date') border-red-500 @enderror">
            @error('expiration_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        

        {{-- Usage Limit --}}
        <div class="mb-4">
            <label for="usage_limit" class="block text-gray-700">Tổng lượt sử dụng tối đa:</label>
            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('usage_limit') border-red-500 @enderror">
            @error('usage_limit')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Min Order Value --}}
        <div class="mb-4">
            <label for="min_order_value" class="block text-gray-700">	Giá trị đơn tối thiểu:</label>
            <input type="number" name="min_order_value" id="min_order_value" value="{{ old('min_order_value') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('min_order_value') border-red-500 @enderror">
            @error('min_order_value')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label for="status" class="block text-gray-700">Trạng thái:</label>
            <select name="status" id="status" class="w-full px-4 py-2 mt-2 border rounded-lg @error('status') border-red-500 @enderror">
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
            Tạo mã giảm giá
        </button>
        <a href="{{ route('coupons.index') }}" class="bg-gray-300 text-black px-6 py-2 rounded-lg hover:bg-gray-400 transition-all">Quay lại</a>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Lấy các phần tử
        const discountType = document.getElementById('discount_type');
        const maxDiscountValueDiv = document.getElementById('max_discount_value');

        // Hàm kiểm tra và ẩn/hiện max_discount_value
        function toggleMaxDiscount() {
            if (discountType.value === 'percentage') {
                maxDiscountValueDiv.style.display = 'block'; // Hiển thị
            } else {
                maxDiscountValueDiv.style.display = 'none'; // Ẩn
            }
        }

        // Gọi hàm khi trang được tải
        toggleMaxDiscount();

        // Lắng nghe sự kiện thay đổi của discount_type
        discountType.addEventListener('change', toggleMaxDiscount);
    });
</script>


@endsection
