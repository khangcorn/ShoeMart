@extends('client.layout')

@section('content')
    <div class="container mx-auto px-4 max-w-screen-xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4 sticky top-0 bg-white z-10">
            <p class="font-semibold text-2xl">Shoes ({{ count($products) }})</p>
            <p id="toggleFilter" class="font-semibold cursor-pointer flex items-center gap-1">
                Hide Filters
                <svg aria-hidden="true" class="icon-filter-ds" focusable="false" viewBox="0 0 24 24" role="img"
                    width="24px" height="24px" fill="none">
                    <path stroke="currentColor" stroke-width="1.5" d="M21 8.25H10m-5.25 0H3"></path>
                    <path stroke="currentColor" stroke-width="1.5" d="M7.5 6v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"
                        clip-rule="evenodd"></path>
                    <path stroke="currentColor" stroke-width="1.5" d="M3 15.75h10.75m5 0H21"></path>
                    <path stroke="currentColor" stroke-width="1.5" d="M16.5 13.5v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"
                        clip-rule="evenodd"></path>
                </svg>
            </p>
        </div>

        <!-- Layout chính -->
        <div class="flex transition-all duration-500 ease-in-out">
            <!-- Cột Category (20%) -->
            <div id="categoryColumn" class="w-1/5 bg-white transition-all duration-500 ease-in-out">
                <ul class="space-y-2">
                    <li><a href="#" class="text-black font-semibold text-md">Lifestyle</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Jordan</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Running</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Basketball</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Football</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Training</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Skateboarding</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Golf</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Tennis</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Athletics</a></li>
                    <li><a href="#" class="text-black font-semibold text-md">Walking</a></li>
                </ul>

            </div>

            <!-- Cột Sản Phẩm (80%) -->
            <div id="productColumn" class="w-4/5 transition-all duration-500 ease-in-out">
                <div class="grid grid-cols-3 gap-3">
                    @foreach ($products as $product)
                        <div class="bg-white">
                            @if ($product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                    class="w-[325px] h-[325px] object-cover "
                                    alt="{{ $product->name }}">
                            @else
                                <img src="{{ asset('storage/default.jpg') }}"
                                    class="w-[325px] h-[325px] object-cover border-[1px] border-gray-300" alt="No Image">
                            @endif

                            <p class="font-semibold text-orange-600 mt-2">Category cha</p>
                            <p {{ route('products.detail', $product->product_id) }} class="font-semibold">
                                {{ $product->name }}</p>
                            <p class="font-semibold text-gray-500">
                                {{ $product->category->parent ? $product->category->parent->name : $product->category->name }}
                            </p>
                            <p class="text-black font-semibold"> {{ number_format($product->price, 0, ',', '.') }} đ</p>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <style>
        #productColumn img {
            transition: width 0.5s ease-in-out, height 0.5s ease-in-out;
        }
    </style>
    <script>
        document.getElementById("toggleFilter").addEventListener("click", function() {
            let categoryColumn = document.getElementById("categoryColumn");
            let productColumn = document.getElementById("productColumn");
            let images = document.querySelectorAll("#productColumn img");

            if (categoryColumn.classList.contains("hidden")) {
                categoryColumn.classList.remove("hidden");
                setTimeout(() => {
                    categoryColumn.style.width = "20%";
                    productColumn.style.width = "80%";
                    images.forEach(img => img.style.width = img.style.height = "325px");
                }, 10);
                this.innerHTML =
                    'Hide Filters <svg aria-hidden="true" class="icon-filter-ds" focusable="false" viewBox="0 0 24 24" role="img" width="24px" height="24px" fill="none"><path stroke="currentColor" stroke-width="1.5" d="M21 8.25H10m-5.25 0H3"></path><path stroke="currentColor" stroke-width="1.5" d="M7.5 6v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" clip-rule="evenodd"></path><path stroke="currentColor" stroke-width="1.5" d="M3 15.75h10.75m5 0H21"></path><path stroke="currentColor" stroke-width="1.5" d="M16.5 13.5v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" clip-rule="evenodd"></path></svg>';
            } else {
                categoryColumn.style.width = "0";
                productColumn.style.width = "100%";
                images.forEach(img => img.style.width = img.style.height = "400px");
                setTimeout(() => {
                    categoryColumn.classList.add("hidden");
                }, 300);
                this.innerHTML =
                    'Show Filters <svg aria-hidden="true" class="icon-filter-ds" focusable="false" viewBox="0 0 24 24" role="img" width="24px" height="24px" fill="none"><path stroke="currentColor" stroke-width="1.5" d="M21 8.25H10m-5.25 0H3"></path><path stroke="currentColor" stroke-width="1.5" d="M7.5 6v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" clip-rule="evenodd"></path><path stroke="currentColor" stroke-width="1.5" d="M3 15.75h10.75m5 0H21"></path><path stroke="currentColor" stroke-width="1.5" d="M16.5 13.5v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" clip-rule="evenodd"></path></svg>';
            }
        });
    </script>
@endsection
