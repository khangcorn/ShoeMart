@extends('admin.layout')

@section('title', 'Edit Order Status')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Chỉnh sửa trạng thái đơn hàng</h2>

    <form method="POST" action="{{ route('order-statuses.update', $status->status_id) }}">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Tên trạng thái</label>
            <input type="text" name="name" value="{{ old('name', $status->name) }}" class="w-full border rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Mô tả (tuỳ chọn)</label>
            <textarea name="description" rows="4" class="w-full border rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $status->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('order-statuses.index') }}" class="bg-gray-300 text-black px-6 py-2 rounded-lg hover:bg-gray-400 transition-all">Back</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all">Update</button>
        </div>
    </form>
</div>
@endsection
