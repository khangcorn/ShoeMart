@extends('client.layout')

@section('content')
    <div class="container mx-auto p-4 max-w-screen-lg mt-16">
        <div class="flex flex-wrap md:flex-nowrap">
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
                                    <img id="main-product-image"
                                        src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                        alt="{{ $product->name }}"
                                        class="object-cover w-[400px] h-[600px] rounded-lg border border-gray-200 ">

                                    <p
                                        class="absolute top-4 left-4 cursor-pointer border-[1px] border-gray-200 rounded-full px-4 py-2 flex gap-2 items-center">
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
                <p class="font-semibold py-2" id="product-price">
                    {{ number_format((int) $product->price, 0, ',', '.') }} <span class="font-normal text-sm underline">đ</span>
                </p>
                
                
                
                <!-- Ảnh biến thể dưới -->
                <div class=" border-1 flex py-4 overflow-x-auto" id="variant-images-container">

                    @foreach ($product->variants as $variant)
                    <div class="w-1/5 variant-item" data-variant-id="{{ $variant->id }}"
                        @php
                            $colorAttribute = optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Color'))->variantAttribute;
                            $sizeAttribute = optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Size'))->variantAttribute;
                            $variantImage = optional($variant->images->first())->image_url;
                        @endphp
                        data-color="{{ $colorAttribute ? $colorAttribute->attribute_value : 'N/A' }}"
                        data-size="{{ $sizeAttribute ? $sizeAttribute->attribute_value : 'N/A' }}"
                        data-price="{{ $variant->price }}"
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
                            data-size="{{ $size }}" onclick="selectSize(this)">
                            EU {{ $size }}
                        </p>
                    @endfor
                </div>
            </div>
            
        
            
            
                <!-- Hiển thị kích thước đã chọn -->
                <div class="hidden">
                    <p id="selected-size" class="mt-2 text-sm text-gray-700">Chưa chọn kích thước</p>
                </div>








                <!-- Thêm vào giỏ hàng -->
                <div class="space-y-2 mt-8">
                    <button class=" bg-black cursor-pointer hover:bg-gray-800 transition ease-in-out duration-200 text-white  py-4 w-full rounded-full font-semibold ">Add to
                        Bag</button>

                    <button
                        class="bg-white hover:border-black transition ease-in-out duration-200 cursor-pointer font-semibold border-[1px] border-gray-400 text-black py-4 w-full rounded-full flex gap-2 items-center justify-center">
                        Add to Favourite
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                            height="24px" fill="none">
                            <path stroke="currentColor" stroke-width="1.5"
                                d="M16.794 3.75c1.324 0 2.568.516 3.504 1.451a4.96 4.96 0 010 7.008L12 20.508l-8.299-8.299a4.96 4.96 0 010-7.007A4.923 4.923 0 017.205 3.75c1.324 0 2.568.516 3.504 1.451l.76.76.531.531.53-.531.76-.76a4.926 4.926 0 013.504-1.451">
                            </path>
                            <title>non-filled</title>
                        </svg>
                    </button>
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
                    <p class="font-semibold underline py-4">View Product Details</p>
                </div>
                <div x-data="{ isOpen: false }" class="w-full max-w-md mx-auto">
                    <!-- Header -->
                    <div class="flex py-4 p-2 border-b border-gray-200 items-center justify-between cursor-pointer" @click="isOpen=!isOpen">
                        <p class="font-semibold text-lg">Free Delivery and Returns</p>
                        <svg width="20px" height="20px"  viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" transform="rotate(270)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools --> <title>ic_fluent_ios_arrow_left_24_filled</title> <desc>Created with Sketch.</desc> <g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="ic_fluent_ios_arrow_left_24_filled" fill="#212121" fill-rule="nonzero"> <path d="M12.7270006,3.68663679 C13.1062197,3.28512543 13.0881482,2.6522184 12.6866368,2.27299937 C12.2851254,1.89378034 11.6522184,1.91185185 11.2729994,2.31336321 L2.77268886,11.3133632 C2.40871099,11.6987375 2.4086868,12.3011749 2.77263373,12.6865784 L11.2729442,21.6880264 C11.652131,22.0895682 12.2850366,22.1076905 12.6865784,21.7285038 C13.0881202,21.349317 13.1062426,20.7164114 12.7270558,20.3148696 L4.87515196,12.0000552 L12.7270006,3.68663679 Z" id="🎨-Color"> </path> </g> </g> </g></svg>
                    </div>
                 
                
                    <!-- Dropdown Content -->
                    <div x-show="isOpen" x-transition class="overflow-hidden rounded-md text-sm text-gray-600">
                        <p>
                            Your order of <span class="font-bold">5,000,000₫</span> or more gets free standard delivery.
                        </p>
                        <br />
                        <strong>Standard:</strong> delivered in 4-5 Business Days <br />
                        <strong>Express:</strong> delivered in 2-4 Business Days <br /><br />
                        Orders are processed and delivered Monday-Friday (excluding public holidays).<br /><br />
                        <span class="font-bold">Nike Members enjoy free returns.</span>
                    </div>
                </div>
                



            </div>

        </div>

    </div>




    </div>


  
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const firstVariant = document.querySelector(".variant-item");
                if (firstVariant) {
                    updateProductDetails(firstVariant);
                }
            });
        
            function updateProductDetails(element) {
                // Xóa trạng thái active của tất cả biến thể
                document.querySelectorAll('.variant-item').forEach(variant => {
                    variant.classList.remove('border-black'); 
                });
        
                // Đánh dấu biến thể đang chọn
                element.classList.add('border-black');
        
                // Lấy dữ liệu từ biến thể đã chọn
                const variant = element.closest('.variant-item');
                const color = variant.getAttribute('data-color');
                const size = variant.getAttribute('data-size');
                const price = variant.getAttribute('data-price');
                const imagesData = variant.getAttribute('data-images');
        
                let images = [];
                try {
                    images = JSON.parse(imagesData);
                } catch (error) {
                    console.error("Error parsing images data:", error);
                }
        
                // Cập nhật hình ảnh chính
                const mainImage = document.getElementById('main-product-image');
                mainImage.src = images.length > 0 ? `{{ asset('storage/') }}/${images[0].image_url}` : 'default-image.jpg';
        
                // Cập nhật giá
                document.getElementById('product-price').innerHTML = `${price} <span class="font-normal underline">đ</span>`;
        
                // Cập nhật màu sắc
                document.getElementById('selected-color').innerText = color;
        
                // Cập nhật kích thước
                document.getElementById('selected-size').innerText = `EU ${size}`;
        
                // Cập nhật trạng thái kích thước
                document.querySelectorAll('.size-option').forEach(sizeOption => {
                    let sizeValue = sizeOption.getAttribute('data-size');
        
                    if (size === sizeValue) {
                        sizeOption.classList.remove('opacity-50', 'line-through', 'hover:cursor-pointer');
                        sizeOption.classList.add('border-black', 'cursor-pointer');
                        sizeOption.style.pointerEvents = 'auto'; // Cho phép chọn size có hàng
                    } else {
                        sizeOption.classList.add('opacity-50', 'line-through', 'hover:cursor-pointer');
                        sizeOption.classList.remove('border-black', 'cursor-pointer');
                        sizeOption.style.pointerEvents = 'none'; // Chặn chọn size hết hàng
                    }
                });
        
                // Reset hiệu ứng ring
                resetSizeSelection();
        
                // Cập nhật danh sách ảnh biến thể
                const imagesContainer = document.getElementById('variant-images-display');
                imagesContainer.innerHTML = '';
        
                images.forEach(image => {
                    const imageElement = document.createElement('img');
                    imageElement.src = `{{ asset('storage/') }}/${image.image_url}`;
                    imageElement.alt = color;
                    imageElement.classList.add('object-cover', 'w-[65px]', 'h-[65px]', 'rounded-md', 'border',
                        'border-gray-200', 'cursor-pointer');
        
                    // Click vào ảnh nhỏ để đổi ảnh chính
                    imageElement.onclick = function() {
                        mainImage.src = imageElement.src;
                    };
        
                    imagesContainer.appendChild(imageElement);
                });
            }
        
            function selectSize(element) {
                // Xóa class "ring-2 ring-black" khỏi tất cả các size
                document.querySelectorAll('.size-option').forEach(el => {
                    el.classList.remove('ring-2', 'ring-black');
                });
        
                // Thêm class "ring-2 ring-black" vào size được chọn
                element.classList.add('ring-2', 'ring-black');
            }
        
            function resetSizeSelection() {
                // Xóa hiệu ứng ring khi thay đổi biến thể
                document.querySelectorAll('.size-option').forEach(el => {
                    el.classList.remove('ring-2', 'ring-black');
                });
            }
        </script>
        
@endsection
