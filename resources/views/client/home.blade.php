@extends('client.layout')

@section('title', 'Trang chủ')

@section('content')

<<<<<<< HEAD
    <div class=" mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
        {{-- <h1 class="my-4">Danh sách sản phẩm</h1> --}}
        <div class="row">
            

    <div class="  ">
        <div class="">
            <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_1704,c_limit/e0b60c2f-d245-42e9-86ca-f7ea95ba6d45/nike-just-do-it.jpg"
                alt="">
            <div class="text-center mt-4">
                <p class="mb-0 font-semibold">Just In</p>
                <span class="uppercase text-4xl font-bold"> Air Max Dn8</span>
                <p class="font-semibold"> Introducing the next chapter of Dynamic Air. Get the sensation.</p>
                <button class="font-bold px-4 py-2 text-white bg-black rounded-full">Shop</button>
=======
<div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
    <div class="row">
        @foreach($products as $product)
        @php
        // Lấy giá của sản phẩm chính
        $displayPrice = $product->price;
        
        // Lấy ảnh đầu tiên của sản phẩm chính (nếu có), nếu không có thì dùng ảnh mặc định
        $productImage = $product->images->where('type', 'main')->first();
        $imageUrl = $productImage ? asset($productImage->image_url) : asset('storage/images/default.jpg'); // Sử dụng đúng phần image_url từ database
        @endphp
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <!-- Sử dụng biến $imageUrl -->
                <img src="{{ $imageUrl }}" class="card-img-top">
        
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">Giá: {{ number_format($displayPrice, 0, ',', '.') }} VND</p>
                    <a href="{{ route('products.detail', ['id' => $product->product_id]) }}" class="btn btn-primary">Xem chi tiết</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    

    <div class="text-center mt-4">
        <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_1704,c_limit/e0b60c2f-d245-42e9-86ca-f7ea95ba6d45/nike-just-do-it.jpg" alt="">
        <p class="mb-0 font-semibold">Just In</p>
        <span class="uppercase text-4xl font-bold">Air Max Dn8</span>
        <p class="font-semibold">Introducing the next chapter of Dynamic Air. Get the sensation.</p>
        <button class="font-bold px-4 py-2 text-white bg-black rounded-full">Shop</button>
    </div>

    <div class="mx-auto max-w-screen-lg px-8 py-4 sm:px-6 lg:px-8">
        <p class="text-2xl font-medium text-center py-2">Shop By Gender</p>
        <div class="flex items-center justify-center gap-2">
            <div>
                <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/4ee9f942-8d0a-4a4d-a317-a3ed96d33d57/image.png" alt="">
                <button class="mt-4 font-semibold px-3 py-2 text-white bg-black rounded-full">Shop Men's</button>
            </div>
            <div>
                <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/d44d84ab-e2bd-48ae-a175-8fe8de522954/nike-just-do-it.jpg" alt="">
                <button class="mt-4 font-semibold px-3 py-2 text-white bg-black rounded-full">Shop Women's</button>
            </div>
            <div>
                <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/e4227a79-252b-4a46-b7c2-ef7e8b85d5d7/image.png" alt="">
                <button class="mt-4 font-semibold px-3 py-2 text-white bg-black rounded-full">Shop Kids'</button>
            </div>
        </div>
        {{-- <h1 class="my-4">Danh sách sản phẩm</h1> --}}
      
        <div class="container">
            <p class="text-center text-2xl font-bold mb-4">The Latest & Greatest</p>
        
          <div class="grid grid-cols-6 gap-2">
            @foreach($products as $product)
            @php
                // Lấy biến thể đầu tiên nếu có
                $variant = $product->variants->first();
                
                // Lấy giá khuyến mãi, nếu không có thì lấy giá gốc
                $displayPrice = $variant && $variant->price_sale ? $variant->price_sale : ($variant ? $variant->price : $product->price);
                $originalPrice = $variant ? $variant->price : $product->price;
        
                // Lấy ảnh của biến thể nếu có, nếu không thì lấy ảnh sản phẩm
                $image = $variant && $variant->images->isNotEmpty() ? 
                    $variant->images->first()->image_url : 
                    ($product->images->isNotEmpty() ? $product->images->first()->image_url : 'default.jpg');
            
            @endphp
      

            <div class="col-md-4 mb-4">
                <div class="card">

                    <img src="{{ asset('storage/' . $image) }}" class="card-img-top">

                    <div class="space-y-2">
                        <p class="font-semibold">{{ $product->name }}</p>
                        <p class="text-gray-300">Product category</p>
        
                        @if ($variant && $variant->price_sale && $variant->price_sale < $variant->price)
                            <p class=" font-semibold">
                                {{ number_format($displayPrice, 0, ',', '.') }} đ
                                {{-- <span class="text-gray-500 line-through ml-2">
                                    {{ number_format($originalPrice, 0, ',', '.') }} đ
                                </span> --}}
                            </p>
                        @else
                            <p class="font-semibold">{{ number_format($displayPrice, 0, ',', '.') }} đ</p>
                        @endif
                        
                        {{-- <a href="{{ route('products.detail', ['id' => $product->product_id]) }}" class="btn btn-primary">Xem chi tiết</a> --}}
                    </div>
                </div>
            </div>
        @endforeach
        
          </div>
        </div>
        
        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    </div>
  
           
       
        
        </div>
    </div>
@endsection

    </div>

    <div class="container">
        <p class="text-center text-2xl font-bold mb-4">The Latest & Greatest</p>

        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @foreach ($products->take(10) as $product)
                    @php
                        // Lấy ảnh của sản phẩm chính cho slider
                        $image = $product->images->isNotEmpty() 
                            ? $product->images->first()->image_url 
                            : 'default.jpg';
                    @endphp
                    <div class="swiper-slide">
                        <div class="card">
                            <img src="{{ asset('' . $image) }}" class="card-img-top">

                            <div class="card-body">
                                <h5 class="text-black text-sm">{{ $product->name }}</h5>
                                <p class="text-gray-400 text-sm">Category</p>
                                <h5 class="text-black text-sm">{{ number_format($product->price, 0, ',', '.') }} đ</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <!-- Nút điều hướng -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>

@endsection
