@extends('admin.layout')

@section('title', 'Create Coupon')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-4">Create Coupon</h1>

    <form action="{{ route('coupons.store') }}" method="POST" class="bg-white shadow-md p-6 rounded-lg">
        @csrf

        {{-- Code --}}
        <div class="mb-4">
            <label for="code" class="block text-gray-700">Code:</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('code') border-red-500 @enderror">
            @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Discount Type --}}
        <div class="mb-4">
            <label for="discount_type" class="block text-gray-700">Discount Type:</label>
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
            <label for="discount_value" class="block text-gray-700">Discount Value:</label>
            <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('discount_value') border-red-500 @enderror">
            @error('discount_value')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Max Discount Value --}}
        <div class="mb-4">
            <label for="max_discount_value" class="block text-gray-700">Max Discount Value:</label>
            <input type="number" name="max_discount_value" id="max_discount_value" value="{{ old('max_discount_value') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('max_discount_value') border-red-500 @enderror">
            @error('max_discount_value')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Expiration Date --}}
        <div class="mb-4">
            <label for="expiration_date" class="block text-gray-700">Expiration Date:</label>
            <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('expiration_date') border-red-500 @enderror">
            @error('expiration_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Usage Limit --}}
        <div class="mb-4">
            <label for="usage_limit" class="block text-gray-700">Usage Limit:</label>
            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" class="w-full px-4 py-2 mt-2 border rounded-lg @error('usage_limit') border-red-500 @enderror">
            @error('usage_limit')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label for="status" class="block text-gray-700">Status:</label>
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
            Create Coupon
        </button>
    </form>
</div>
@endsection
