@extends('client.layout')

@section('title', 'Shoemart. Just Do It. Shoemart VN')

@section('content')
    <style>
        .swiper-wrapper {
            max-height: 495px;
        }

        .mySwiper3 {
            height: auto !important;
        }

        .mySwiper3 .swiper-wrapper {
            height: auto !important;
        }

        .mySwiper3 .swiper-slide {
            height: auto !important;
        }
    </style>
    <div class="bg-white">
        <div class="">
            <img class="relative"
                src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_1704,c_limit/e0b60c2f-d245-42e9-86ca-f7ea95ba6d45/nike-just-do-it.jpg"
                alt="">
            <div class="text-center  absolute top-2/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                <p class=" font-semibold text-white mb-3"
                    style="font: 500 1rem / 1.5 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">
                    Just In
                </p>

                <span class="uppercase text-4xl font-bold text-white"
                    style="font: 800 4.5rem / 0.9 'Nike Futura ND', 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">
                    Air Max Dn8
                </span>

                <p class="font-semibold  text-white mt-3"
                    style="font: 500 1rem / 1.5 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;"> Introducing the
                    next chapter of Dynamic Air. Get the sensation.</p>
                <button class="font-bold px-4 py-2 mt-3 text-black bg-white rounded-full"
                    style="font: 700 1rem / 1 'Nike Futura ND', 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">Shop</button>

                <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">







                </div>
            </div>


        </div>
        <div class="mx-auto max-w-screen-xl px-10 py-4 sm:px-6 lg:px-8">


            <div class="flex justify-between items-center py-2 mt-12">
                <div>
                    <p class="text-2xl font-medium text-center  mb-0">Find Your Max</p>
                </div>
                <div class="flex items-center gap-2">
                    <button id="prevSlide2"
                        class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                            height="24px" class="rotate-180" fill="none">
                            <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033"></path>
                        </svg>
                    </button>
                    <button id="nextSlide2"
                        class=" w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                            height="24px" fill="none">
                            <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033"></path>
                        </svg>
                    </button>

                </div>
            </div>

            <!-- Thêm class 'swiper' để SwiperJS nhận diện -->
            <div class="swiper mySwiper2 ">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img class="object-cover w-auto h-auto"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/cf9b1d53-1fb5-4355-b2b8-ca0437c053e4/nike-just-do-it.png"
                            alt="">
                        <p class="text-xl   py-4 mb-0">Air Max Dn8</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="object-cover w-auto h-auto"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/28bb01bb-991e-4fc3-92e2-85f4916e29b8/nike-just-do-it.jpg"
                            alt="">
                        <p class="text-xl   py-4 mb-0">Air Max Dn</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="object-cover w-auto h-auto"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/b3b6a3ff-0cb8-417d-82f4-b40926b08b4c/image.jpg"
                            alt="">
                        <p class="text-xl   py-4 mb-0"> Air Max LV8</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="object-cover w-auto h-auto"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/9f24d90f-6f2a-416b-8ed1-e56d6d0a72d4/image.jpg"
                            alt="">
                        <p class="text-xl   py-4 mb-0"> Air Max 90</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="object-cover w-auto h-auto"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_470,c_limit/d3302631-9f44-435d-9b5b-10d7ea8604da/image.jpg"
                            alt="">
                        <p class="text-xl   py-4 mb-0"> Air Max Plus</p>
                    </div>

                </div>
            </div>
        </div>



        <div class=" mx-auto max-w-screen-xl  px-4 sm:px-6 lg:px-8">
            <div class="  ">
                <!-- Phân trang -->
                <div class="d-flex justify-content-center ">
                    {{ $products->links() }}
                </div>
                <div class="">
                    <div class="flex items-center justify-between">
                        <p class="text-2xl font-medium text-center  mb-0">Trending Now</p>
                        <div class="flex items-center gap-2">
                            <button id="prevSlide"
                                class=" w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                                    height="24px" class="rotate-180" fill="none">
                                    <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033">
                                    </path>
                                </svg>
                            </button>
                            <button id="nextSlide"
                                class=" w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
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
                                    <a href="{{ url('/products/' . $product->id) }}">
                                        <img src="{{ $imageUrl }}"
                                            class="w-[390px] h-[390px] object-cover cursor-pointer">
                                    </a>


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

                    document.addEventListener("DOMContentLoaded", function() {
                        var nextBtn2 = document.getElementById("nextSlide2");
                        var prevBtn2 = document.getElementById("prevSlide2");

                        var swiper2 = new Swiper(".mySwiper2", {
                            slidesPerView: 3,
                            spaceBetween: 10,
                            navigation: {
                                nextEl: "#nextSlide2",
                                prevEl: "#prevSlide2",
                            },
                            breakpoints: {
                                640: {
                                    slidesPerView: 1
                                },
                                1024: {
                                    slidesPerView: 2
                                },
                                1280: {
                                    slidesPerView: 4
                                }
                            },
                            on: {
                                init: function() {
                                    checkNavButtons2();
                                },
                                slideChange: function() {
                                    checkNavButtons2();
                                }
                            }
                        });

                        function checkNavButtons2() {
                            prevBtn2.disabled = swiper2.isBeginning;
                            nextBtn2.disabled = swiper2.isEnd;
                        }
                    });
                </script>


            </div>
        </div>
        <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 mt-12">
            <p class="text-2xl font-medium py-4  mb-0">Don't Miss</p>
            <div>
                <img src="https://static.nike.com/a/images/f_auto/dpr_2.0,cs_srgb/w_1781,c_limit/d53c3c65-c452-4ab3-a979-654753f739c2/nike-just-do-it.png"
                    alt="">
                <div class="justify-center text-center py-10">
                    <p class=" font-semibold text-black mb-3"
                        style="font: 500 1rem / 1.5 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">Air Jordan
                        4 ‘Abundance’</p>

                    <span class="uppercase text-4xl font-bold text-black"
                        style="font: 800 2.5rem / 0.9 'Nike Futura ND', 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">
                        IN HER BAG
                    </span>

                    <p class="font-semibold  text-black mt-3"
                        style="font: 500 1rem / 1.5 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">
                        Always earned, never given. Abundance is an homage to her hustle.</p>
                    <button class="font-bold px-4 py-2 mt-3 text-white bg-black rounded-full"
                        style="font: 500 1rem / 1 'Nike Futura ND', 'Helvetica Now Text Medium', Helvetica, Arial, sans-serif;">Shop</button>

                </div>

            </div>
        </div>
        <div class="mx-auto flex justify-between items-center max-w-screen-xl px-4 sm:px-6 lg:px-8 mt-10">
            <p class="text-2xl font-medium py-2 mb-0">Shop By Sport</p>
            <div class="flex  items-center py-2 gap-2">
                <button id="prevSlide3"
                    class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                        height="24px" class="rotate-180" fill="none">
                        <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033"></path>
                    </svg>
                </button>
                <button id="nextSlide3"
                    class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                        height="24px" fill="none">
                        <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033"></path>
                    </svg>
                </button>
            </div>

        </div>
        <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 ">
            <div class="swiper mySwiper3">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/a3c971bc-bc0a-4c0c-8bdf-e807a3027e53/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Running</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/e4695209-3f23-4a05-a9f9-d0edde31b653/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Football</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/38ed4b8e-9cfc-4e66-9ddd-02a52314eed9/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Basketball</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/e36a4a2b-4d3f-4d1c-bc75-d6057b7cec87/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Training and Gym</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/7ce96f81-bf80-45b9-918e-f2534f14015d/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Tennis</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/6be55ac6-0243-42d6-87d0-a650074c658c/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Yoga</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/608705dc-dea5-4450-b68f-e624cf1ed2a7/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class=" cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Skateboarding</p>
                    </div>
                    <div class="swiper-slide">
                        <img class="cursor-pointer object-cover w-auto h-auto relative"
                            src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/w_619,c_limit/c779e4f6-7d91-46c3-9282-39155e0819e5/nike-just-do-it.jpg"
                            alt="">
                        <p
                            class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full absolute bottom-4 left-4">
                            Dance</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 mt-10">
            <!-- Header -->
            <div class="flex justify-between items-center py-2">
                <p class="text-2xl py-2 font-medium">Member Benefits</p>
                <div class="flex items-center gap-2">
                    <button id="prevSlide4"
                        class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                            height="24px" class="rotate-180" fill="none">
                            <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033"></path>
                        </svg>
                    </button>
                    <button id="nextSlide4"
                        class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center transition duration-75 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                            height="24px" fill="none">
                            <path stroke="currentColor" stroke-width="1.5" d="M8.474 18.966L15.44 12 8.474 5.033"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Swiper Slider -->
            <div class="swiper mySwiper4">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide ">
                        <img class="relative" src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_773,c_limit/cb28c551-b85b-479f-8fc3-40ad4e7c9ca4/nike-just-do-it.jpg"
                            alt="">
                            <div class="absolute bottom-4 left-4 ">
                                <p class="text-white text-sm font-semibold">Member Product</p>
                                <p class="text-white text-xl font-semibold mt-0 mb-2">Your Exclusive Access</p>
                                <button class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full ">Shop</button>
                            </div>
                          
                    </div>
                    <!-- Slide 2 -->
                    <div class="swiper-slide ">
                        <img class="relative" src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_773,c_limit/100ca749-1a94-4f98-bc43-a58e7e9cdbcf/nike-just-do-it.png"
                            alt="">
                            <div class="absolute bottom-4 left-4 ">
                                <p class="text-white text-sm font-semibold">Nike By You</p>
                                <p class="text-white text-xl font-semibold mt-0 mb-2">Your Customisation Service</p>
                                <button class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full ">Customise</button>
                            </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="swiper-slide ">
                        <img class="relative" src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_773,c_limit/39412611-0af5-4770-8c2e-ef5c23bc6a3d/nike-just-do-it.jpg"
                            alt="">
                            <div class="absolute bottom-4 left-4 ">
                                <p class="text-white text-sm font-semibold">
                                    Member Rewards

                                    </p>
                                <p class="text-white text-xl font-semibold mt-0 mb-2">How We Say Thank You</p>
                                <button class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full ">                                    
                                    
                                    Celebrate</button>
                            </div>
                    </div>
                    <!-- Slide 4 -->
                    <div class="swiper-slide ">
                        <img class="relative" src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_773,c_limit/a9767bce-db10-41ff-9eb5-f5daf8bbb3e6/nike-just-do-it.png"
                            alt="">
                            <div class="absolute bottom-4 left-4 ">
                                <p class="text-white text-sm font-semibold">Member Product</p>
                                <p class="text-white text-xl font-semibold mt-0 mb-2">Your Exclusive Access</p>
                                <button class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full ">Shop</button>
                            </div>
                    </div>
                    <div class="swiper-slide ">
                        <img class="relative" src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_773,c_limit/37b262a3-c8c7-49e8-a29f-8d46bc8ab950/nike-just-do-it.jpg"
                            alt="">
                            <div class="absolute bottom-4 left-4 ">
                                <p class="text-white text-sm font-semibold">Member Product</p>
                                <p class="text-white text-xl font-semibold mt-0 mb-2">Your Exclusive Access</p>
                                <button class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full ">Shop</button>
                            </div>
                    </div>
                    <div class="swiper-slide ">
                        <img class="relative" src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_773,c_limit/c17ae904-9307-4575-8ac1-ad08adafe17f/nike-just-do-it.jpg"
                            alt="">
                            <div class="absolute bottom-4 left-4 ">
                                <p class="text-white text-sm font-semibold">Member Product</p>
                                <p class="text-white text-xl font-semibold mt-0 mb-2">Your Exclusive Access</p>
                                <button class="cursor-pointer text-lg mb-0 px-5 py-1 font-semibold bg-white rounded-full ">Shop</button>
                            </div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const slider = document.querySelector(".swiper.mySwiper4");
                const prevButton = document.getElementById("prevSlide4");
                const nextButton = document.getElementById("nextSlide4");

                if (slider) {
                    const swiper4 = new Swiper(".mySwiper4", {
                        slidesPerView: 3,
                        spaceBetween: 10,
                        navigation: {
                            nextEl: "#nextSlide4",
                            prevEl: "#prevSlide4",
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 1
                            },
                            768: {
                                slidesPerView: 2
                            },
                            1024: {
                                slidesPerView: 3
                            },
                        },
                    });

                    // Disable buttons if needed
                    function updateButtonState() {
                        prevButton.disabled = swiper4.isBeginning;
                        nextButton.disabled = swiper4.isEnd;
                    }

                    swiper4.on("slideChange", updateButtonState);
                    updateButtonState();
                }
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var nextBtn3 = document.getElementById("nextSlide3");
                var prevBtn3 = document.getElementById("prevSlide3");

                var swiper3 = new Swiper(".mySwiper3", {
                    slidesPerView: 3,
                    spaceBetween: 10,
                    navigation: {
                        nextEl: "#nextSlide3",
                        prevEl: "#prevSlide3",
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
                            checkNavButtons3();
                        },
                        slideChange: function() {
                            checkNavButtons3();
                        }
                    }
                });

                function checkNavButtons3() {
                    prevBtn3.disabled = swiper3.isBeginning;
                    nextBtn3.disabled = swiper3.isEnd;
                }
            });
        </script>
 <div class="mx-auto max-w-screen-xl flex justify-center px-4 bg-white sm:px-6 lg:px-8 mt-10 ">
    <div class="grid grid-cols-4 py-4 space-x-4 gap-4">
