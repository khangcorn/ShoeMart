@extends('admin.layout')

@section('content')
<style>
    <style>
    .alert-danger {
        background-color: #f8d7da;
        padding: 10px;
        border: 1px solid #f5c2c7;
        border-radius: 5px;
    }
</style>

</style>
@if ($errors->any())
    <div class="alert alert-danger" id="error-message">
        <ul>
            @foreach ($errors->all() as $error)
                <li style="color: red;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Tạo tài khoản nhân viên</h2>



    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold mb-1">Tên đăng nhập</label>
            <input type="text" name="username" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Mật khẩu</label>
            <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded" required>
        </div>

      <div class="flex justify-between mt-4">
    
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Tạo tài khoản
    </button>
    <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        Quay lại
    </a>
</div>
    </form>
</div>
@endsection
<script>
    setTimeout(function () {
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.transition = 'opacity 0.5s ease';
            errorMessage.style.opacity = '0';
            setTimeout(() => errorMessage.remove(), 500); // Xoá khỏi DOM sau hiệu ứng
        }
    }, 5000);
</script>
