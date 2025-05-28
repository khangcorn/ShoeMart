@extends('client.layout')

@section('content')
<style>
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



    <div class="container mx-auto p-4 max-w-screen-lg mt-16">
        <div class="flex flex-wrap gap-4 md:flex-nowrap">
            <!-- Hình ảnh sản phẩm chính -->
            <div class="w-full md:w-1/2">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="flex space-x-3.5">
                                <!-- Cột chứa ảnh biến thể -->
                                <div class="flex flex-col space-y-2 overflow-y-auto h-full" id="variant-images-display">
                                    @foreach ($product->variants as $variant)
                                    @php
                                        $variantImage = optional($variant->images->first())->image_url;
                                    @endphp
                                
                                    <img class="" 
                                        src="{{ asset($variantImage ? 'storage/' . $variantImage : 'storage/default-image.jpg') }}" 
                                        alt="{{ $variant->color ?? 'No Color' }}" 
                                        onclick="updateProductDetails(this)">
                                @endforeach
                                
                                </div>

                                <!-- Ảnh chính -->
                                <div class="relative">
                                    @php
                                  $mainImage = optional($product->images->first())->image_url;
                                    @endphp
                                   <img id="main-product-image"
                                   class="object-cover w-auto h-[550px] " 
                                    alt="{{ $product->name }}"
                                   src="{{ asset($mainImage ? 'storage/' . $mainImage : 'storage/default-image.jpg') }}">

                                    <p
                                        class="absolute top-4 left-4 cursor-pointer border-[1px] bg-white border-gray-200 rounded-full px-4 py-2 flex gap-2 items-center">
                                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img"
                                            width="20px" height="20px" fill="none">
                                            <path fill="currentColor" fill-rule="evenodd" stroke="currentColor"
                                                stroke-width="1.5"
                                                d="M2.56 10.346l5.12 3.694-1.955 5.978c-.225.688.568 1.261 1.157.836L12 17.159l5.12 3.695c.587.425 1.381-.148 1.155-.836l-1.954-5.978 5.118-3.694c.589-.425.286-1.352-.442-1.352H14.67l-.166-.507-1.789-5.47c-.225-.69-1.205-.69-1.43 0L9.33 8.993H3.003c-.728 0-1.03.927-.442 1.352z"
                                                clip-rule="evenodd"></path>
                                        </svg> Highly Rated
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="w-full md:w-1/2 md:pl-8 mt-6 md:mt-0">
                <p class="font-semibold text-orange-600">Sustainable Materials </p>
                <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                <p class="text-gray-400 font-semibold">{{ $product->category->name }}</p>
               @php
                    $hasVariants = $product->variants && $product->variants->count() > 0;

                    if ($hasVariants) {
                        $lowestVariant = $product->variants->sortBy(function ($variant) {
                            return $variant->price_sale > 0 ? $variant->price_sale : $variant->price;
                        })->first();

                        $originalPrice = $lowestVariant->price;
                        $salePrice = $lowestVariant->price_sale;
                    } else {
                        $originalPrice = $product->price;
                        $salePrice = $product->price_sale;
                    }
                @endphp

                <p class="font-semibold py-2" id="product-price">
                    @if ($salePrice && $salePrice > 0)
                        <span class="line-through text-gray-500">{{ number_format($originalPrice, 0, ',', '.') }}</span>
                        / {{ number_format($salePrice, 0, ',', '.') }}
                    @else
                        {{ number_format($originalPrice, 0, ',', '.') }}
                    @endif
                    <span class="font-normal text-sm underline">đ</span>
                </p>

                
                
                
                <!-- Ảnh biến thể dưới -->
                <div class=" border-1 flex py-4 overflow-x-auto" id="variant-images-container">

                    @foreach ($product->variants as $variant)
                    <div class="w-1/5 variant-item" data-variant="{{ $variant->variant_id }}"
                        @php
                            $colorAttribute = optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Color'))->variantAttribute;
                            $sizeAttribute = optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Size'))->variantAttribute;
                            $variantImage = optional($variant->images->first())->image_url;

                            $originalPrice = $variant->price;
                            $salePrice = $variant->price_sale;
                        @endphp
                        data-color="{{ $colorAttribute ? $colorAttribute->attribute_value : 'N/A' }}"
                        data-size="{{ $sizeAttribute ? $sizeAttribute->attribute_value : 'N/A' }}"
                        data-price="{{ $originalPrice }}"
                        data-price-sale="{{ $salePrice }}"
                        data-stock="{{ $variant->stock }}"
                        data-images="{{ json_encode($variant->images) }}">
                
                        <img class="object-cover cursor-pointer w-[85px] h-[85px] rounded-md"
                            src="{{ asset($variantImage ? 'storage/' . $variantImage : 'storage/default-image.jpg') }}"
                            alt="{{ $variant->color ?? 'No Color' }}"
                            onclick="updateProductDetails(this)">
                    </div>
                @endforeach
                

                </div>
                <!-- Hiển thị màu sắc của sản phẩm -->
                <div class="hidden">
                    <div class=" mb-1 mt-4 flex justify-between">
                        <label for="color" class="block font-semibold">Color</label>
                      
                    </div>
                </div>
                <div class="py-2 flex justify-between">
                    <p for="size" class="font-semibold">Select size</p>
                    <p class="font-semibold flex items-center gap-2"> <svg aria-hidden="true" focusable="false"
                            viewBox="0 0 24 24" role="img" width="24px" height="24px" fill="none">
                            <path stroke="currentColor" stroke-width="1.5"
                                d="M21.75 10.5v6.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V10.5m3.308-2.25h12.885">
                            </path>
                            <path stroke="currentColor" stroke-width="1.5"
                                d="M15.79 5.599l2.652 2.65-2.652 2.653M8.21 5.599l-2.652 2.65 2.652 2.653M17.25 19v-2.5M12 19v-2.5M6.75 19v-2.5">
                            </path>
                        </svg> Size guide</p>
                </div>



                <!-- Hiển thị kích thước của sản phẩm -->
                {{-- <div class="">
                <div class="grid grid-cols-4 gap-2">
                    @php
                        $sizeArray = $sizes->toArray(); 
                    @endphp
            
                    @for ($size = 30; $size <= 41; $size++)
                        <p id="selected-size" class="px-3 hover:border-black transition ease-in-out duration-300 cursor-pointer py-2 text-center text-lg font-semibold border-[1.5px] border-gray-300 rounded-md 
                            {{ in_array($size, $sizeArray) ? 'bg-white text-black' : 'bg-white opacity-50 line-through' }}">
                            EU {{ $size }}
                        </p>
                    @endfor
                </div>
            </div> --}}
            <div>
                <div class="grid grid-cols-4 gap-2">
                    @php
                        $currentVariant = $product->variants->first(); // Lấy biến thể đầu tiên làm mặc định
                        $sizeArray = $currentVariant
                            ? $currentVariant->variantAttributeValues
                                ->where('variantAttribute.attribute_name', 'Size')
                                ->pluck('variantAttribute.attribute_value')
                                ->toArray()
                            : [];
                    @endphp
            
                    @for ($size = 30; $size <= 41; $size++)
                        <p class="px-3 hover:border-black transition ease-in-out duration-300 cursor-pointer py-2 text-center text-lg font-semibold border-[1.5px] border-gray-300 rounded-md size-option
                        {{ in_array($size, $sizeArray) ? 'bg-white text-black' : 'opacity-50 line-through bg-white hover:cursor-pointer' }}"
                            data-size   ="{{ $size }}" onclick="selectSize(this)">
                             {{ $size }}
                        </p>
                    @endfor
                </div>
            </div>
            
        
            
            
                <!-- Hiển thị kích thước đã chọn -->
                <div class="hidden">
                    <p id="selected-size" class="mt-2 text-sm text-gray-700">Chưa chọn kích thước</p>
                </div>
               @php
    $cartQuantity = $cartQuantity ?? 0;
    $hasVariant = $product->variants->isNotEmpty();
    $stock = $hasVariant ? $product->variants->first()->stock : $product->stock;
    $maxQuantity = max(1, $stock - $cartQuantity);
