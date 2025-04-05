@extends('admin.layout')

@section('title', 'Edit Shipping Fee')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Edit Shipping Fee</h2>

    <form method="POST" action="{{ route('shipping-fees.update', $shippingFee->shipping_id) }}">
        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Province</label>
            <input type="text" name="province" value="{{ old('province', $shippingFee->province) }}" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">District (optional)</label>
            <input type="text" name="district" value="{{ old('district', $shippingFee->district) }}" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Ward (optional)</label>
            <input type="text" name="ward" value="{{ old('ward', $shippingFee->ward) }}" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Fee (VNĐ)</label>
            <input type="number" step="0.01" name="fee" value="{{ old('fee', $shippingFee->fee) }}" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('shipping-fees.index') }}" class="bg-gray-300 text-black px-6 py-2 rounded-lg hover:bg-gray-400 transition-all">Back</a>
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-all">Save Changes</button>
        </div>
    </form>
</div>
@endsection
