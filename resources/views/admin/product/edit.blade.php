@extends('admin.layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Chỉnh Sửa Sản Phẩm</h1>

        <form action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-4">
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

                <div>
                    <label for="price_sale" class="block text-sm font-medium">Giá Khuyến Mãi</label>
                    <input type="number" id="price_sale" name="price_sale"
                        class="  text-black w-full p-2 border rounded-lg @error('price_sale') border-red-500 @enderror"
                        value="{{ old('price_sale', $product->price_sale) }}">
                    @error('price_sale')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">Danh Mục</label>
                    <select class="  text-black form-control" id="category_id" name="category_id">
                        <option value="">Chọn Danh Mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ $category->category_id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="product_images" class="block text-sm font-medium">Hình Ảnh Sản Phẩm Chính</label>
                    <input type="file" id="product_images" name="product_images[]" multiple
                        class="  text-black w-full p-2 border rounded-lg @error('product_images') border-red-500 @enderror">

                    @error('product_images')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hiển thị ảnh của sản phẩm chính -->
                @if(isset($product) && $product->images->where('variant_id', null)->count() > 0)
                    <div class="mt-4">
                        <p class="text-sm font-medium">Ảnh Sản Phẩm Chính:</p>
                        <div class="flex gap-2">
                            @foreach($product->images->where('variant_id', null) as $image)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $image->image_url) }}" class="w-20 h-20 object-cover rounded-lg border">

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
                <p id="total_stock" class="font-bold text-lg">{{ $product->stock }}</p>
                <input type="hidden" name="stock" id="total_stock_input" value="{{ $product->stock }}">
            </div>

            <!-- Variant Fields -->
            <div id="variant_fields">
                @foreach($product->variants as $index => $variant)
                <div class="variant mt-3 border p-4 rounded-lg shadow-md">
                    <h2 class="font-bold text-lg">Biến thể {{ $index + 1 }}</h2>
            
                    <!-- Thêm variant_id -->
                    <input type="hidden" name="variants[{{ $index }}][variant_id]" value="{{ $variant->variant_id }}">
            
                    <!-- Giá và giá khuyến mãi -->
                    <div class="form-group">
                        <label>Giá</label>
                        <input type="number" class="  text-black form-control" name="variants[{{ $index }}][price]"
                            value="{{ $variant->price }}">
                    </div>
            
                    <div class="form-group">
                        <label>Giá Khuyến Mãi</label>
                        <input type="number" class="  text-black form-control" name="variants[{{ $index }}][price_sale]"
                            value="{{ $variant->price_sale }}">
                    </div>
            
                    <div class="form-group">
                        <label>Số lượng</label>
                        <input type="number" class="form-control" name="variants[{{ $index }}][stock]" 
                        value="{{ $variant->stock }}">
                    </div>
            
                    <!-- Chọn màu sắc -->
                    @php
                    // Lấy tất cả các màu và kích cỡ có sẵn
                    $allColors = \App\Models\VariantAttribute::where('attribute_name', 'Color')->pluck('attribute_value');
                    $allSizes = \App\Models\VariantAttribute::where('attribute_name', 'Size')->pluck('attribute_value');
                    @endphp
            
                    <div class="form-group">
                        <label for="color">Màu sắc</label>
                        <select class="text-black form-control" name="variants[{{ $index }}][color]">
                            <option value="">Chọn màu</option>
                            @foreach($allColors as $color)
                                <option value="{{ $color }}" 
                                    {{ old('variants.' . $index . '.color', optional($variant->variantAttributeValues->where('variantAttribute.attribute_name', 'Color')->first())->variantAttribute->attribute_value) == $color ? 'selected' : '' }}>
                                    {{ $color }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Chọn kích cỡ -->
                    <div class="form-group">
                        <label for="size">Chọn kích cỡ</label>
                        <select class="text-black form-control" name="variants[{{ $index }}][size]">
                            <option value="">Chọn kích cỡ</option>
                            @foreach($allSizes as $size)
                                <option value="{{ $size }}" 
                                    {{ old('variants.' . $index . '.size', optional($variant->variantAttributeValues->where('variantAttribute.attribute_name', 'Size')->first())->variantAttribute->attribute_value) == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Hình ảnh biến thể -->
                    <div class="form-group">
                        <label>Hình Ảnh Biến Thể</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($variant->images as $image)
                                <div class="relative">
                                    <img src="{{ asset('' . $image->image_url) }}" class="w-20 h-20 object-cover rounded-lg border">
                                </div>
                            @endforeach
                        </div>
                        <input type="file" class="form-control mt-2" name="variants[{{ $index }}][images][]" multiple>
                    </div>
            
                    <!-- Xóa biến thể -->
                    <button type="button" class="btn btn-danger delete-variant" data-variant-id="{{ $variant->variant_id }}"
                        data-product-id="{{ $product->product_id }}">Xóa Biến Thể</button>
                </div>
            @endforeach
            
            </div>

            <!-- Thêm Biến Thể -->
            <button type="button" class="btn btn-primary" id="add_variant_button">Thêm Biến Thể</button>

            <button type="submit" class="btn btn-success mt-2">Cập Nhật</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    
            const addVariantButton = document.getElementById('add_variant_button');
            const variantFieldsContainer = document.getElementById('variant_fields');
            let variantIndex = {{ count($product->variants) }}; // Số lượng biến thể hiện tại
    
            // Lấy danh sách màu sắc và kích cỡ từ Blade
            const colors = @json($colors);
            const sizes = @json($sizes);
            const existingVariants = new Set();
    
            // Lấy biến thể cũ từ database
            const oldVariants = [
        @foreach($product->variants as $variant)
            { color: "{{ $variant->color }}", size: "{{ $variant->size }}" },
        @endforeach
    ];
            // Thêm các biến thể cũ vào danh sách kiểm tra trùng lặp
            oldVariants.forEach(variant => {
                if (variant.color && variant.size) {
                    existingVariants.add(`${variant.color}-${variant.size}`);
                }
            });
    
            // Khi nhấn nút "Thêm Biến Thể"
            addVariantButton.addEventListener('click', function () {
                const newVariant = document.createElement('div');
                newVariant.classList.add('variant', 'mt-3', 'border', 'p-4', 'rounded-lg', 'shadow-md');
    
                // Tạo danh sách option cho Màu sắc và Kích cỡ
                let colorOptions = colors.map(color => `<option value="${color.attribute_value}">${color.attribute_value}</option>`).join('');
                let sizeOptions = sizes.map(size => `<option value="${size.attribute_value}">${size.attribute_value}</option>`).join('');
    
                // HTML của biến thể mới
                newVariant.innerHTML = `
                    <div class="form-group">
                        <label>Giá</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][price]" value="">
                    </div>
                    <div class="form-group">
                        <label>Giá Khuyến Mãi</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][price_sale]" value="">
                    </div>
                    <div class="form-group">
                        <label>Số lượng</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][stock]" value="">
                    </div>
                    <div class="form-group">
                        <label>Màu sắc</label>
                        <select class="form-control variant-color" name="variants[${variantIndex}][color]">
                            <option value="">Chọn màu</option>
                            ${colorOptions}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kích cỡ</label>
                        <select class="form-control variant-size" name="variants[${variantIndex}][size]">
                            <option value="">Chọn kích cỡ</option>
                            ${sizeOptions}
                        </select>
                    </div>
              <div class="form-group">
    <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể (Tối đa 5 ảnh)</label>
    <input type="file" class="form-control variant-image-input"
           id="variant_images_${variantIndex}"
           name="variants[${variantIndex}][images][]"
           multiple accept="image/*"
           onchange="previewImage(event, ${variantIndex})">
    <div class="image-preview" id="image_preview_${variantIndex}"></div>
</div>


                    <p class="text-danger error-message d-none" style="display: none;">⚠️ Biến thể với Màu và Size này đã tồn tại!</p>
                    <button type="button" class="btn btn-danger remove-variant">Xóa Biến Thể</button>
                `;
    
                // Thêm biến thể vào danh sách
                variantFieldsContainer.appendChild(newVariant);
                variantIndex++;
    
                // Gán sự kiện kiểm tra trùng lặp khi thay đổi Màu hoặc Kích cỡ
                newVariant.querySelector('.variant-color').addEventListener('change', checkDuplicateVariant);
                newVariant.querySelector('.variant-size').addEventListener('change', checkDuplicateVariant);
    
                // Gán sự kiện xóa biến thể
                newVariant.querySelector('.remove-variant').addEventListener('click', function () {
                    removeVariant(this);
                });
            });
    
            // 🔥 Kiểm tra trùng lặp khi chọn Màu + Size
            function checkDuplicateVariant() {
                let hasDuplicate = false;
                let currentVariants = new Set(existingVariants); // Copy từ biến thể cũ
    
                document.querySelectorAll('.variant').forEach(variant => {
                    const colorElement = variant.querySelector('.variant-color');
                    const sizeElement = variant.querySelector('.variant-size');
                    const errorMsg = variant.querySelector('.error-message');
    
                    if (colorElement && sizeElement) {
                        const color = colorElement.value;
                        const size = sizeElement.value;
    
                        // Kiểm tra nếu đã chọn cả màu & size
                        if (color && size) {
                            const key = `${color}-${size}`;
                            if (currentVariants.has(key)) {
                                errorMsg.classList.remove('d-none'); // Hiển thị lỗi
                                errorMsg.style.display = "block";
                                hasDuplicate = true;
                            } else {
                                errorMsg.classList.add('d-none');
                                errorMsg.style.display = "none";
                                currentVariants.add(key);
                            }
                        }
                    }
                });
    
                return hasDuplicate;
            }
    
            // 🗑️ Xóa biến thể
            document.querySelectorAll(".delete-variant").forEach(button => {
                button.addEventListener("click", function () {
                    const variantId = this.getAttribute("data-variant-id");
                    const productId = this.getAttribute("data-product-id");
    
                    if (!productId || !variantId) {
                        alert("Có lỗi xảy ra. Product ID hoặc Variant ID không hợp lệ.");
                        return;
                    }
    
                    if (!confirm("Bạn có chắc muốn xóa biến thể này không?")) return;
    
                    fetch(`/admin/products/${productId}/variants/${variantId}/delete`, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            "Content-Type": "application/json",
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("✅ " + data.message);
                            location.reload();
                        } else {
                            alert("❌ " + data.message);
                        }
                    })
                    .catch(error => console.error("Lỗi khi xóa biến thể:", error));
                });
            });
     
    
            // 🚀 Kiểm tra trước khi submit
            document.querySelector('form').addEventListener('submit', function (event) {
                if (checkDuplicateVariant()) {
                    event.preventDefault();
                    alert('⚠️ Có biến thể trùng Color & Size (bao gồm cả biến thể cũ). Hãy kiểm tra lại!');
                }
            });
    
        });
    </script>
    
    
        
    
    


@endsection