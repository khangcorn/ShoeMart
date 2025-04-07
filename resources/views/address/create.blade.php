@extends('client.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Thêm Địa Chỉ Mới</h2>
    
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('address.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="recipient_name" class="block text-gray-700">Tên người nhận</label>
            <input type="text" name="recipient_name" id="recipient_name" 
                   class="w-full p-2 border border-gray-300 rounded" 
                   value="{{ old('recipient_name') }}" required>
            @error('recipient_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="recipient_phone" class="block text-gray-700">Số điện thoại</label>
            <input type="text" name="recipient_phone" id="recipient_phone" 
                   class="w-full p-2 border border-gray-300 rounded" 
                   value="{{ old('recipient_phone') }}" required>
            @error('recipient_phone')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="address_name" class="block text-gray-700">Tên địa chỉ (ví dụ: Nhà riêng, Công ty...)</label>
            <input type="text" name="address_name" id="address_name" 
                   class="w-full p-2 border border-gray-300 rounded" 
                   value="{{ old('address_name') }}">
            @error('address_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <!-- Thay 'province' thành 'city' -->
        <div class="mb-4">
            <label for="city" class="block text-gray-700">Thành phố</label>
            <input type="text" name="city" id="city" 
                   class="w-full p-2 border border-gray-300 rounded" 
                   value="{{ old('city') }}" required>
            @error('city')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="district" class="block text-gray-700">Quận/Huyện</label>
            <input type="text" name="district" id="district" 
                   class="w-full p-2 border border-gray-300 rounded" 
                   value="{{ old('district') }}" required>
            @error('district')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="ward" class="block text-gray-700">Phường/Xã</label>
            <input type="text" name="ward" id="ward" 
                   class="w-full p-2 border border-gray-300 rounded" 
                   value="{{ old('ward') }}" required>
            @error('ward')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="street_address" class="block text-gray-700">Địa chỉ</label>
            <input type="text" name="street_address" id="street_address" 
                   class="w-full p-2 border border-gray-300 rounded" 
                   value="{{ old('street_address') }}" required>
            @error('street_address')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="is_default" class="block text-gray-700">Đặt làm địa chỉ mặc định?</label>
            <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
            @error('is_default')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded-md">
                Lưu địa chỉ
            </button>
            <a href="{{ route('cart.checkout') }}" class="bg-gray-300 text-gray-800 py-2 px-6 rounded-md hover:bg-gray-400 transition">
                Quay lại trang đơn hàng
            </a>
        </div>
    </form>
</div>
@endsection
