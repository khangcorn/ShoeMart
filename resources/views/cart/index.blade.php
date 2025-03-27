@extends('client.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Giỏ Hàng</h2>

    @if($cartItems->isEmpty())
        <p class="text-lg">Giỏ hàng của bạn đang trống.</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300" id="cart-table">
            <thead>
                <tr>
                    <th class="px-4 py-2 border border-gray-300">Sản phẩm</th>
                    <th class="px-4 py-2 border border-gray-300">Giá</th>
                    <th class="px-4 py-2 border border-gray-300">Số lượng</th>
                    <th class="px-4 py-2 border border-gray-300">Tổng</th>
                    <th class="px-4 py-2 border border-gray-300">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                <tr>
                    <td class="px-4 py-2 border border-gray-300">{{ $item->product->name }}</td>
                    <td class="px-4 py-2 border border-gray-300">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                    <td class="px-4 py-2 border border-gray-300">
                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-quantity-form">
                            @csrf
                            @method('PUT')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="quantity-input px-2 py-1 border border-gray-300 rounded-md" data-item-id="{{ $item->id }}">
                            <button type="submit" class="hidden">Cập nhật</button>
                        </form>
                    </td>
                    <td class="px-4 py-2 border border-gray-300">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                    <td class="px-4 py-2 border border-gray-300">
                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="delete-item-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white py-1 px-4 rounded-md hover:bg-red-600">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            <p class="font-semibold text-lg" id="total-price"><strong>Tổng tiền: </strong>{{ number_format($total, 0, ',', '.') }} đ</p>

            <form action="{{ route('cart.clear') }}" method="POST" class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-gray-600 text-white py-2 px-6 rounded-md hover:bg-gray-700">Xóa toàn bộ giỏ hàng</button>
            </form>

            <a href="{{ route('order.create') }}" class="mt-4 inline-block bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition">Tiến hành đặt hàng</a>
        </div>
    @endif
</div>
