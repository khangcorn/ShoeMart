@extends('admin.layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Thêm Sản Phẩm Mới và Biến Thể</h1>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Tên sản phẩm, giá, giá sale, số lượng, danh mục -->
            <div>
                <label for="name" class="block text-sm font-medium">Tên Sản Phẩm</label>
                <input type="text" id="name" name="name"
                    class="w-full p-2 border rounded-lg @error('name') border-red-500 @enderror" value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium">Giá</label>
                <input type="number" id="price" name="price"
                    class="w-full p-2 border rounded-lg @error('price') border-red-500 @enderror"
                    value="{{ old('price') }}">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price_sale" class="block text-sm font-medium">Giá Khuyến Mãi</label>
                <input type="number" id="price_sale" name="price_sale"
                    class="w-full p-2 border rounded-lg @error('price_sale') border-red-500 @enderror"
                    value="{{ old('price_sale') }}">
                @error('price_sale')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium">Số Lượng</label>
                <input type="number" id="stock" name="stock"
                    class="w-full p-2 border rounded-lg @error('stock') border-red-500 @enderror"
                    value="{{ old('stock') }}">
                @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục</label>
                <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Variant Fields -->
            <div id="variant_fields"></div> <!-- Container to hold variant groups -->
            <button type="button" class="btn btn-primary" id="add_variant_btn">Thêm Biến Thể</button>

            <button type="submit" class="btn btn-success mt-2">Lưu</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addVariantBtn = document.getElementById('add_variant_btn');
            const variantFieldsContainer = document.getElementById('variant_fields');
            let variantIndex = 0;  // Counter for variants

            addVariantBtn.addEventListener('click', function () {
                // Create new variant group
                const newVariant = document.createElement('div');
                newVariant.classList.add('variant', 'mt-3');

                // Add HTML for the new variant
                newVariant.innerHTML = `
                    <div class="form-group">
                        <label for="variant_price_${variantIndex}">Giá</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][price]" value="">
                    </div>

                    <div class="form-group">
                        <label for="variant_price_sale_${variantIndex}">Giá Khuyến Mãi</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][price_sale]" value="">
                    </div>

                    <div class="form-group">
                        <label for="variant_stock_${variantIndex}">Số Lượng</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][stock]" value="">
                    </div>

                    <!-- Color selection -->
                    <div class="form-group">
                        <label for="variant_color_${variantIndex}">Màu Sắc</label>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="variants[${variantIndex}][color]" value="Trắng">
                            <label class="form-check-label">Trắng</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="variants[${variantIndex}][color]" value="Đen">
                            <label class="form-check-label">Đen</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="variants[${variantIndex}][color]" value="Vàng">
                            <label class="form-check-label">Vàng</label>
                        </div>
                    </div>

                    <!-- Size selection -->
                    <div class="form-group">
                        <label for="variant_sizes_${variantIndex}">Kích Thước</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="variants[${variantIndex}][sizes][]" value="39">
                            <label class="form-check-label">39</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="variants[${variantIndex}][sizes][]" value="40">
                            <label class="form-check-label">40</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="variants[${variantIndex}][sizes][]" value="41">
                            <label class="form-check-label">41</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="variants[${variantIndex}][sizes][]" value="42">
                            <label class="form-check-label">42</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="variants[${variantIndex}][sizes][]" value="43">
                            <label class="form-check-label">43</label>
                        </div>
                    </div>

                    <!-- Image upload -->
                    <div class="form-group">
                        <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể</label>
                        <input type="file" class="form-control" name="variants[${variantIndex}][images][]" multiple>
                        <div id="variant_images_preview_${variantIndex}" class="mt-2"></div>
                    </div>

                    <button type="button" class="btn btn-danger" onclick="removeVariant(${variantIndex})">Xóa Biến Thể</button>
                `;

                // Append the new variant group
                variantFieldsContainer.appendChild(newVariant);

                variantIndex++;  // Increment the variant counter
            });
        });

        function removeVariant(index) {
            document.querySelector(`#variant_fields .variant:nth-child(${index + 1})`).remove();
        }
    </script>
@endsection
