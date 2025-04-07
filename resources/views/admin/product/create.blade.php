@extends('admin.layout')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Thêm Sản Phẩm Mới và Biến Thể</h1>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Main Product Fields -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Tên Sản Phẩm</label>
            <input type="text" id="name" name="name"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror"
                value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
            <input type="text" id="description" name="description"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('description') border-red-500 @enderror"
                value="{{ old('description') }}">
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Giá</label>
            <input type="number" id="price" name="price"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('price') border-red-500 @enderror"
                value="{{ old('price') }}">
            @error('price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price_sale" class="block text-sm font-medium text-gray-700">Giá Khuyến Mãi</label>
            <input type="number" id="price_sale" name="price_sale"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('price_sale') border-red-500 @enderror"
                value="{{ old('price_sale') }}">
            @error('price_sale')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="total_stock_input" class="block text-sm font-medium text-gray-700">Số Lượng</label>
            <input type="number" name="stock" id="total_stock_input"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('stock') border-red-500 @enderror"
                value="{{ old('stock') }}">
            @error('stock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh Mục</label>
            <select class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('category_id') border-red-500 @enderror"
                id="category_id" name="category_id">
                <option value="">Chọn Danh Mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="product_images" class="block text-sm font-medium text-gray-700">Hình Ảnh Sản Phẩm Chính</label>
            <input type="file" id="product_images" name="product_images[]" multiple
                class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('product_images') border-red-500 @enderror">
            @error('product_images')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Variant Fields -->
        <div id="variant_fields" class="space-y-4"></div>
        <button type="button" id="add_variant_btn"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Thêm Biến Thể</button>

        <div class="flex gap-4 mt-6">
            <button type="submit"
                class="bg-red-600 text-red px-6 py-2 rounded-lg hover:bg-green-700 transition-all">Lưu</button>
            <a href="{{ route('products.index') }}"
                class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 transition-all">Quay lại</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addVariantBtn = document.getElementById('add_variant_btn');
        const variantFieldsContainer = document.getElementById('variant_fields');
        let variantIndex = 0;
        let colorImages = {}; // Lưu ảnh theo màu

        addVariantBtn.addEventListener('click', function () {
            const newVariant = document.createElement('div');
            newVariant.classList.add('variant', 'mt-3', 'border', 'p-3', 'rounded-lg', 'shadow-md');

            newVariant.innerHTML = `
                <div class="form-group">
                    <label>Giá</label>
                    <input type="number" class="form-control variant-price border border-dark text-black w-full" name="variants[${variantIndex}][price]" value="">
                </div>
                <div class="form-group">
                    <label>Giá Khuyến Mãi</label>
                    <input type="number" class="form-control variant-sale-price border border-dark text-black w-full" name="variants[${variantIndex}][price_sale]" value="">
                </div>
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" class="form-control variant-stock border border-dark text-black w-full" name="variants[${variantIndex}][stock]" value="0">
                </div>
                <div class="form-group">
                    <label>Màu sắc</label>
                    <select class="form-control variant-color border border-dark text-black w-full" name="variants[${variantIndex}][color]">
                        <option value="">Chọn màu</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->attribute_value }}">{{ $color->attribute_value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Kích cỡ</label>
                    <select class="form-control variant-size border border-dark text-black w-full" name="variants[${variantIndex}][size]">
                        <option value="">Chọn kích cỡ</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->attribute_value }}">{{ $size->attribute_value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể (Tối đa 5 ảnh)</label>
                    <input type="file" class="form-control variant-image-input border border-dark w-full"
                        id="variant_images_${variantIndex}"
                        name="variants[${variantIndex}][images][]"
                        multiple accept="image/*">
                    <div class="image-preview" id="image_preview_${variantIndex}"></div>
                </div>
                <p class="text-danger error-message d-none" style="display: none;">⚠️ Biến thể với Màu và Size này đã tồn tại!</p>
                <button type="button" class="btn btn-danger mt-2 remove-variant">Xóa Biến Thể</button>
            `;

            variantFieldsContainer.appendChild(newVariant);
            variantIndex++;

            const errorMsg = newVariant.querySelector('.error-message');
            errorMsg.classList.add('d-none');
            errorMsg.style.display = "none";

            const colorSelect = newVariant.querySelector('.variant-color');
            const sizeSelect = newVariant.querySelector('.variant-size');
            const imageInput = newVariant.querySelector('.variant-image-input');
            const imagePreview = newVariant.querySelector('.image-preview');

            colorSelect.addEventListener('change', function () {
                checkDuplicateVariant();
                autoFillImages(newVariant);
            });

            sizeSelect.addEventListener('change', function () {
                checkDuplicateVariant();
            });

            imageInput.addEventListener('change', function (event) {
                let color = colorSelect.value;
                if (color) {
                    let files = event.target.files;
                    if (files.length > 0) {
                        colorImages[color] = files;
                        displayImages(imagePreview, files);
                    }
                }
            });

            newVariant.querySelector('.remove-variant').addEventListener('click', function () {
                newVariant.remove();
                checkDuplicateVariant();
            });
        });

        function checkDuplicateVariant() {
            let existingVariants = new Set();

            document.querySelectorAll('.variant').forEach(variant => {
                const color = variant.querySelector('.variant-color').value;
                const size = variant.querySelector('.variant-size').value;
                const errorMsg = variant.querySelector('.error-message');

                errorMsg.classList.add('d-none');
                errorMsg.style.display = "none";

                if (color && size) {
                    const key = `${color}-${size}`;
                    if (existingVariants.has(key)) {
                        errorMsg.classList.remove('d-none');
                        errorMsg.style.display = "block";
                    } else {
                        existingVariants.add(key);
                    }
                }
            });
        }

        function autoFillImages(variantElement) {
            let color = variantElement.querySelector('.variant-color').value;
            let imagePreview = variantElement.querySelector('.image-preview');
            let imageInput = variantElement.querySelector('.variant-image-input');

            imagePreview.innerHTML = "";
            imageInput.value = "";

            if (colorImages[color]) {
                displayImages(imagePreview, colorImages[color]);
            }
        }

        function displayImages(previewContainer, files) {
            previewContainer.innerHTML = "";
            Array.from(files).forEach(file => {
                let img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.width = "80px";
                img.style.marginRight = "5px";
                img.style.borderRadius = "8px";
                previewContainer.appendChild(img);
            });
        }

        // ✅ VALIDATION FORM TRƯỚC KHI SUBMIT
        document.querySelector('form').addEventListener('submit', function (event) {
            let isValid = true;

            if (document.querySelector('.error-message:not(.d-none)')) {
                alert('Có biến thể bị trùng Color & Size. Hãy kiểm tra lại!');
                event.preventDefault();
                return;
            }

            document.querySelectorAll('.variant').forEach(variant => {
                const price = variant.querySelector('.variant-price');
                const salePrice = variant.querySelector('.variant-sale-price');
                const stock = variant.querySelector('.variant-stock');
                const color = variant.querySelector('.variant-color');
                const size = variant.querySelector('.variant-size');

                clearError(price);
                clearError(salePrice);
                clearError(stock);
                clearError(color);
                clearError(size);

                if (price.value.trim() === '' || parseFloat(price.value) < 0) {
                    showError(price, 'Giá không hợp lệ');
                    isValid = false;
                }

                if (salePrice.value !== '' && parseFloat(salePrice.value) < 0) {
                    showError(salePrice, 'Giá khuyến mãi không hợp lệ');
                    isValid = false;
                }

                if (stock.value.trim() === '' || parseInt(stock.value) < 0) {
                    showError(stock, 'Số lượng không hợp lệ');
                    isValid = false;
                }

                if (!color.value) {
                    showError(color, 'Vui lòng chọn màu');
                    isValid = false;
                }

                if (!size.value) {
                    showError(size, 'Vui lòng chọn kích cỡ');
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Vui lòng điền đầy đủ và hợp lệ tất cả các trường của biến thể.');
            }
        });

        function showError(inputEl, message) {
            let errorEl = document.createElement('p');
            errorEl.className = 'text-red-500 text-sm mt-1 input-error';
            errorEl.textContent = message;
            inputEl.classList.add('border-red-500');
            inputEl.parentNode.appendChild(errorEl);
        }

        function clearError(inputEl) {
            inputEl.classList.remove('border-red-500');
            const errorEl = inputEl.parentNode.querySelector('.input-error');
            if (errorEl) errorEl.remove();
        }
    });
</script>

    
@endsection