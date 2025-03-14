@extends('client.layout')

@section('title', 'Trang chủ')

@section('content')
    <div class="container">
        <h1 class="my-4">Danh sách sản phẩm</h1>
        <div class="row">
            @foreach($products as $product)
                @php
                    // Lấy biến thể đầu tiên nếu có
                    $variant = $product->variants->first();
                    // Lấy giá của biến thể nếu có, nếu không thì lấy giá sản phẩm
                    $displayPrice = $variant ? $variant->price : $product->price;
                    // Lấy ảnh của biến thể nếu có, nếu không thì lấy ảnh sản phẩm
                    $image = $variant && $variant->images->isNotEmpty() ? $variant->images->first()->image_url : ($product->images->isNotEmpty() ? $product->images->first()->image_url : 'default.jpg');
                @endphp

                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ asset('storage/' . ltrim($image, '/storage/')) }}" class="card-img-top">

                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Giá: {{ number_format($displayPrice, 0, ',', '.') }} VND</p>
                            <a href="{{ route('products.detail', ['id' => $product->product_id]) }}" class="btn btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
