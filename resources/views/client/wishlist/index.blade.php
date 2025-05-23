@extends('client.layout')

@section('css')
@endsection

@section('content')
<style>
    .alert {
  padding: 15px 20px;
  margin: 20px auto;
  max-width: 600px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 16px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  opacity: 1;
}

/* Màu nền và chữ theo type thông báo */
.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.alert-warning {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}

</style>
@if(session('message'))
    <div id="alert-message" class="alert alert-{{ session('type') }}">
        {{ session('message') }}
    </div>

    <script>
        setTimeout(() => {
            const alert = document.getElementById('alert-message');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000); // 5000 ms = 5 giây
    </script>
@endif

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
                                @php
                            $image = null;

                            if ($item->variant) {
                                // Ưu tiên ảnh có variant_id trùng khớp
                                $image = $item->product->images->firstWhere('variant_id', $item->variant->variant_id);
                            }

                            // Nếu không có ảnh của biến thể, dùng ảnh chính (main)
                            if (!$image) {
                                $image = $item->product->images->firstWhere('type', 'main');
                            }
                        @endphp
                                <img src="{{ asset('storage/' . $image->image_url) }}"
                                alt="{{ $item->product->name }}"
                                class="w-16 h-16 object-cover rounded-md border transition-transform duration-200 hover:scale-105 hover:shadow-md">
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
                                            {{ number_format($item->product->price_sale, 0, ',', '.') }}₫
                                        </span>
                                        <span class="text-sm text-gray-500 line-through ml-2">
                                           {{ number_format($item->product->price, 0, ',', '.') }}₫
                                        </span>
                                    @else
                                       <span class="text-gray-800 font-bold text-lg">
                                            {{ number_format($item->product->price, 0, ',', '.') }}₫
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('products.detail', ['id' => $item->product->product_id]) }}"
                                        class="text-sm text-blue-600 hover:underline">
                                        Xem chi tiết
                                    </a>
                                    <form id="delete-wishlist-{{ $item->product->product_id }}" action="{{ route('wishlist.delete') }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="product_id" value="{{ $item->product->product_id }}">
                                    </form>

                                    <button onclick="event.preventDefault(); if(confirm('Bạn có chắc muốn xoá sản phẩm khỏi yêu thích?')) document.getElementById('delete-wishlist-{{ $item->product->product_id }}').submit();"
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
