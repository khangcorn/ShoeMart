@extends('client.component.link')

@if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-md mb-4">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-500 text-white p-4 rounded-md mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Đăng Ký</h2>

        <!-- Form đăng ký -->
        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="username" value="{{ old('username') }}" placeholder="Nhập tên người dùng">
                @error('username')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="email" value="{{ old('email') }}" placeholder="Nhập email">
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại">
                @error('phone')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="password" placeholder="Nhập mật khẩu">
                @error('password')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="password_confirmation" placeholder="Xác nhận mật khẩu">
                @error('password_confirmation')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none transition duration-200 mt-4">
                Đăng ký
            </button>
        </form>

        <!-- Liên kết đăng nhập -->
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Đã có tài khoản? Đăng nhập</a>
        </div>
    </div>
</div>
