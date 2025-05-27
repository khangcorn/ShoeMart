@extends('admin.layout')

@section('content')
    @if ($errors->has('image_format'))
        <div id="error-messages" class="alert-error-custom">
            {{ $errors->first('image_format') }}
        </div>
    @endif

    <style>
        .alert-error-custom {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            display: inline-block;
            padding: 10px 18px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            margin-right: 8px;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }
    </style>




    <div class="px-4 py-4">
        <h1 class="text-3xl font-bold mb-6">Chỉnh Sửa Sản Phẩm</h1>

        <form id="product_form" action="{{ route('products.update', $product->product_id) }}" method="POST"
            enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Main Product Fields -->
            <div>
                <label for="name" class="block text-sm font-medium">Tên Sản Phẩm</label>
                <input type="text" id="name" name="name"
                    class=" text-black w-full p-2 border rounded-lg @error('name') border-red-500 @enderror"
                    value="{{ old('name', $product->name) }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                <div>
                    <label for="description" class="block text-sm font-medium">Mô tả</label>
                    <input type="text" id="description" name="description"
                        class="  text-black w-full p-2 border rounded-lg @error('description') border-red-500 @enderror"
                        value="{{ old('description', $product->description) }}">
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium">Giá</label>
                    <input type="number" id="price" name="price"
                        class="  text-black w-full p-2 border rounded-lg @error('price') border-red-500 @enderror"
                        value="{{ old('price', $product->price) }}">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @if ($product->variants->isEmpty())
                    <div>
                        <label for="stock" class="block text-sm font-medium">Số Lượng </label>
                        <input type="number" id="stock" name="stock"
                            class="text-black w-full p-2 border rounded-lg @error('stock') border-red-500 @enderror"
                            value="{{ old('stock', $product->stock) }}">
                        @error('stock')
                            {{-- <p class="text-red-500 text-sm mt-1">{{ $message }}</p> --}}
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="price_sale" class="block text-sm font-medium">Giá Khuyến Mãi</label>
                    <input type="number" id="price_sale" name="price_sale"
                        class="  text-black w-full p-2 border rounded-lg @error('price_sale') border-red-500 @enderror"
                        value="{{ old('price_sale', $product->price_sale) }}">
                    @error('price_sale')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <input type="hidden" id="product_price" name="product_price"
                    value="{{ old('product_price', $product->price ?? '') }}">
                <input type="hidden" id="product_stock_input" name="product_stock"
                    value="{{ old('product_stock', $product->stock ?? '') }}">

                <div class="form-group">
                    <label for="category_id">Danh Mục</label>
                    <select class="  text-black form-control" id="category_id" name="category_id">
                        <option value="">Chọn Danh Mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}"
                                {{ $category->category_id == $product->category_id ? 'selected' : '' }}>
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="product_images" class="block text-sm font-medium">Hình Ảnh Sản Phẩm Chính</label>
                    <input type="file" id="product_images" name="product_images[]" multiple
                        class="text-black w-full p-2 border rounded-lg @error('product_images') border-red-500 @enderror">

                    @error('product_images')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Hiển thị ảnh của sản phẩm chính -->
                @if (isset($product) && $product->images->where('variant_id', null)->count() > 0)
                    <div class="mt-4">
                        <p class="text-sm font-medium">Ảnh Sản Phẩm Chính:</p>
                        <div class="flex gap-2">
                            @foreach ($product->images->where('variant_id', null) as $image)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $image->image_url) }}"
                                        class="w-20 h-20 object-cover rounded-lg border">

                                    <button type="button"
                                        class="absolute top-0 right-0 bg-red-500 text-white p-1 text-xs rounded remove-image"
                                        data-image-id="{{ $image->image_id }}">X</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium">Tổng Số Lượng</label>
                <p id="total_stock" class="font-bold text-lg">
                    @if ($product->variants->isEmpty())
                        {{ $product->stock }} <!-- Hiển thị số lượng sản phẩm nếu không có biến thể -->
                    @else
                        {{ $product->variants->sum('stock') }} <!-- Hiển thị tổng số lượng biến thể nếu có -->
                    @endif
                </p>
                @if (!$product->variants->isEmpty())
                    <input type="hidden" name="stock" id="total_stock_input"
                        value="{{ $product->variants->sum('stock') }}">
                @endif

            </div>

            <!-- Variant Fields -->
            <div id="variant_fields">
                @foreach ($product->variants as $index => $variant)
                    <div class="variant mt-3 border p-4 rounded-lg shadow-md">
                        <h2 class="font-bold text-lg">Biến thể {{ $index + 1 }}</h2>

                        <!-- Thêm variant_id -->
                        <input type="hidden" name="variants[{{ $index }}][variant_id]"
                            value="{{ $variant->variant_id }}">

                        <!-- Giá và giá khuyến mãi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div class="form-group">
                                <label class="text-dark">Giá</label>
                                <input type="number" class="form-control text-black border border-dark w-full"
                                    name="variants[{{ $index }}][price]" value="{{ $variant->price }}">
                            </div>

                            <div class="form-group">
                                <label class="text-dark">Giá Khuyến Mãi</label>
                                <input type="number" class="form-control text-black border border-dark w-full"
                                    name="variants[{{ $index }}][price_sale]" value="{{ $variant->price_sale }}">
                            </div>


                            <div class="form-group">
                                <label class="text-dark">Số lượng cho biến thể {{ $variant->id }}</label>
                                <input type="number" class="form-control text-black border border-dark w-full"
                                    name="variants[{{ $index }}][stock]" value="{{ $variant->stock }}">
                            </div>

                        </div>

                        <!-- Chọn màu sắc -->
                        @php
                            $allColors = \App\Models\VariantAttribute::where('attribute_name', 'Color')->pluck(
                                'attribute_value',
                            );
                            $allSizes = \App\Models\VariantAttribute::where('attribute_name', 'Size')->pluck(
                                'attribute_value',
                            );
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div class="form-group">
                                <label class="text-dark">Màu sắc</label>
                                <select class="form-control text-black border border-dark w-full variant-color"
                                    name="variants[{{ $index }}][color]">
                                    <option value="">Chọn màu</option>
                                    @foreach ($allColors as $color)
                                        @php
                                            // Lấy giá trị color của biến thể hiện tại, nếu có
                                            $selectedColor =
                                                optional(
                                                    $variant->variantAttributeValues
                                                        ->where('variantAttribute.attribute_name', 'Color')
                                                        ->first(),
                                                )->variantAttribute->attribute_value ?? '';
                                        @endphp
                                        <option value="{{ $color }}"
                                            {{ old('variants.' . $index . '.color', $selectedColor) == $color ? 'selected' : '' }}>
                                            {{ $color }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="text-dark">Chọn kích cỡ</label>
                                <select class="form-control text-black border border-dark w-full variant-size"
                                    name="variants[{{ $index }}][size]">
                                    <option value="">Chọn kích cỡ</option>
                                    @foreach ($allSizes as $size)
                                        @php
                                            // Lấy giá trị size của biến thể hiện tại, nếu có
                                            $selectedSize =
                                                optional(
                                                    $variant->variantAttributeValues
                                                        ->where('variantAttribute.attribute_name', 'Size')
                                                        ->first(),
                                                )->variantAttribute->attribute_value ?? '';
                                        @endphp
                                        <option value="{{ $size }}"
                                            {{ old('variants.' . $index . '.size', $selectedSize) == $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-red size-error-msg" style="display: none;"></p>
                            </div>

                        </div>

                        <!-- Hình ảnh biến thể -->
                        <div class="form-group mb-4">
                            <label class="text-dark">Hình Ảnh Biến Thể(Tối đa 5 ảnh)</label>

                            <!-- Preview ảnh (dùng id riêng theo index biến thể) -->
                            <div id="preview-container-{{ $index }}" class="grid grid-cols-3 gap-2 mb-4">
                                @foreach ($variant->images as $image)
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $image->image_url) }}"
                                            class="w-20 h-20 object-cover rounded-lg border">
                                    </div>
                                @endforeach
                            </div>

                            <!-- Input chọn ảnh mới -->
                            <input type="file" class="form-control mt-2 border border-dark w-full preview-multi-input"
                                name="variants[{{ $index }}][images][]" multiple
                                data-preview-container="preview-container-{{ $index }}">
                            <p class="text-red-500 text-sm mt-1 image-error-msg" id="image_error_{{ $index }}"
                                style="display: none;"></p> <!-- Phần tử hiển thị lỗi -->
                        </div>

                        {{--             
                    <!-- Xóa biến thể -->
                    <button type="button" class="btn btn-danger delete-variant" data-variant-id="{{ $variant->variant_id }}"
                        data-product-id="{{ $product->product_id }}">Xóa Biến Thể</button> --}}
                    </div>
                @endforeach
            </div>


            <!-- Thêm Biến Thể -->
            <button type="button" class="btn-custom btn-primary" id="add_variant_button">Thêm Biến Thể</button>
            <button type="submit" class="btn-custom btn-success mt-2">Cập Nhật</button>
            <a href="{{ route('products.index') }}" class="btn-custom btn-secondary mt-2">Quay lại</a>

        </form>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productForm = document.getElementById('product_form');
            const addVariantButton = document.getElementById('add_variant_button');
            const variantFieldsContainer = document.getElementById('variant_fields');
            let variantIndex = {{ count($product->variants) }};
            const colors = @json($colors);
            const sizes = @json($sizes);

            function getExistingVariants(ignoreElement = null) {
                let existingVariants = [];
                document.querySelectorAll('.variant').forEach(variant => {
                    if (ignoreElement && variant === ignoreElement) return;
                    let colorSelect = variant.querySelector('select[name*="[color]"]');
                    let sizeSelect = variant.querySelector('select[name*="[size]"]');
                    if (colorSelect && sizeSelect) {
                        let color = colorSelect.value.trim().toLowerCase();
                        let size = sizeSelect.value.trim().toLowerCase();
                        if (color && size) {
                            existingVariants.push({
                                color,
                                size
                            });
                        }
                    }
                });
                return existingVariants;
            }

            function validateVariant(newVariant) {
                let colorSelect = newVariant.querySelector('.variant-color');
                let sizeSelect = newVariant.querySelector('.variant-size');
                let errorMsg = newVariant.querySelector('.size-error-msg');

                if (!colorSelect || !sizeSelect || !errorMsg) return true;

                let color = colorSelect.value.trim().toLowerCase();
                let size = sizeSelect.value.trim().toLowerCase();

                if (color && size) {
                    let existingVariants = getExistingVariants(newVariant);
                    let isDuplicate = existingVariants.some(v => v.color === color && v.size === size);
                    if (isDuplicate) {
                        errorMsg.innerText = "⚠ Biến thể này đã tồn tại.";
                        errorMsg.style.display = "block";
                        return false;
                    } else {
                        errorMsg.style.display = "none";
                        return true;
                    }
                } else {
                    errorMsg.style.display = "none";
                    return true;
                }
            }

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

            addVariantButton.addEventListener('click', function() {
                const newVariant = document.createElement('div');
                newVariant.classList.add('variant', 'mt-3', 'border', 'p-4', 'rounded-lg', 'shadow-md');

                let colorOptions = colors.map(c =>
                    `<option value="${c.attribute_value}">${c.attribute_value}</option>`).join('');
                let sizeOptions = sizes.map(s =>
                    `<option value="${s.attribute_value}">${s.attribute_value}</option>`).join('');

                newVariant.innerHTML = `
                     <div class="form-group mb-4">
    <label for="variant-price-${variantIndex}" class="text-gray-700 font-semibold">Giá</label>
    <input id="variant-price-${variantIndex}" type="number" class="form-control variant-price border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][price]" value="">
</div>

<div class="form-group mb-4">
    <label for="variant-sale-price-${variantIndex}" class="text-gray-700 font-semibold">Giá Khuyến Mãi</label>
    <input id="variant-sale-price-${variantIndex}" type="number" class="form-control variant-sale-price border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][price_sale]" value="">
</div>

<div class="form-group mb-4">
    <label for="variant-stock-${variantIndex}" class="text-gray-700 font-semibold">Số lượng</label>
    <input id="variant-stock-${variantIndex}" type="number" class="form-control variant-stock border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][stock]" value="0">
</div>

<div class="form-group mb-4">
    <label for="variant-color-${variantIndex}" class="text-gray-700 font-semibold">Màu sắc</label>
    <select id="variant-color-${variantIndex}" class="form-control variant-color border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][color]">
        <option value="">Chọn màu</option>
        @foreach ($colors as $color)
            <option value="{{ $color->attribute_value }}">{{ $color->attribute_value }}</option>
        @endforeach
    </select>
</div>

<div class="form-group mb-4">
    <label for="variant-size-${variantIndex}" class="text-gray-700 font-semibold">Kích cỡ</label>
    <select id="variant-size-${variantIndex}" class="form-control variant-size border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][size]">
        <option value="">Chọn kích cỡ</option>
        @foreach ($sizes as $size)
            <option value="{{ $size->attribute_value }}">{{ $size->attribute_value }}</option>
        @endforeach
    </select>
</div>

<div class="form-group mb-4">
    <label for="variant-images-${variantIndex}" class="text-gray-700 font-semibold">Hình Ảnh Biến Thể (Tối đa 5 ảnh)</label>
    <input type="file" class="form-control variant-image-input border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500" id="variant-images-${variantIndex}" name="variant_images_${variantIndex}[]" multiple accept="image/*">
    <div class="image-preview mt-2" id="image-preview-${variantIndex}"></div>
</div>

<p class="text-danger error-message text-red-500 d-none" style="display: none;">⚠️ Biến thể với Màu và Size này đã tồn tại!</p>

<button type="button" class="btn btn-danger mt-4 bg-red-500 text-white rounded-lg px-4 py-2 hover:bg-red-600 focus:outline-none remove-variant">Xóa Biến Thể</button>
                `;

                variantFieldsContainer.appendChild(newVariant);
                variantIndex++;

                let colorSelect = newVariant.querySelector('.variant-color');
                let sizeSelect = newVariant.querySelector('.variant-size');

                colorSelect.addEventListener('change', function() {
                    validateVariant(newVariant);
                });
                sizeSelect.addEventListener('change', function() {
                    validateVariant(newVariant);
                });

                newVariant.querySelector('.remove-variant').addEventListener('click', function() {
                    newVariant.remove();
                });
            });

            // Gắn validate cho các biến thể đã tồn tại (render từ backend)
            document.querySelectorAll('.variant').forEach(existingVariant => {
                let colorSelect = existingVariant.querySelector('.variant-color');
                let sizeSelect = existingVariant.querySelector('.variant-size');

                if (colorSelect && sizeSelect) {
                    colorSelect.addEventListener('change', function() {
                        validateVariant(existingVariant);
                    });
                    sizeSelect.addEventListener('change', function() {
                        validateVariant(existingVariant);
                    });
                }
            });
            // Xử lý xóa biến thể từ database
            document.querySelectorAll(".delete-variant").forEach(button => {
                button.addEventListener("click", function() {
                    if (!confirm("Bạn có chắc muốn xóa biến thể này không?")) return;
                    const variantId = this.getAttribute("data-variant-id");
                    const productId = this.getAttribute("data-product-id");
                    if (!productId || !variantId) return alert(
                        "Có lỗi xảy ra. Product ID hoặc Variant ID không hợp lệ.");

                    fetch(`/admin/products/${productId}/variants/${variantId}/delete`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute("content"),
                                "Content-Type": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.success ? "✅ " + data.message : "❌ " + data.message);
                            if (data.success) location.reload();
                        })
                        .catch(error => console.error("Lỗi khi xóa biến thể:", error));
                });
            });
            document.querySelectorAll('.preview-multi-input').forEach(input => {
                input.addEventListener('change', function() {
                    const containerId = this.dataset.previewContainer;
                    const previewContainer = document.getElementById(containerId);

                    // Tìm phần tử hiển thị lỗi ảnh gần input (cùng cha)
                    const errorElement = this.parentElement.querySelector('.image-error-msg');

                    // Xóa ảnh cũ trong khung preview và ẩn lỗi cũ (nếu có)
                    previewContainer.innerHTML = '';
                    errorElement.style.display = 'none';
                    errorElement.textContent = '';

                    let files = Array.from(this.files);

                    // Kiểm tra định dạng ảnh trước
                    let invalidFiles = files.filter(file => !file.type.startsWith('image/'));
                    if (invalidFiles.length > 0) {
                        // Hiển thị alert nếu có file không phải là ảnh
                        alert('❌ Chỉ cho phép tải lên các file hình ảnh (jpg, jpeg, png, gif)');
                        errorElement.textContent =
                            '❌ Chỉ cho phép tải lên các file hình ảnh (jpg, jpeg, png, gif)';
                        errorElement.style.display = 'block';
                        return; // Không tiếp tục xử lý nếu có lỗi
                    }

                    // Nếu hợp lệ thì hiển thị preview
                    files.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = "w-20 h-20 object-cover rounded-lg border";
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            });

            function validateImages() {
                let isValid = true;
                document.querySelectorAll('input[type="file"][name*="[images]"]').forEach(input => {
                    const errorElement = input.parentElement.querySelector('.image-error-msg');
                    if (!errorElement) return;

                    errorElement.style.display = 'none';
                    errorElement.textContent = '';

                    const files = Array.from(input.files);

                    const hasInvalidFile = files.some(file => !file.type.startsWith('image/'));
                    if (hasInvalidFile) {
                        errorElement.textContent =
                            '❌ Chỉ cho phép tải lên các file hình ảnh (jpg, jpeg, png, gif)';
                        errorElement.style.display = 'block';
                        isValid = false;
                    }

                    if (files.length > 5) {
                        errorElement.textContent = '❌ Tối đa chỉ được chọn 5 ảnh cho mỗi biến thể.';
                        errorElement.style.display = 'block';
                        isValid = false;
                    }
                });

                return isValid;
            }

            // Validate toàn bộ biến thể khi submit
            productForm.addEventListener('submit', function(event) {
                let allVariants = document.querySelectorAll('.variant');
                let isValid = true;
                if (allVariants.length === 0) {
                    alert("⚠ Sản phẩm phải có ít nhất một biến thể.");
                    event.preventDefault();
                    return;
                }
                let totalStock = 0;
                let prices = [];

                allVariants.forEach(variant => {
                    if (!validateVariant(variant)) isValid = false;

                    const priceInput = variant.querySelector('input[name*="[price]"]');
                    const salePriceInput = variant.querySelector('input[name*="[price_sale]"]');
                    const stockInput = variant.querySelector('input[name*="[stock]"]');
                    const colorSelect = variant.querySelector('select[name*="[color]"]');
                    const sizeSelect = variant.querySelector('select[name*="[size]"]');

                    clearError(priceInput);
                    clearError(salePriceInput);
                    clearError(stockInput);
                    clearError(colorSelect);
                    clearError(sizeSelect);

                    if (!priceInput.value || parseFloat(priceInput.value) < 0) {
                        showError(priceInput, "Giá không hợp lệ");
                        isValid = false;
                    }

                    if (salePriceInput.value && parseFloat(salePriceInput.value) < 0) {
                        showError(salePriceInput, "Giá khuyến mãi không hợp lệ");
                        isValid = false;
                    }

                    if (!stockInput.value || parseInt(stockInput.value) < 0) {
                        showError(stockInput, "Số lượng không hợp lệ");
                        isValid = false;
                    }

                    if (!colorSelect.value) {
                        showError(colorSelect, "Vui lòng chọn màu sắc");
                        isValid = false;
                    }

                    if (!sizeSelect.value) {
                        showError(sizeSelect, "Vui lòng chọn kích cỡ");
                        isValid = false;
                    }

                    // Lấy giá để tính giá sản phẩm (ưu tiên giá khuyến mãi nếu có)
                    let priceVal = parseFloat(priceInput.value);
                    let salePriceVal = salePriceInput.value ? parseFloat(salePriceInput.value) :
                        null;
                    if (!isNaN(priceVal)) {
                        if (salePriceVal !== null && !isNaN(salePriceVal) && salePriceVal > 0 &&
                            salePriceVal < priceVal) {
                            prices.push(salePriceVal);
                        } else {
                            prices.push(priceVal);
                        }
                    }

                    // Tính tổng số lượng
                    let stockVal = parseInt(stockInput.value);
                    if (!isNaN(stockVal)) {
                        totalStock += stockVal;
                    }
                });

                const imageValid = validateImages();

                if (!isValid || !imageValid) {
                    event.preventDefault();
                    alert("⚠ Vui lòng kiểm tra lại các biến thể. Có lỗi xảy ra!");
                    return;
                }

                // Lấy giá nhỏ nhất trong mảng giá tính được
                let productPrice = prices.length > 0 ? Math.min(...prices) : 0;

                // Gán giá và tổng số lượng cho input product (giả sử input ẩn có id product_price và product_stock_input)
                const productPriceInput = document.getElementById('product_price');
                const productStockInput = document.getElementById('product_stock_input');

                if (productPriceInput) {
                    productPriceInput.value = productPrice;
                }

                if (productStockInput) {
                    productStockInput.value = totalStock;
                }
            });

            if (document.getElementById('error-messages')) {
                setTimeout(function() {
                    document.getElementById('error-messages').style.display = 'none';
                }, 5000); // 5000ms = 5 giây
            }
        });
    </script>



@endsection
