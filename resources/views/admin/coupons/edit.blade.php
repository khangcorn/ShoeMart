@extends('admin.layout')

@section('title', 'Edit Coupon')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Coupon</h2>

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
                <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" class="w-full mt-1 px-3 py-2 border rounded-md" required>
            </div>
            <!-- Apply To -->
<div>
    <label for="apply_to" class="block text-sm font-medium text-gray-700">Apply To</label>
    <select name="apply_to" id="apply_to" class="w-full mt-1 px-3 py-2 border rounded-md" required>
        <option value="order" {{ old('apply_to', $coupon->apply_to) === 'order' ? 'selected' : '' }}>Order (Giảm đơn hàng)</option>
        <option value="shipping" {{ old('apply_to', $coupon->apply_to) === 'shipping' ? 'selected' : '' }}>Shipping (Giảm phí ship)</option>
    </select>
</div>


            <!-- Discount Type -->
            <div>
                <label for="discount_type" class="block text-sm font-medium text-gray-700">Discount Type</label>
                <select name="discount_type" id="discount_type" class="w-full mt-1 px-3 py-2 border rounded-md" required>
                    <option value="fixed" {{ $coupon->discount_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="percentage" {{ $coupon->discount_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                </select>
            </div>

            <!-- Discount Value -->
            <div>
                <label for="discount_value" class="block text-sm font-medium text-gray-700">Discount Value</label>
                <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" step="0.01" class="w-full mt-1 px-3 py-2 border rounded-md" required>
            </div>

            <!-- Max Discount Value -->
            <div>
                <label for="max_discount_value" class="block text-sm font-medium text-gray-700">Max Discount Value</label>
                <input type="number" name="max_discount_value" id="max_discount_value" value="{{ old('max_discount_value', $coupon->max_discount_value) }}" step="0.01" class="w-full mt-1 px-3 py-2 border rounded-md">
            </div>

            <!-- Expiration Date -->
            <div>
                <label for="expiration_date" class="block text-sm font-medium text-gray-700">Expiration Date</label>
                <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date', $coupon->expiration_date) }}" class="w-full mt-1 px-3 py-2 border rounded-md" required>
            </div>

            <!-- Usage Limit -->
            <div>
                <label for="usage_limit" class="block text-sm font-medium text-gray-700">Usage Limit</label>
                <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="w-full mt-1 px-3 py-2 border rounded-md">
            </div>
            <!-- Min Order Value -->
<div>
    <label for="min_order_value" class="block text-sm font-medium text-gray-700">Min Order Value</label>
    <input type="number" name="min_order_value" id="min_order_value" value="{{ old('min_order_value', $coupon->min_order_value) }}" step="0.01" class="w-full mt-1 px-3 py-2 border rounded-md">
</div>


            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
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
                Update Coupon
            </button>
            <a href="{{ route('coupons.index') }}" class="ml-3 text-gray-600 hover:underline text-sm">← Back to list</a>
        </div>
    </form>
</div>
@endsection
