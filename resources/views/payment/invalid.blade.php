@extends('client.layout')

@section('title', 'Thanh toán không hợp lệ')

@section('content')
<div class="max-w-xl mx-auto mt-20 p-6 bg-white rounded shadow text-center">
    <h1 class="text-3xl font-bold mb-4 text-yellow-600">Thanh toán không hợp lệ!</h1>
    <p class="mb-6">Thông tin giao dịch không hợp lệ hoặc bị giả mạo.</p>

    <a href="{{ url('/orders') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
        Quay về danh sách đơn hàng
    </a>
</div>
@endsection
