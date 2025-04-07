@extends('client.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Đơn hàng của bạn đã được đặt thành công!</h2>
    @if(session('success'))
        <p class="text-green-600 font-bold">{{ session('success') }}</p>
    @endif
    <a href="{{ route('cart.index') }}" class="mt-4 inline-block bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition">
        Quay lại giỏ hàng
    </a>
    <a href="{{  url('/')  }}" class="mt-4 inline-block bg-gray-500 text-white py-2 px-6 rounded-md hover:bg-gray-600 transition ml-4">
        Tiếp tục mua hàng
    </a>
</div>
@endsection
