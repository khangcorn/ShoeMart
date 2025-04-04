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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="form-group">
                            <label class="text-dark">Giá</label>
                            <input type="number" class="form-control text-black border border-dark w-full" name="variants[{{ $index }}][price]"
                                value="{{ $variant->price }}">
                        </div>
            
                        <div class="form-group">
                            <label class="text-dark">Giá Khuyến Mãi</label>
                            <input type="number" class="form-control text-black border border-dark w-full" name="variants[{{ $index }}][price_sale]"
                                value="{{ $variant->price_sale }}">
                        </div>
            
                        <div class="form-group">
                            <label class="text-dark">Số lượng</label>
                            <input type="number" class="form-control text-black border border-dark w-full" name="variants[{{ $index }}][stock]" 
                            value="{{ $variant->stock }}">
                        </div>
                    </div>
            
                    <!-- Chọn màu sắc -->
                    @php
                    $allColors = \App\Models\VariantAttribute::where('attribute_name', 'Color')->pluck('attribute_value');
                    $allSizes = \App\Models\VariantAttribute::where('attribute_name', 'Size')->pluck('attribute_value');
                    @endphp
            
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="form-group">
                            <label class="text-dark">Màu sắc</label>
                            <select class="form-control text-black border border-dark w-full" name="variants[{{ $index }}][color]">
                                <option value="">Chọn màu</option>
                                @foreach($allColors as $color)
                                    <option value="{{ $color }}" 
                                        {{ old('variants.' . $index . '.color', optional($variant->variantAttributeValues->where('variantAttribute.attribute_name', 'color')->first())->variantAttribute->attribute_value) == $color ? 'selected' : '' }}>
                                        {{ $color }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
            
                        <div class="form-group">
                            <label class="text-dark">Chọn kích cỡ</label>
                            <select class="form-control text-black border border-dark w-full" name="variants[{{ $index }}][size]">
                                <option value="">Chọn kích cỡ</option>
                                @foreach($allSizes as $size)
                                    <option value="{{ $size }}" 
                                        {{ old('variants.' . $index . '.size', optional($variant->variantAttributeValues->where('variantAttribute.attribute_name', 'size')->first())->variantAttribute->attribute_value) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            
                    <!-- Hình ảnh biến thể -->
                    <div class="form-group mb-4">
                        <label class="text-dark">Hình Ảnh Biến Thể</label>
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            @foreach($variant->images as $image)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $image->image_url) }}" class="w-20 h-20 object-cover rounded-lg border">
                                </div>
                            @endforeach
                        </div>
                        <input type="file" class="form-control mt-2 border border-dark w-full" name="variants[{{ $index }}][images][]" multiple>
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
    let variantIndex = {{ count($product->variants) }};

    const colors = @json($colors);
    const sizes = @json($sizes);

    /** 
     * Lấy danh sách biến thể đã có trong form 
     * Trả về danh sách các cặp { color, size } đã tồn tại
     */
     function getExistingVariants(ignoreElement = null) {
    let existingVariants = [];
    document.querySelectorAll('.variant').forEach(variant => {
        // Bỏ qua biến thể đang kiểm tra
        if (ignoreElement && variant === ignoreElement) return;

        let colorSelect = variant.querySelector('select[name*="[color]"]');
        let sizeSelect = variant.querySelector('select[name*="[size]"]');

        if (colorSelect && sizeSelect) {
            let color = colorSelect.value.trim().toLowerCase();
            let size = sizeSelect.value.trim().toLowerCase();

            // Chỉ thêm vào danh sách nếu có cả color và size
            if (color !== "" && size !== "") {
                existingVariants.push({ color, size });
            }
        }
    });

    return existingVariants;
}

/**
 * Kiểm tra xem biến thể mới có bị trùng lặp không
 * @param {HTMLElement} newVariant - Phần tử div chứa biến thể mới
 * @returns {boolean} - True nếu hợp lệ, False nếu bị trùng lặp
 */
function validateVariant(newVariant) {
    let colorSelect = newVariant.querySelector('select[name*="[color]"]');
    let sizeSelect = newVariant.querySelector('select[name*="[size]"]');
    let errorMsg = newVariant.querySelector('.size-error-msg');

    if (!colorSelect || !sizeSelect) return true; 

    let color = colorSelect.value.trim().toLowerCase();
    let size = sizeSelect.value.trim().toLowerCase();

    if (color && size) {
        let existingVariants = getExistingVariants(newVariant);  // ⬅️ Bỏ qua chính nó

        let isDuplicate = existingVariants.some(variant => 
            variant.color === color && variant.size === size
        );

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

    // Xử lý khi nhấn nút thêm biến thể
    addVariantButton.addEventListener('click', function () {
        const newVariant = document.createElement('div');
        newVariant.classList.add('variant', 'mt-3', 'border', 'p-4', 'rounded-lg', 'shadow-md');

        let colorOptions = colors.map(c => `<option value="${c.attribute_value}">${c.attribute_value}</option>`).join('');
        let sizeOptions = sizes.map(s => `<option value="${s.attribute_value}">${s.attribute_value}</option>`).join('');

        newVariant.innerHTML = `
            <div class="form-group">
                <label>Giá</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][price]">
            </div>
            <div class="form-group">
                <label>Giá Khuyến Mãi</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][price_sale]">
            </div>
            <div class="form-group">
                <label>Số lượng</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][stock]">
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
                <small class="text-danger size-error-msg" style="display: none;"></small>
            </div>
            <div class="form-group">
                <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể (Tối đa 5 ảnh)</label>
                <input type="file" class="form-control"
                       id="variant_images_${variantIndex}"
                       name="variants[${variantIndex}][images][]"
                       multiple accept="image/*">
                <div class="image-preview" id="image_preview_${variantIndex}"></div>
            </div>
            <button type="button" class="btn btn-danger remove-variant">Xóa Biến Thể</button>
        `;

        variantFieldsContainer.appendChild(newVariant);
        variantIndex++;

        let colorSelect = newVariant.querySelector('.variant-color');
        let sizeSelect = newVariant.querySelector('.variant-size');

        // Khi chọn màu hoặc kích cỡ, kiểm tra trùng lặp
        colorSelect.addEventListener('change', function () {
            validateVariant(newVariant);
        });

        sizeSelect.addEventListener('change', function () {
            validateVariant(newVariant);
        });

        newVariant.querySelector('.remove-variant').addEventListener('click', function () {
            newVariant.remove();
        });
    });

    // Xử lý xóa biến thể từ CSDL
    document.querySelectorAll(".delete-variant").forEach(button => {
        button.addEventListener("click", function () {
            if (!confirm("Bạn có chắc muốn xóa biến thể này không?")) return;
            const variantId = this.getAttribute("data-variant-id");
            const productId = this.getAttribute("data-product-id");
            if (!productId || !variantId) return alert("Có lỗi xảy ra. Product ID hoặc Variant ID không hợp lệ.");

            fetch(`/admin/products/${productId}/variants/${variantId}/delete`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
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
});

        </script>
        
        
        
        
        
        
        

    
    
        
    
    


@endsection