@endphp

<div class="flex items-center space-x-4 mt-4">
    <label for="quantity" class="font-semibold">Quantity:</label>

    <button type="button" class="px-3 py-2 bg-gray-200 rounded" onclick="updateQuantity(-1)">-</button>

    <input 
        id="quantity" 
        type="number" 
        class="w-16 text-center border border-gray-300 rounded-md"
        value="1" 
        min="1" 
        max="{{ $maxQuantity }}"
    >

    <button type="button" class="px-3 py-2 bg-gray-200 rounded" onclick="updateQuantity(1)">+</button>

    <span class="text-gray-600">
        Còn lại: 
        <span id="stock-remaining">{{ $stock }}</span>
    </span>
</div>

                

                <!-- Thêm vào giỏ hàng -->
                <div class="space-y-2 mt-8">
                   
                    <button id="add-to-bag"  
                    class="bg-black cursor-pointer hover:bg-gray-800 transition ease-in-out duration-200 text-white py-4 w-full rounded-full font-semibold" 
                    data-product="{{ $product->product_id }}"
                    @if($product->variants->isNotEmpty())
                        data-variant="{{ $product->variants->first()->variant_id }}" 
                    @endif>
                    Thêm vào giỏ hàng
                </button>
                
                        <form action="{{ route('wishlist.toggle') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                            <button type="submit"
                                class="bg-white hover:border-black transition ease-in-out duration-200 cursor-pointer font-semibold border-[1px] border-gray-400 text-black py-4 w-full rounded-full flex gap-2 items-center justify-center">
                                
                                @if(in_array($product->product_id, $wishlistedProductIds))
                                    <!-- Trái tim đầy khi đã yêu thích -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24" stroke="none">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 
                                                4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 
                                                14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 
                                                6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                @else
                                    <!-- Trái tim rỗng khi chưa yêu thích -->
                                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px" height="24px" fill="none">
                                        <path stroke="currentColor" stroke-width="1.5"
                                            d="M16.794 3.75c1.324 0 2.568.516 3.504 1.451a4.96 4.96 0 010 7.008L12 20.508l-8.299-8.299a4.96 4.96 0 010-7.007A4.923 4.923 0 017.205 3.75c1.324 0 2.568.516 3.504 1.451l.76.76.531.531.53-.531.76-.76a4.926 4.926 0 013.504-1.451">
                                        </path>
                                        <title>non-filled</title>
                                    </svg>
                                @endif
                                Yêu thích
                            </button>
                        </form>


                </div>
                <div class="py-8">
                    <p class="">Maximum cushioning in the Vomero provides a comfortable ride for everyday runs. Our softest, most cushioned ride has lightweight ZoomX foam stacked on top of responsive ReactX foam in the midsole. Plus, a redesigned traction pattern offers a smooth heel-to-toe transition.

                    </p>
                    <li id="selected-color" class=" ">Colour Shown:   <p  class="">
                        @forelse ($colors as $color)
                            <span>{{ $color }}</span>
                            @if (!$loop->last)
                                ,
                            @endif
                        @empty
                            <span>Chưa có màu</span>
                        @endforelse
                    </p></li>
                    <li class="">Style: HM6803-101</li>
                    <li class="">Country/Region of Origin: Vietnam</li>
                </div>
            
                



            </div>

        </div>
        <div class="mt-5">
            <style>
                .review {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: box-shadow 0.3s;
        }
        
        .review:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info strong {
            font-size: 16px;
            color: #333;
        }
        
        .review-time {
            font-size: 13px;
            color: #888;
        }
        
        .rating .star {
            font-size: 18px;
            color: #ccc;
            margin-right: 2px;
        }
        
        .rating .star.filled {
            color: #fbc02d;
        }
        
        .review-product-info {
            margin-top: 8px;
            font-size: 14px;
            color: #555;
        }
        
        .review-product-info .badge {
            font-size: 12px;
            background-color: #6c757d;
            color: #fff;
            margin-right: 4px;
        }
        
        .review-body {
            margin-top: 12px;
        }
        
        .review-comment {
            font-size: 14px;
            line-height: 1.5;
            color: #444;
        }
        
        .review-media {
            margin-top: 10px;
        }
        
        .review-media img {
            border-radius: 6px;
            border: 1px solid #ddd;
            transition: transform 0.2s ease;
        }
        
        .review-media img:hover {
            transform: scale(1.05);
        }
        .review .star {
            font-size: 18px;
            color: #ccc;
        }
        .review .star.filled {
            color: #ffc107; /* Bootstrap warning color */
        }
        .review-title {
    font-family: 'Times New Roman', serif;
    font-size: 22px;
    font-weight: bold;
    color: #333;
    border-bottom: 2px solid #ccc;
    padding-bottom: 5px;
    margin-top: 30px;
    margin-bottom: 20px;
    position: relative;
}

