@extends('client.component.link')
@if (session('success'))
    <div class="bg-green-500 text-white p-4 rounded-md mb-4">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-500 text-white p-4 rounded-md mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">

        <h2 class="text-2xl font-semibold text-center mb-6">Đăng Nhập</h2>

        <!-- Form đăng nhập -->
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" required>
                @error('email')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mật khẩu -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" required>
                @error('password')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Button đăng nhập -->
            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none transition duration-200">
                Đăng nhập
            </button>
        </form>

        <!-- Các liên kết -->
        <div class="mt-4 text-center">
            <a href="{{ route('register.form') }}" class="text-blue-600 hover:underline">Đăng ký</a> | 
            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">Quên mật khẩu</a>
        </div>

    </div>
</div>