<div class="space-y-2 ">
    <p class="text-lg text-black font-semibold mb-4">Icons</p>
    <p class="font-semibold text-gray-500">Air Force 1</p>
    <p class="font-semibold text-gray-500">Huarache</p>
    <p class="font-semibold text-gray-500"> Air Max 90</p>
    <p class="font-semibold text-gray-500">Air Max 95</p>
</div>
<div class="space-y-2">
    <p class="text-lg text-black font-semibold mb-4">Shoes
        
       
        
        </p>
    <p class="font-semibold text-gray-500">All Shoes</p>
    <p class="font-semibold text-gray-500"> Custom Shoes</p>
    <p class="font-semibold text-gray-500"> Jordan Shoes</p>
    <p class="font-semibold text-gray-500">Running Shoes</p>
</div>
<div class="space-y-2">
    <p class="text-lg text-black font-semibold mb-4">Clothing</p>
    <p class="font-semibold text-gray-500">     All Clothing</p>
    <p class="font-semibold text-gray-500"> Modest Wear</p>
    <p class="font-semibold text-gray-500">  Hoodies & Pullovers</p>
    <p class="font-semibold text-gray-500">Shirts & Tops</p>
</div>
<div class="space-y-2">
    <p class="text-lg text-black font-semibold mb-4">Kids'</p>
    <p class="font-semibold text-gray-500"> Infant & Toddler Shoes</p>
    <p class="font-semibold text-gray-500">Kids' Shoes</p>
    <p class="font-semibold text-gray-500">  Kids' Jordan Shoes</p>
    <p class="font-semibold text-gray-500"> Kids' Basketball Shoes</p>
</div>

    </div>

</div>
    </div>

@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
