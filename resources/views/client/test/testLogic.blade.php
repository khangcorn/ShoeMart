@extends('client.layout')

@section('content')




    <div class="container mx-auto p-4 max-w-screen-lg mt-16">
        <div class="flex flex-wrap gap-4 md:flex-nowrap">
            <!-- Hình ảnh sản phẩm chính -->
            <div class="w-full md:w-1/2">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="flex space-x-3.5">
                              
                                
                                </div>

                                <!-- Ảnh chính -->
                                <div class="relative">
                                    @php
                                  $mainImage = optional($product->images->first())->image_url;
                                    @endphp
                                   <img id="main-product-image"
                                   class="object-cover w-auto rounded-xl h-[300px] " 
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

            <!-- Thông tin sản phẩm -->
            <div class="w-full md:w-1/2 md:pl-8 mt-6 md:mt-0">
              
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

                
                
                
                @php
                $colorsGrouped = [];
                foreach ($product->variants as $variant) {
                    $colorAttribute = optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Color'))->variantAttribute;
                    $sizeAttribute = optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Size'))->variantAttribute;
                    $colorValue = $colorAttribute ? $colorAttribute->attribute_value : 'N/A';
                    $sizeValue = $sizeAttribute ? $sizeAttribute->attribute_value : 'N/A';
                
                    if (!isset($colorsGrouped[$colorValue])) {
                        $colorsGrouped[$colorValue] = [
                            'image' => optional($variant->images->first())->image_url,
                            'sizes' => [],
                        ];
                    }
                    if ($sizeValue !== 'N/A' && !in_array($sizeValue, $colorsGrouped[$colorValue]['sizes'])) {
                        $colorsGrouped[$colorValue]['sizes'][] = $sizeValue;
                    }
                }
                @endphp
                
                <div class="border-1 flex py-4 space-x-2 overflow-x-auto" id="variant-images-container">
                    @foreach ($colorsGrouped as $color => $data)
                        <div class="w-1/5 variant-item cursor-pointer"
                            data-color="{{ $color }}"
                            data-sizes="{{ json_encode($data['sizes']) }}"
                            onclick="updateProductDetails(this)">
                
                            <img class="object-cover w-[90px] h-[90px] rounded-md"
                                src="{{ asset($data['image'] ? 'storage/' . $data['image'] : 'storage/default-image.jpg') }}"
                                alt="{{ $color }}">
                
                            <p class="text-center mt-1 text-sm font-semibold">{{ $color }}</p>
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
                </div>



            <div>
                <div class="grid grid-cols-4 gap-2">
                    @php
                    // Lấy tất cả size từ tất cả các biến thể của sản phẩm
                    $sizeArray = $product->variants
                        ->flatMap(function ($variant) {
                            return $variant->variantAttributeValues
                                ->where('variantAttribute.attribute_name', 'Size')
                                ->pluck('variantAttribute.attribute_value');
                        })
                        ->unique()
                        ->toArray();
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
            </div>

        </div>
      
    </div>




    </div>

    <script>
        function updateProductDetails(el) {
            const selectedColor = el.getAttribute('data-color');
            filterSizesByColor(selectedColor);
        }
        
        function filterSizesByColor(color) {
            const variantItems = document.querySelectorAll('.variant-item');
            const sizeOptions = document.querySelectorAll('.size-option');
            let allowedSizes = new Set();
        
            variantItems.forEach(variant => {
                if (variant.getAttribute('data-color') === color) {
                    const size = variant.getAttribute('data-size');
                    if (size && size !== 'N/A') {
                        allowedSizes.add(size);
                    }
                }
            });
        
            sizeOptions.forEach(sizeEl => {
                const size = sizeEl.getAttribute('data-size');
                if (allowedSizes.has(size)) {
                    sizeEl.classList.remove('opacity-50', 'line-through');
                    sizeEl.classList.add('bg-white', 'text-black');
                    sizeEl.style.pointerEvents = 'auto';
                } else {
                    sizeEl.classList.add('opacity-50', 'line-through');
                    sizeEl.classList.remove('bg-white', 'text-black');
                    sizeEl.style.pointerEvents = 'none';
                }
            });
        }
        </script>
        
  
    
@endsection