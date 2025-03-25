@extends('client.component.link')

@if(session('status'))
    <div class="bg-green-500 text-white p-4 rounded-md mb-4">
        {{ session('status') }}
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
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Đặt lại mật khẩu</h2>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Địa chỉ email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" value="{{ old('email', $email) }}" required>
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password Input -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" required>
                @error('password')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" required>
                @error('password_confirmation')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none transition duration-200 mt-4">
                Đặt lại mật khẩu
            </button>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Quay lại trang đăng nhập</a>
        </div>
    </div>
</div>
