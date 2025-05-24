@extends('admin.layout')

@section('title', 'Edit Coupon')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Sửa mã giảm giá</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('coupons.update', $coupon->coupon_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Tên</label>
                <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" class="w-full mt-1 px-3 py-2 border rounded-md" required>
            </div>
            <!-- Apply To -->
<div>
    <label for="apply_to" class="block text-sm font-medium text-gray-700">Áp dụng cho</label>
    <select name="apply_to" id="apply_to" class="w-full mt-1 px-3 py-2 border rounded-md" required>
        <option value="order" {{ old('apply_to', $coupon->apply_to) === 'order' ? 'selected' : '' }}>Order (Giảm đơn hàng)</option>
        <option value="shipping" {{ old('apply_to', $coupon->apply_to) === 'shipping' ? 'selected' : '' }}>Shipping (Giảm phí ship)</option>
    </select>
</div>


            <!-- Discount Type -->
            <div>
                <label for="discount_type" class="block text-sm font-medium text-gray-700">Loại</label>
                <select name="discount_type" id="discount_type" class="w-full mt-1 px-3 py-2 border rounded-md" required>
                    <option value="fixed" {{ $coupon->discount_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="percentage" {{ $coupon->discount_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                </select>
            </div>

            <!-- Discount Value -->
            <div>
                <label for="discount_value" class="block text-sm font-medium text-gray-700">Mức giảm</label>
                <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" step="0.01" class="w-full mt-1 px-3 py-2 border rounded-md" required>
            </div>
            
            <div class="mb-4" id="max_discount_value">
                <label for="max_discount_value" class="block text-gray-700">Mức giảm tối đa</label>
                <input type="number" name="max_discount_value" id="max_discount_value" value="{{ old('max_discount_value', $coupon->max_discount_value) }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('max_discount_value') border-red-500 @enderror">
                @error('max_discount_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Expiration Date -->
            <div>
                <label for="expiration_date" class="block text-sm font-medium text-gray-700">Thời hạn sử dụng</label>
                <input type="datetime-local" name="expiration_date" id="expiration_date"
    value="{{ old('expiration_date', $coupon->expiration_date ? \Carbon\Carbon::parse($coupon->expiration_date)->setTimezone('Asia/Ho_Chi_Minh')->format('Y-m-d\TH:i') : '') }}"
    class="w-full mt-1 px-3 py-2 border rounded-md" required>

            

            </div>

            <!-- Usage Limit -->
            <div>
                <label for="usage_limit" class="block text-sm font-medium text-gray-700">Tổng lượt sử dụng tối đa</label>
                <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="w-full mt-1 px-3 py-2 border rounded-md">
            </div>
            <!-- Min Order Value -->
<div>
    <label for="min_order_value" class="block text-sm font-medium text-gray-700">Giá trị đơn tối thiểu</label>
    <input type="number" name="min_order_value" id="min_order_value" value="{{ old('min_order_value', $coupon->min_order_value) }}" step="0.01" class="w-full mt-1 px-3 py-2 border rounded-md">
</div>


            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="status" id="status" class="w-full mt-1 px-3 py-2 border rounded-md" required>
                    <option value="active" {{ $coupon->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ $coupon->status === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="disabled" {{ $coupon->status === 'disabled' ? 'selected' : '' }}>Disabled</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 shadow">
                Cập nhật
            </button>
            <a href="{{ route('coupons.index') }}" class="ml-3 text-gray-600 hover:underline text-sm">← Quay lại</a>
        </div>
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
