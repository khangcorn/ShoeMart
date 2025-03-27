@extends('client.layout')

@section('content')
    <div
        class="h-14 mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 flex items-center justify-between w-full sticky top-0 bg-white z-10">
        <p class="font-semibold text-xl">Shoes ({{ count($products) }})</p>
        <p id="toggleFilter" class="font-semibold cursor-pointer flex items-center gap-1">
            Hide Filters
            <svg aria-hidden="true" class="icon-filter-ds" focusable="false" viewBox="0 0 24 24" role="img" width="24px"
                height="24px" fill="none">
                <path stroke="currentColor" stroke-width="1.5" d="M21 8.25H10m-5.25 0H3"></path>
                <path stroke="currentColor" stroke-width="1.5" d="M7.5 6v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"
                    clip-rule="evenodd"></path>
                <path stroke="currentColor" stroke-width="1.5" d="M3 15.75h10.75m5 0H21"></path>
                <path stroke="currentColor" stroke-width="1.5" d="M16.5 13.5v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"
                    clip-rule="evenodd"></path>
            </svg>
        </p>
    </div>
    <div class="container mx-auto px-4 max-w-screen-xl sm:px-6 lg:px-8">


        <!-- Layout chính -->
        <div class="flex transition-all duration-500 ease-in-out space-x-4 ">
            <!-- Cột Category (20%) -->
            <div id="categoryColumn" class="w-1/5 bg-white transition-all duration-500 ease-in-out">
                <ul class="space-y-3" id="categoryList">
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="lifestyle">Lifestyle</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="jordan">Jordan</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="running">Running</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="basketball">Basketball</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="football">Football</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="training-gym">Training </a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="skateboarding">Skateboarding</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md" data-category="golf">Golf</a>
                    </li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md" data-category="yoga">Yoga</a>
                    </li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="tennis">Tennis</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="athletics">Athletics</a></li>
                    <li><a href="#" class="category-filter text-black font-semibold text-md"
                            data-category="walking">Walking</a></li>
                </ul>



                <!-- Dropdown Gender -->
                <div class="mt-4">
                    <button id="genderToggle"
                        class="w-full text-left font-semibold text-md flex justify-between items-center py-2  border-t border-gray-200">
                        Gender
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <g clip-path="url(#clip0_429_11251)">
                                    <path d="M7 10L12 15" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M12 15L17 10" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_429_11251">
                                        <rect width="24" height="24" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </g>
                        </svg>
                    </button>
                    <div id="genderDropdown" class="hidden rounded-md space-y-2 mb-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="gender[]" value="Men" class="form-checkbox">
                            <span class="font-semibold">Men</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="gender[]" value="Women" class="form-checkbox">
                            <span class="font-semibold">Women</span>
                        </label>
                      
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const genderCheckboxes = document.querySelectorAll("input[name='gender[]']");
                        const products = document.querySelectorAll(".product-item");

                        genderCheckboxes.forEach(checkbox => {
                            checkbox.addEventListener("change", function () {
                                // Bỏ chọn tất cả checkbox khác
                                genderCheckboxes.forEach(cb => {
                                    if (cb !== this) cb.checked = false;
                                });

                                // Lấy giá trị được chọn
                                let selectedGender = this.checked ? this.value : null;

                                // Lọc sản phẩm
                                products.forEach(product => {
                                    let parentCategory = product.getAttribute("data-category-parent");

                                    if (!selectedGender ||
                                        (selectedGender === "Men" && parentCategory ===
                                            "Men's Shoes") ||
                                        (selectedGender === "Women" && parentCategory ===
                                            "Women's Shoes") ||
                                        (selectedGender === "Unisex" && parentCategory ===
                                            "Unisex Shoes")) {
                                        product.style.display = "block";
                                    } else {
                                        product.style.display = "none";
                                    }
                                });
                            });
                        });
                    });
                </script>
                <!-- Dropdown Price Filter -->
                <div class="">
                    <button id="priceToggle"
                        class="w-full text-left font-semibold text-md flex justify-between items-center py-2  border-t border-gray-200">
                        Sort By
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 10L12 15" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M12 15L17 10" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <div id="priceDropdown" class="hidden space-y-2 rounded-md mb-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="sortPriceAsc" class="form-checkbox">
                            <span class="font-semibold">Price: Low-High</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="sortPriceDesc" class="form-checkbox">
                            <span class="font-semibold">Price: High-Low</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="sortNewest" class="form-checkbox">
                            <span class="font-semibold">Newest</span>
                        </label>
                    </div>
                </div>
                <!-- Dropdown Colour Filter -->
                <div class="">
                    <button id="colourToggle"
                        class="w-full text-left font-semibold text-md flex justify-between items-center py-2 border-t border-gray-200">
                        Colours
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 10L12 15" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M12 15L17 10" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <div id="colourDropdown" class="hidden space-y-2 rounded-md mb-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="colorBlack" class="form-checkbox">
                            <span class="font-semibold">Black</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="colorWhite" class="form-checkbox">
                            <span class="font-semibold">White</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="colorRed" class="form-checkbox">
                            <span class="font-semibold">Red</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="colorPink" class="form-checkbox">
                            <span class="font-semibold">Pink</span>
                        </label>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        // Lấy các phần tử
                        const colourToggle = document.getElementById("colourToggle");
                        const colourDropdown = document.getElementById("colourDropdown");
                        const colorCheckboxes = document.querySelectorAll("#colourDropdown input[type='checkbox']");

                        // Toggle hiển thị dropdown
                        colourToggle.addEventListener("click", function () {
                            colourDropdown.classList.toggle("hidden");
                        });

                        // Chỉ cho phép chọn một màu duy nhất
                        colorCheckboxes.forEach(checkbox => {
                            checkbox.addEventListener("change", function () {
                                if (this.checked) {
                                    colorCheckboxes.forEach(cb => {
                                        if (cb !== this) cb.checked = false;
                                    });
                                }
                            });
                        });
                    });
                </script>
                <!-- Dropdown Size Filter -->
                <div class="">
                    <button id="sizeToggle"
                        class="w-full text-left font-semibold text-md flex justify-between items-center py-2 border-t border-gray-200">
                        Size
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 10L12 15" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M12 15L17 10" stroke="#292929" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <div id="sizeDropdown" class="hidden space-y-2 rounded-md mb-2">
                        <div class="grid grid-cols-4 gap-2 p-0.5">
                            <button type="button" data-value="35"
                                class="size-btn bg-white text-gray-700 font-semibold px-1.5 py-1 border border-gray-200 rounded-md transition">
                                35
                            </button>
                            <button type="button" data-value="35.5"
                                class="size-btn bg-white text-gray-700 font-semibold px-1.5 py-1 border border-gray-200 rounded-md transition">
                                35.5
                            </button>
                            <button type="button" data-value="36"
                                class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                                36
                            </button>
                            <button type="button" data-value="36.5"
                                class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                                36.5
                            </button>
                            <button type="button" data-value="37"
                                class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                                37
                            </button>
                            <button type="button" data-value="37.5"
                            class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                            37.5
                        </button>
                            <button type="button" data-value="38"
                                class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                                38
                            </button>
                            <button type="button" data-value="38.5"
                            class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                            38.5
                        </button>
                            <button type="button" data-value="39"
                                class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                                39
                            </button>
                            <button type="button" data-value="40"
                                class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                                40
                            </button>
                            <button type="button" data-value="40.5"
                            class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                            40.5
                        </button>
                            <button type="button" data-value="41"
                                class="size-btn bg-white text-gray-700 font-semibold px-2 py-1 border border-gray-200 rounded-md transition">
                                41
                            </button>
                        </div>
                    </div>
                </div>
                
                <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const sizeToggle = document.getElementById("sizeToggle");
                    const sizeDropdown = document.getElementById("sizeDropdown");
                    const sizeButtons = document.querySelectorAll(".size-btn");
                
                    // Toggle dropdown
                    sizeToggle.addEventListener("click", function () {
                        sizeDropdown.classList.toggle("hidden");
                    });
                
                    // Toggle button active state
                    sizeButtons.forEach(button => {
                        button.addEventListener("click", function () {
                            this.classList.toggle("ring-2");
                            this.classList.toggle("ring-black");
                            this.classList.toggle("bg-white");
                            this.classList.toggle("text-black");
                        });
                    });
                });
                </script>
                





            </div>

            <script>
                document.getElementById('genderToggle').addEventListener('click', function () {
                    let dropdown = document.getElementById('genderDropdown');
                    let arrow = document.getElementById('arrow');
                    dropdown.classList.toggle('hidden');
                    arrow.classList.toggle('rotate-180');
                });
            </script>


            <!-- Cột Sản Phẩm (80%) -->
            <div id="productColumn" class="w-4/5 transition-all duration-500 ease-in-out">
                <div class="grid grid-cols-3 gap-3">
                    {{-- <div class="relative">
                        <img src="https://static.nike.com/a/images/w_960,c_limit/72a4154a-74dd-4e18-b3ee-c076135dd54f/image.jpg" alt="">
                        <div class="absolute bottom-28 left-14 transform -translate-x-1/2 -translate-y-1/2">
                            <button class="rounded-full px-5 py-1.5 bg-white text-black font-semibold shadow-md hover:bg-gray-200 transition">
                                Shop
                            </button>
                        </div>
                    </div> --}}
                    
                    @foreach ($products as $product)
                        <div class="bg-white product-item"
                            data-category-parent="{{ $product->category->parent ? $product->category->parent->name : '' }}"
                            data-category="{{ Str::slug($product->category->name) }}"
                            data-date="{{ $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s') }}">

                            <a href="{{ route('products.detail', $product->product_id) }}">
                                @if ($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                        class="w-[312px] h-[312px] object-cover" alt="{{ $product->name }}">
                                @else
                                    <img src="{{ asset('storage/default.jpg') }}"
                                        class="w-[312px] h-[312px] object-cover border-[1px] border-gray-300" alt="No Image">
                                @endif
                            </a>

                            <p class="font-semibold text-orange-600 mt-2">
                                {{ $product->category->parent ? $product->category->parent->name : $product->category->name }}
                            </p>
                            <a href="{{ route('products.detail', $product->product_id) }}">
                                <p class="font-semibold">{{ $product->name }}</p>
                            </a>
                            <p class="font-semibold text-gray-500">
                                {{ $product->category->name }}
                            </p>
                            <p class="text-black font-semibold mb-2 relative">
                                {{ number_format($product->price, 0, ',', ',') }}
                                <span class="text-xs underline font-thin absolute top-0.5 -left-19">đ</span>
                            </p>
                            

                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>


    <style>
        .active {
            transition: ease-in-out 0.3s;
            color: #ED580C;
        }

        #productColumn img {
            transition: width 0.5s ease-in-out, height 0.5s ease-in-out;
        }

        #categoryColumn {
            max-height: calc(200vh - 56px);
            /* Chiều cao tối đa (trừ header) */
            overflow-y: auto;
            /* Tạo thanh cuộn dọc */
            scrollbar-width: thin;
            /* Tùy chỉnh thanh cuộn trên Firefox */
            scrollbar-color: #ccc transparent;
            /* Màu thanh cuộn */
        }

        /* Tùy chỉnh thanh cuộn trên Chrome, Edge */
        #categoryColumn::-webkit-scrollbar {
            width: 6px;
        }

        #categoryColumn::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 6px;
        }

        #categoryColumn::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>


    <script>
        document.getElementById("toggleFilter").addEventListener("click", function () {
            let categoryColumn = document.getElementById("categoryColumn");
            let productColumn = document.getElementById("productColumn");
            let images = document.querySelectorAll("#productColumn img");
            let mainContainer = document.querySelector(".flex.transition-all");

            if (categoryColumn.classList.contains("hidden")) {
                categoryColumn.classList.remove("hidden");
                mainContainer.classList.add("space-x-4");
                setTimeout(() => {
                    categoryColumn.style.width = "20%";
                    productColumn.style.width = "80%";
                    images.forEach(img => img.style.width = img.style.height = "325px");
                }, 10);
                this.innerHTML =
                    'Hide Filters <svg aria-hidden="true" class="icon-filter-ds" focusable="false" viewBox="0 0 24 24" role="img"width="24px" height="24px" fill="none"><path stroke="currentColor" stroke-width="1.5" d="M21 8.25H10m-5.25 0H3"></path><path stroke="currentColor" stroke-width="1.5" d="M7.5 6v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"    clip-rule="evenodd"></path><path stroke="currentColor" stroke-width="1.5" d="M3 15.75h10.75m5 0H21"></path><path stroke="currentColor" stroke-width="1.5" d="M16.5 13.5v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"clip-rule="evenodd"></path></svg>';
            } else {
                categoryColumn.style.width = "0";
                productColumn.style.width = "100%";
                mainContainer.classList.remove("space-x-4");
                images.forEach(img => img.style.width = img.style.height = "400px");
                setTimeout(() => {
                    categoryColumn.classList.add("hidden");
                }, 300);
                this.innerHTML =
                    'Show Filters <svg aria-hidden="true" class="icon-filter-ds" focusable="false" viewBox="0 0 24 24" role="img"width="24px" height="24px" fill="none"><path stroke="currentColor" stroke-width="1.5" d="M21 8.25H10m-5.25 0H3"></path><path stroke="currentColor" stroke-width="1.5" d="M7.5 6v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"    clip-rule="evenodd"></path><path stroke="currentColor" stroke-width="1.5" d="M3 15.75h10.75m5 0H21"></path><path stroke="currentColor" stroke-width="1.5" d="M16.5 13.5v0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"clip-rule="evenodd"></path></svg>';
            }
        });
    </script>


    <script>
        // Toggle dropdown visibility
        document.getElementById('priceToggle').addEventListener('click', function () {
            document.getElementById('priceDropdown').classList.toggle('hidden');
        });

        // Lấy tất cả các checkbox
        const sortOptions = document.querySelectorAll('#priceDropdown input[type="checkbox"]');

        // Thêm sự kiện để đảm bảo chỉ chọn một trong ba tùy chọn
        sortOptions.forEach(option => {
            option.addEventListener('change', function () {
                if (this.checked) {
                    // Bỏ chọn tất cả các checkbox khác
                    sortOptions.forEach(otherOption => {
                        if (otherOption !== this) {
                            otherOption.checked = false;
                        }
                    });

                    // Gọi hàm sắp xếp tương ứng
                    if (this.id === 'sortPriceAsc') {
                        sortProductsByPrice(true);
                    } else if (this.id === 'sortPriceDesc') {
                        sortProductsByPrice(false);
                    } else if (this.id === 'sortNewest') {
                        sortProductsByDate();
                    }
                } else {
                    resetProductOrder();
                }
            });
        });

        // Function to sort products by price
        function sortProductsByPrice(ascending) {
            let productContainer = document.querySelector('#productColumn .grid');
            let products = Array.from(productContainer.children);

            products.sort((a, b) => {
                let priceA = parseInt(a.querySelector('.text-black.font-semibold').innerText.replace(/\D/g, ''));
                let priceB = parseInt(b.querySelector('.text-black.font-semibold').innerText.replace(/\D/g, ''));
                return ascending ? priceA - priceB : priceB - priceA;
            });

            renderSortedProducts(productContainer, products);
        }

        // Function to sort products by date (newest first)
        function sortProductsByDate() {
            let productContainer = document.querySelector('#productColumn .grid');
            let products = Array.from(productContainer.children);

            products.sort((a, b) => {
                let dateA = new Date(a.getAttribute('data-date'));
                let dateB = new Date(b.getAttribute('data-date'));
                return dateB - dateA; // Newest first
            });

            renderSortedProducts(productContainer, products);
        }

        // Function to re-render sorted products
        function renderSortedProducts(container, products) {
            container.innerHTML = "";
            products.forEach(product => container.appendChild(product));
        }

        // Function to reset product order
        function resetProductOrder() {
            location.reload();
        }
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const categoryLinks = document.querySelectorAll(".category-filter");
            const products = document.querySelectorAll(".product-item");

            categoryLinks.forEach(link => {
                link.addEventListener("click", function (event) {
                    event.preventDefault();

                    const selectedCategory = this.getAttribute("data-category");

                    // Kiểm tra nếu đã active trước đó thì bỏ chọn
                    if (this.classList.contains("active")) {
                        this.classList.remove("active");
                        products.forEach(product => product.style.display = "block");
                    } else {
                        // Xóa active từ tất cả danh mục
                        categoryLinks.forEach(l => l.classList.remove("active"));
                        this.classList.add("active");

                        // Ẩn tất cả sản phẩm trước khi lọc
                        products.forEach(product => {
                            if (product.getAttribute("data-category") ===
                                selectedCategory) {
                                product.style.display = "block";
                            } else {
                                product.style.display = "none";
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection