@extends('client.layout')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="container mx-auto max-w-md mt-10">
    <h2 class="text-2xl font-semibold mb-6">Đổi mật khẩu</h2>

    {{-- Thông báo bắt buộc đổi mật khẩu lần đầu --}}
    <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded">
        Đây là lần đăng nhập đầu tiên, bạn bắt buộc phải đổi mật khẩu để tiếp tục.
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.change') }}">
        @csrf
<div class="mb-4">
    <label for="current_password" class="block mb-1 font-medium">Mật khẩu hiện tại</label>
    <input id="current_password" type="password" name="current_password" required
           class="w-full border rounded px-3 py-2 @error('current_password') border-red-500 @enderror">
    @error('current_password')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
        <div class="mb-4">
            
            <label for="password" class="block mb-1 font-medium">Mật khẩu mới</label>
            <input id="password" type="password" name="password" required autofocus
                   class="w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block mb-1 font-medium">Xác nhận mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Đổi mật khẩu
        </button>
    </form>
</div>
@endsection
