@extends('client.layout')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-6">
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner" id="variant-images">
                    @foreach($variantImages as $key => $image)
                        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                            <img src="{{ asset($image->image_url) }}" class="d-block w-100" alt="Product Image">
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
            <div class="d-flex mt-3" id="variant-selection">
                @foreach($product->variants as $variant)
                    <a href="javascript:void(0)" class="me-2 variant-link" data-variant-id="{{ $variant->variant_id }}">
                        @if($variant->images->isNotEmpty())
                            <img src="{{ asset($variant->images->first()->image_url) }}" class="border rounded" width="60">
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
            <h4 id="variant-price">{{ number_format($firstVariant->price, 0, ',', '.') }} VND</h4>
            @if($firstVariant->price_sale)
                <p id="variant-sale-price" class="text-danger">Sale: {{ number_format($firstVariant->price_sale, 0, ',', '.') }} VND</p>
            @endif

            <p>{{ $product->description }}</p>

            <!-- Chọn size -->
            @if($sizes->isNotEmpty())
                <h5>Select Size</h5>
                <div class="d-flex flex-wrap gap-2" id="variant-sizes">
                    @foreach($sizes as $size)
                    <button class="btn btn-outline-dark {{ $size->stock > 0 ? '' : 'disabled' }}">
                        EU {{ $size->attribute_value }} ({{ $size->stock }} in stock)
                    </button>
                    @endforeach
                </div>
            @endif

            <!-- Nút thêm vào giỏ hàng -->
            <button class="btn btn-dark mt-3">Add to Bag</button>
        </div>
    </div>
</div>

@section('scripts')
<script>
       var productId = "{{ $product->product_id }}";
 document.querySelectorAll('.variant-link').forEach(function(element) {
    element.addEventListener('click', function() {
        var variantId = this.getAttribute('data-variant-id');
        
        fetch(`/products/${productId}/variant-details?variant_id=${variantId}`)

            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Cập nhật hình ảnh biến thể
                var imagesHtml = '';
                data.images.forEach(function(image) {
                    imagesHtml += `
                        <div class="carousel-item">
                            <img src="${image.image_url}" class="d-block w-100" alt="Variant Image">
                        </div>
                    `;
                });
                document.getElementById('variant-images').innerHTML = imagesHtml;

                // Cập nhật giá và giá sale
                document.getElementById('variant-price').textContent = data.price + ' VND';
                if (data.sale_price) {
                    document.getElementById('variant-sale-price').textContent = 'Sale: ' + data.sale_price + ' VND';
                    document.getElementById('variant-sale-price').style.display = 'block';
                } else {
                    document.getElementById('variant-sale-price').style.display = 'none';
                }

                // Cập nhật kích thước
                var sizesHtml = '';
                data.sizes.forEach(function(size) {
                    sizesHtml += `
                        <button class="btn btn-outline-dark ${size.stock > 0 ? '' : 'disabled'}">
                            EU ${size.attribute_value} (${size.stock} in stock)
                        </button>
                    `;
                });
                document.getElementById('variant-sizes').innerHTML = sizesHtml;
            })
            .catch(function(error) {
                console.error('Lỗi:', error);
            });
    });
});

</script>
@endsection

@endsection
