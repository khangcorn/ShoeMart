@extends('client.layout')

@section('title', 'Trang chủ')

@section('content')

    <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
        <div class="">
            <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_1704,c_limit/e0b60c2f-d245-42e9-86ca-f7ea95ba6d45/nike-just-do-it.jpg" alt="">
            <div>
                <p></p>
                <p></p>
                <p></p>
            </div>
        </div>
        <h1 class="my-4">Danh sách sản phẩm</h1>
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ asset('./storage/' . $product->images->first()->image_url) }}" class="card-img-top">

                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Giá: {{ number_format($product->price, 0, ',', '.') }} VND</p>
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
