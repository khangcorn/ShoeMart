@extends('client.layout')

@section('title', 'Thanh toán thất bại')

@section('content')
<div class="max-w-xl mx-auto mt-20 p-6 bg-white rounded shadow text-center">
    <h1 class="text-3xl font-bold mb-4 text-red-600">Thanh toán thất bại!</h1>
    <p class="mb-6">Giao dịch của bạn đã bị từ chối hoặc không thành công.</p>
    @if (session('error'))
        <p class="text-red-500 font-semibold mb-6">{{ session('error') }}</p>
    @endif

    <a href="{{ url('/orders') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
        Quay về danh sách đơn hàng
    </a>
</div>
@endsection
