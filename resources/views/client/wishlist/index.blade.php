@extends('client.layout')

@section('css')
@endsection

@section('content')
    <div class="mt-24 px-6 lg:px-28">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Danh sách sản phẩm yêu thích</h2>

            @if ($wishlist->isEmpty())
                <div class="text-center py-16 text-gray-500">
                    <p class="text-xl">Không có sản phẩm nào trong danh sách yêu thích 😢</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($wishlist as $item)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300">
                            <a href="{{ route('products.detail', ['id' => $item->product->product_id]) }}">
                                <img src="{{ getImage($item->product->mainImage->image_url) }}"
                                    alt="{{ $item->product->name }}"
                                    class="w-full h-48 object-cover">
                            </a>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                                    <a href="{{ route('products.detail', ['id' => $item->product->product_id]) }}"
                                        class="hover:text-blue-600 transition">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                <div class="mb-4">
                                    @if ($item->product->price_sale && $item->product->price_sale < $item->product->price)
                                        <span class="text-red-600 font-bold text-lg">
                                            {{ format_cash($item->product->price_sale) }}
                                        </span>
                                        <span class="text-sm text-gray-500 line-through ml-2">
                                            {{ format_cash($item->product->price) }}
                                        </span>
                                    @else
                                        <span class="text-gray-800 font-bold text-lg">
                                            {{ format_cash($item->product->price) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('products.detail', ['id' => $item->product->product_id]) }}"
                                        class="text-sm text-blue-600 hover:underline">
                                        Xem chi tiết
                                    </a>
                                    <button onclick="window.location.href='{{ route('wishlist.delete', ['id' => $item->product->product_id]) }}'"
                                        class="text-red-500 hover:text-red-700 transition-all" title="Xoá khỏi yêu thích">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
@endsection
