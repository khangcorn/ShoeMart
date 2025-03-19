@extends('client.layout')

@section('title', 'Trang chủ')

@section('content')

    <div class="bg-white">
        <div class="">
            <img class="relative" src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_1704,c_limit/e0b60c2f-d245-42e9-86ca-f7ea95ba6d45/nike-just-do-it.jpg"
                alt="">
            <div class="text-center  absolute top-2/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                <p class="mb-0 font-semibold text-white" style="font: 500 1rem / 1.5 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">
                    Just In
                </p>
                
                <span class="uppercase text-4xl font-bold text-white" style="font: 800 2.5rem / 0.9 'Nike Futura ND', 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">
                    Air Max Dn8
                </span>
                
                <p class="font-semibold py-2 text-white" style="font: 500 1rem / 1.5 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;"> Introducing the next chapter of Dynamic Air. Get the sensation.</p>
                <button class="font-bold px-4 py-2 text-black bg-white rounded-full" style="font: 500 1rem / 1 'Nike Futura ND', 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">Shop</button>

                <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">


                  




                </div>
            </div>


        </div>
        <div class="mx-auto max-w-screen-lg px-8 py-4 sm:px-6 lg:px-8">

            <div class="flex justify-between  gap-2">
                <div>
                    <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/4ee9f942-8d0a-4a4d-a317-a3ed96d33d57/image.png"
                        alt="">
                    <button class="mt-4 font-semibold px-3 py-2 text-white bg-black rounded-full ">Shop
                        Men's</button>
                </div>
                <div>
                    <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/d44d84ab-e2bd-48ae-a175-8fe8de522954/nike-just-do-it.jpg"
                        alt="">
                    <button class="mt-4 font-semibold px-3 py-2 text-white bg-black rounded-full">Shop
                        Women's</button>
                </div>
                <div>
                    <img src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/e4227a79-252b-4a46-b7c2-ef7e8b85d5d7/image.png"
                        alt="">
                    <button class="mt-4 font-semibold px-3 py-2 text-white bg-black rounded-full">Shop
                        Kids'</button>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
        <div class=" mx-auto max-w-screen-xl  px-4 sm:px-6 lg:px-8">
            <div class="  ">
                <!-- Phân trang -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
                <div class="">
                    <div class="flex items-center justify-between">
                        <p class="text-2xl font-medium text-center py-2 mb-0">Trending Now</p>
                        <div class="flex items-center gap-2">
                            <button id="prevSlide"
                                class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                                    height="24px" class="rotate-180" fill="none">
                                    <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033">
                                    </path>
                                </svg>
                            </button>
                            <button id="nextSlide"
                                class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                                    height="24px" fill="none">
                                    <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033">
                                    </path>
                                </svg>
                            </button>

                        </div>
                    </div>

                    <!-- Swiper container -->
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            @foreach ($products as $product)
                                @php
                                    $displayPrice = $product->price;
                                    $productImage = $product->images->where('type', 'main')->first();
                                    $imageUrl = $productImage
                                        ? asset($productImage->image_url)
                                        : asset('storage/images/default.jpg');
                                @endphp

                                <div class="swiper-slide bg-white rounded-lg p-2">
                                    <img src="{{ $imageUrl }}" class="w-[410px] h-[410px] object-cover">

                                    <div class="mt-2">
                                        <p class="font-semibold truncate">{{ $product->name }}</p>
                                        <p class="text-gray-400 text-sm">{{ $product->category->name }}</p>
                                        <p class="font-semibold"> {{ number_format($displayPrice, 0, '.', ',') }} <span
                                                class="font-normal underline">đ</span></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var nextBtn = document.getElementById("nextSlide");
                        var prevBtn = document.getElementById("prevSlide");

                        var swiper = new Swiper(".mySwiper", {
                            slidesPerView: 3,
                            spaceBetween: 10,
                            navigation: {
                                nextEl: "#nextSlide",
                                prevEl: "#prevSlide",
                            },
                            breakpoints: {
                                640: {
                                    slidesPerView: 1
                                },
                                1024: {
                                    slidesPerView: 2
                                },
                                1280: {
                                    slidesPerView: 3
                                }
                            },
                            on: {
                                init: function() {
                                    checkNavButtons();
                                },
                                slideChange: function() {
                                    checkNavButtons();
                                }
                            }
                        });

                        function checkNavButtons() {
                            if (swiper.isBeginning) {
                                prevBtn.style.opacity = "0.6";
                            } else {
                                prevBtn.style.opacity = "1";
                            }

                            if (swiper.isEnd) {
                                nextBtn.style.opacity = "0.6";
                            } else {
                                nextBtn.style.opacity = "1";
                            }
                        }
                    });
                </script>


            </div>
        </div>
    </div>

@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
