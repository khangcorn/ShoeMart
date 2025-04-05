@extends('admin.layout')

@section('title', 'Create Coupon')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold mb-4">Create Coupon</h1>

        <form action="{{ route('coupons.store') }}" method="POST" class="bg-white shadow-md p-6 rounded-lg">
            @csrf
            <div class="mb-4">
                <label for="code" class="block text-gray-700">Code:</label>
                <input type="text" name="code" id="code" class="w-full px-4 py-2 mt-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label for="discount_type" class="block text-gray-700">Discount Type:</label>
                <select name="discount_type" id="discount_type" class="w-full px-4 py-2 mt-2 border rounded-lg">
                    <option value="fixed">Fixed</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="discount_value" class="block text-gray-700">Discount Value:</label>
                <input type="number" name="discount_value" id="discount_value" class="w-full px-4 py-2 mt-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label for="max_discount_value" class="block text-gray-700">Max Discount Value:</label>
                <input type="number" name="max_discount_value" id="max_discount_value" class="w-full px-4 py-2 mt-2 border rounded-lg">
            </div>

            <div class="mb-4">
                <label for="expiration_date" class="block text-gray-700">Expiration Date:</label>
                <input type="date" name="expiration_date" id="expiration_date" class="w-full px-4 py-2 mt-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label for="usage_limit" class="block text-gray-700">Usage Limit:</label>
                <input type="number" name="usage_limit" id="usage_limit" class="w-full px-4 py-2 mt-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700">Status:</label>
                <select name="status" id="status" class="w-full px-4 py-2 mt-2 border rounded-lg">
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
                Create Coupon
            </button>
        </form>
    </div>
@endsection
