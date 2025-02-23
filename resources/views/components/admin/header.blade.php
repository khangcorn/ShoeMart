<nav class="bg-gray-900 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <a class="text-lg font-bold hover:text-gray-400 transition" href="{{ url('/') }}">Trang chủ</a>
        <button class="lg:hidden text-white focus:outline-none" onclick="toggleMenu()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
        <div class="hidden lg:flex space-x-6" id="navbarNav">
            <a class="hover:text-gray-400 transition" href="{{ route('categories.index') }}">Danh mục</a>
            <a class="hover:text-gray-400 transition" href="{{ route('products.index') }}">Sản phẩm</a>
            <a class="hover:text-gray-400 transition" href="{{ route('product_variants.index') }}">Product Variant</a>
        </div>
    </div>
</nav>

<script>
    function toggleMenu() {
        document.getElementById("navbarNav").classList.toggle("hidden");
    }
</script>
