@extends('client.layout')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-6">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($variantImages as $key => $image)
                        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                            <img src="{{ asset('' . $image->image_url) }}" class="d-block w-100" alt="Product Image">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
            

            <!-- Danh sách biến thể (màu sắc) -->
            <div class="d-flex mt-3">
                @foreach($product->variants as $variant)
                    <a href="{{ route('products.detail', ['id' => $product->product_id, 'variant' => $variant->variant_id]) }}" class="me-2">
                        @if($variant->images->isNotEmpty())
                            <img src="{{ asset('' . $variant->images->first()->image_url) }}" class="border rounded" width="60">
                        @else
                            <img src="{{ asset('storage/default-image.jpg') }}" class="border rounded" width="60">
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-6">
            <h2>{{ $product->name }}</h2>
            <p class="text-muted">{{ $product->category->name ?? 'No Category' }}</p>
            
            <!-- Giá sản phẩm -->
            <h4>{{ number_format($firstVariant->price, 0, ',', '.') }} VND</h4>
            @if($firstVariant->price_sale)
                <p class="text-danger">Sale: {{ number_format($firstVariant->price_sale, 0, ',', '.') }} VND</p>
            @endif

            <p>{{ $product->description }}</p>

            <!-- Chọn size -->
            <h5>Select Size</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($sizes as $size)
                    <button class="btn btn-outline-dark {{ $size->stock > 0 ? '' : 'disabled' }}">
                        EU {{ $size->attribute_value }} <!-- Hiển thị kích thước -->
                    </button>
                @endforeach
            </div>
            

            <!-- Nút thêm vào giỏ hàng -->
            <button class="btn btn-dark mt-3">Add to Bag</button>
        </div>
    </div>
</div>
@endsection
