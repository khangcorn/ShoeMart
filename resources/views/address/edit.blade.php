@extends('client.layout')

@section('content')

<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Sửa địa chỉ</h2>

    <form action="{{ route('address.update', $address->address_id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Sử dụng PUT để cập nhật dữ liệu -->

        <div class="mb-4">
            <label for="recipient_name" class="block text-sm font-medium text-gray-700">Tên người nhận</label>
            <input type="text" id="recipient_name" name="recipient_name" class="w-full p-2 border border-gray-300 rounded-md" value="{{ $address->recipient_name }}" required>
        </div>

        <div class="mb-4">
            <label for="street_address" class="block text-sm font-medium text-gray-700">Địa chỉ</label>
            <input type="text" id="street_address" name="street_address" class="w-full p-2 border border-gray-300 rounded-md" value="{{ $address->street_address }}" required>
        </div>

        <div class="mb-4">
            <label for="ward" class="block text-sm font-medium text-gray-700">Phường/Xã</label>
            <input type="text" id="ward" name="ward" class="w-full p-2 border border-gray-300 rounded-md" value="{{ $address->ward }}" required>
        </div>

        <div class="mb-4">
            <label for="district" class="block text-sm font-medium text-gray-700">Quận/Huyện</label>
            <input type="text" id="district" name="district" class="w-full p-2 border border-gray-300 rounded-md" value="{{ $address->district }}" required>
        </div>

        <div class="mb-4">
            <label for="city" class="block text-sm font-medium text-gray-700">Thành phố</label>
            <input type="text" id="city" name="city" class="w-full p-2 border border-gray-300 rounded-md" value="{{ $address->city }}" required>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded-md">
                Cập nhật địa chỉ
            </button>
        </div>
    </form>
</div>
@endsection