.review-title::before {
    content: "⭐";
    position: absolute;
    left: -25px;
    font-size: 20px;
    color: #f39c12;
}

        
            </style>
                <h4 class="review-title">Đánh giá từ người mua</h4>
                    <p>
                ⭐ Trung bình: {{ $averageRating }} / 5 
                ({{ $totalRatings }} lượt đánh giá)
            </p>
                 @include('client.products.product_reviews', ['reviews' => $reviews])
            </div>
    </div>




    </div>


  
    <script>
      document.addEventListener("DOMContentLoaded", function() {
    const firstVariant = document.querySelector(".variant-item");
    if (firstVariant) {
        updateProductDetails(firstVariant);
    }

    document.getElementById("add-to-bag").addEventListener("click", addToCart);
    
    document.querySelectorAll('.variant-item').forEach(variant => {
        variant.addEventListener('click', function() {
            updateProductDetails(this);
        });
    });

    // Sự kiện kiểm tra số lượng nhập vào
    document.getElementById("quantity").addEventListener("input", function () {
        let maxQuantity = parseInt(this.max);
        let minQuantity = parseInt(this.min);
        let value = parseInt(this.value);

        if (isNaN(value) || value < minQuantity) {
            this.value = minQuantity;
        } else if (value > maxQuantity) {
            this.value = maxQuantity;
        }
    });
    updateCartIcon(data.count);
});

