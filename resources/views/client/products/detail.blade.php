@extends('client.layout')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Hình ảnh sản phẩm chính -->
        <div class="w-full md:w-1/2">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img id="main-product-image" src="{{ asset('storage/' . $product->images->first()->image_url) }}" alt="{{ $product->name }}" class="object-cover w-full h-96 rounded-md">
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="w-full md:w-1/2 md:pl-8 mt-6 md:mt-0">
            <h1 class="text-3xl font-semibold text-gray-900">{{ $product->name }}</h1>
            <p class="text-gray-400 text-sm">{{ $product->category->name }}</p>
            <p class="font-semibold text-xl mt-2" id="product-price">
                {{ number_format($product->price, 0, '.', ',') }} <span class="font-normal underline">đ</span>
            </p>
            <p class="mt-4">{{ $product->description }}</p>

            <!-- Hiển thị màu sắc của sản phẩm -->
            <div class="mt-4">
                <label for="color" class="block text-sm text-gray-700">Màu sắc:</label>
                <p id="selected-color" class="mt-2 text-sm text-gray-700">
                    @forelse ($colors as $color)
                        <span>{{ $color }}</span> @if (!$loop->last), @endif
                    @empty
                        <span>Chưa có màu</span>
                    @endforelse
                </p>
            </div>
            
            <!-- Hiển thị kích thước của sản phẩm -->
            <div class="mt-4">
                <label for="size" class="block text-sm text-gray-700">Kích thước:</label>
                <p id="selected-size" class="mt-2 text-sm text-gray-700">
                    @forelse ($sizes as $size)
                        <span>{{ $size }}</span> @if (!$loop->last), @endif
                    @empty
                        <span>Chưa có kích thước</span>
                    @endforelse
                </p>
            </div>
            
            

            <!-- Thêm vào giỏ hàng -->
            <button class="mt-6 bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition">Thêm vào giỏ hàng</button>
        </div>
    </div>

    <!-- Ảnh biến thể dưới -->
    <div class="mt-6 flex space-x-4 overflow-x-auto" id="variant-images-container">
        @foreach ($product->variants as $variant)
        <div class="w-1/4 p-2 variant-item" data-variant-id="{{ $variant->id }}" 
            data-color="{{ $variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Color')->variantAttribute->attribute_value }}" 
            data-size="{{ $variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Size')->variantAttribute->attribute_value }}" 
            data-price="{{ $variant->price }}" 
            data-images="{{ json_encode($variant->images) }}">
            @php
                $variantImage = $variant->images->first();
            @endphp
            <img src="{{ asset('storage/' . $variantImage->image_url) }}" alt="{{ $variant->color }}" class="object-cover w-full h-32 rounded-md cursor-pointer" 
                 onclick="updateProductDetails(this)">
        </div>
    @endforeach
    
    </div>

    <!-- Ảnh biến thể phụ sẽ hiển thị dưới ảnh chính -->
    <div class="mt-6 flex space-x-4 overflow-x-auto" id="variant-images-display">
        <!-- Các ảnh sẽ được chèn vào đây khi chọn biến thể -->
    </div>
</div>

<script>
  function updateProductDetails(element) {
    // Lấy dữ liệu từ biến thể đã chọn
    const variant = element.closest('.variant-item');
    const color = variant.getAttribute('data-color');
    const size = variant.getAttribute('data-size');
    const price = variant.getAttribute('data-price');
    const images = JSON.parse(variant.getAttribute('data-images'));
  
    // Cập nhật hình ảnh chính
    document.getElementById('main-product-image').src = `{{ asset('storage/') }}/${images[0].image_url}`;
  
    // Cập nhật giá
    document.getElementById('product-price').innerHTML = `${price} <span class="font-normal underline">đ</span>`;
  
    // Cập nhật màu sắc và kích thước đã chọn
    document.getElementById('selected-color').innerText = color;  // Cập nhật màu sắc
    document.getElementById('selected-size').innerText = size;    // Cập nhật kích thước
  
    // Cập nhật các ảnh biến thể dưới
    const imagesContainer = document.getElementById('variant-images-display');
    imagesContainer.innerHTML = ''; // Xóa các ảnh cũ
  
    images.forEach(function(image) {
        const imageElement = document.createElement('img');
        imageElement.src = `{{ asset('storage/') }}/${image.image_url}`;
        imageElement.alt = color;
        imageElement.classList.add('object-cover', 'w-full', 'h-32', 'rounded-md');
        
        // Thêm sự kiện onclick cho mỗi ảnh để thay đổi ảnh chính
        imageElement.onclick = function() {
            document.getElementById('main-product-image').src = imageElement.src;
        };
  
        imagesContainer.appendChild(imageElement);
    });
}

  </script>
  
@endsection
