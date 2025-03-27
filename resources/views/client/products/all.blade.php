@extends('client.layout')

@section('content')
    <div class="container">
        <h2 class="mb-4">Tất cả sản phẩm</h2>
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" class="card-img-top" alt="{{ $product->name }}">

                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Giá: {{ number_format($product->price, 0, ',', '.') }} VND</p>
                            <a href="{{ route('products.detail', $product->product_id) }}" class="btn btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