function updateCartIcon(count) {
    const cartCountElement = document.getElementById("cart-count");
    if (cartCountElement) {
        cartCountElement.innerText = count;
        cartCountElement.style.display = count > 0 ? "flex" : "none";
    }
}


function addToCart() {
    let productId = this.getAttribute("data-product");
    let variantId = this.getAttribute("data-variant");
    let quantityInput = document.getElementById("quantity");
    let quantity = parseInt(quantityInput.value);

    fetch("/cart/add", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({ 
            product_id: productId, 
            variant_id: variantId, 
            quantity: quantity,
            current_url: window.location.href
        })
    })
    .then(async response => {
        const data = await response.json(); // ✅ Chỉ gọi 1 lần duy nhất

        if (!response.ok) {
            if (response.status === 401 && data.redirect) {
                const loginUrl = new URL(data.redirect, window.location.origin);
                loginUrl.searchParams.set('redirect', window.location.href);
                window.location.href = loginUrl.toString();
            } else {
                alert(data.message || "Lỗi khi thêm sản phẩm.");
            }
            return;
        }

        if (data.success) {
            alert("Thêm vào giỏ hàng thành công!");
            updateCartIcon(data.count);
        } else {
            alert(data.message || "Không thể thêm vào giỏ hàng.");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Lỗi khi thêm vào giỏ hàng!");
    });
}






function updateQuantity(change) {
    let quantityInput = document.getElementById('quantity');
    let maxStock = parseInt(quantityInput.getAttribute('max')); 
    let currentValue = parseInt(quantityInput.value);

    let newValue = currentValue + change;
    if (newValue < 1) newValue = 1; // Không nhỏ hơn 1
    if (newValue > maxStock) newValue = maxStock; // Không vượt quá tồn kho

    quantityInput.value = newValue;
}

function updateProductDetails(element) {
    document.querySelectorAll('.variant-item').forEach(variant => {
        variant.classList.remove('border-black'); 
    });

    element.classList.add('border-black');

    const variant = element.closest('.variant-item');
    const color = variant.getAttribute('data-color');
    const size = variant.getAttribute('data-size');
    const price = parseInt(variant.getAttribute('data-price'));
    const priceSale = parseInt(variant.getAttribute('data-price-sale'));
    const stock = parseInt(variant.getAttribute('data-stock')); 
    const imagesData = variant.getAttribute('data-images');
    const variantId = variant.getAttribute("data-variant");
      // Cập nhật số lượng còn lại trên giao diện
      document.getElementById("stock-remaining").textContent = stock;
    
    // Cập nhật max cho input số lượng
    document.getElementById("quantity").max = stock;

    let images = [];
    try {
        images = JSON.parse(imagesData);
    } catch (error) {
        console.error("Error parsing images data:", error);
    }

    document.getElementById('main-product-image').src = images.length > 0 ? `/storage/${images[0].image_url}` : '/storage/default-image.jpg';
    const formatCurrency = value => value.toLocaleString('vi-VN');

    let formattedPrice = '';
    if (priceSale && priceSale > 0) {
        formattedPrice = `<span class="line-through text-gray-500">${formatCurrency(price)}</span> / ${formatCurrency(priceSale)}`;
    } else {
        formattedPrice = `${formatCurrency(price)}`;
    }

    // Cập nhật vào DOM
    document.getElementById('product-price').innerHTML = `${formattedPrice} <span class="font-normal underline">đ</span>`;

    document.getElementById('selected-color').innerText = color;
    document.getElementById('selected-size').innerText = `EU ${size}`;

    updateSizeOptions(size);
    
    let addToBagBtn = document.getElementById("add-to-bag");
    if (addToBagBtn) {
        addToBagBtn.setAttribute("data-variant", variantId);
    }

    resetSizeSelection();
    updateVariantImages(images, color);

    // Cập nhật lại số lượng tối đa khi thay đổi biến thể
    let quantityInput = document.getElementById('quantity');
    quantityInput.max = stock;
    quantityInput.value = 1; 
}

function updateSizeOptions(selectedSize) {
    document.querySelectorAll('.size-option').forEach(sizeOption => {
        let sizeValue = sizeOption.getAttribute('data-size');

        if (selectedSize === sizeValue) {
            sizeOption.classList.remove('opacity-50', 'line-through', 'hover:cursor-pointer');
            sizeOption.classList.add('border-black', 'cursor-pointer');
            sizeOption.style.pointerEvents = 'auto';
        } else {
            sizeOption.classList.add('opacity-50', 'line-through', 'hover:cursor-pointer');
            sizeOption.classList.remove('border-black', 'cursor-pointer');
            sizeOption.style.pointerEvents = 'none';
        }
    });
}

function updateVariantImages(images, color) {
    const imagesContainer = document.getElementById('variant-images-display');
    imagesContainer.innerHTML = '';

    images.forEach(image => {
        const imageElement = document.createElement('img');
        imageElement.src = `/storage/${image.image_url}`;
        imageElement.alt = color;
        imageElement.classList.add(
            'object-cover', 'w-[65px]', 'h-[65px]', 
            'rounded-md', 'border', 'border-gray-200', 'cursor-pointer'
        );

        // Khi hover vào ảnh phụ => thay đổi ảnh chính
        imageElement.onmouseenter = function() {
            document.getElementById('main-product-image').src = imageElement.src;
        };

        imagesContainer.appendChild(imageElement);
    });
}


function selectSize(element) {
    document.querySelectorAll('.size-option').forEach(el => {
        el.classList.remove('ring-2', 'ring-black');
    });

    element.classList.add('ring-2', 'ring-black');
}

function resetSizeSelection() {
    document.querySelectorAll('.size-option').forEach(el => {
        el.classList.remove('ring-2', 'ring-black');
    });
}

    
    </script>
    
@endsection