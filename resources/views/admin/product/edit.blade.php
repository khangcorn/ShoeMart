@extends('admin.layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Chỉnh Sửa Sản Phẩm</h1>

        <form action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
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
                                    <img src="{{ asset($image->image_url) }}" class="w-20 h-20 object-cover rounded-lg border">
                                    <button type="button" class="absolute top-0 right-0 bg-red-500 text-white p-1 text-xs rounded remove-image" data-image-id="{{ $image->image_id }}">X</button>
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

                    <div class="form-group">
                        <label>Giá</label>
                        <input type="number" class="  text-black form-control" name="variants[{{ $index }}][price]" value="{{ $variant->price }}">
                    </div>
            
                    <div class="form-group">
                        <label>Giá Khuyến Mãi</label>
                        <input type="number" class="  text-black form-control" name="variants[{{ $index }}][price_sale]" value="{{ $variant->price_sale }}">
                    </div>
            
                    <div class="form-group">
                        <label>Màu Sắc</label>
                        <select class="  text-black form-control" name="variants[{{ $index }}][color]">
                            <option value="">Chọn Màu</option>
                            @foreach (['Trắng', 'Đen', 'Xanh', 'Đỏ', 'Vàng'] as $color)
                                <option value="{{ $color }}" 
                                    {{ $variant->variantAttributeValues->where('attribute_id', 1)->where('attribute_value', $color)->isNotEmpty() ? 'selected' : '' }}>{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="form-group">
                        <label>Kích Thước & Số Lượng</label>
                        <div class="size-options grid grid-cols-3 gap-2">
                            @foreach([39, 40, 41, 42, 43] as $size)
                                @php
                                    $sizeStock = $variant->variantAttributeValues->where('attribute_value', $size)->first()->stock ?? 0;
                                @endphp
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" name="variants[{{ $index }}][sizes][]" value="{{ $size }}" class="size-checkbox" 
                                        {{ $sizeStock > 0 ? 'checked' : '' }}>
                                    <label>{{ $size }}</label>
                                    <input type="number" class="  text-black size-stock p-1 border rounded-lg" 
                                        name="variants[{{ $index }}][size_stock][{{ $size }}]" 
                                        value="{{ $sizeStock }}" min="0" {{ $sizeStock > 0 ? '' : 'disabled' }}>
                                </div>
                            @endforeach
                        </div>
                    </div>
            
                    <div class="form-group">
                        <label>Hình Ảnh Biến Thể</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($variant->images as $image)
                                <div class="relative">
                                    <img src="{{ asset('' . $image->image_url) }}" alt="Hình ảnh biến thể" width="100">
                                </div>
                            @endforeach
                        </div>
                        <input type="file" class="form-control mt-2" name="variants[{{ $index }}][images][]" multiple>
                    </div>

                    <button type="button" class="btn btn-danger delete-variant" data-variant-id="{{ $variant->variant_id }}" data-product-id="{{ $product->product_id }}">Xóa Biến Thể</button>

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
    let variantIndex = {{ count($product->variants) }}; // Khởi tạo index từ số lượng biến thể hiện tại
    
    // Define productId
    const productId = {{ $product->product_id }}; // Assuming $product is available in the Blade view

    // Lắng nghe sự kiện nhấn nút "Thêm Biến Thể"
    addVariantButton.addEventListener('click', function () {
        const newVariant = document.createElement('div');
        newVariant.classList.add('variant', 'mt-3', 'border', 'p-4', 'rounded-lg', 'shadow-md');
    
        newVariant.innerHTML = `
            <!-- Giá -->
            <div class="form-group">
                <label>Giá</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][price]" value="">
            </div>

            <!-- Giá Khuyến Mãi -->
            <div class="form-group">
                <label>Giá Khuyến Mãi</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][price_sale]" value="">
            </div>

            <!-- Màu Sắc -->
            <div class="form-group">
                <label>Màu Sắc</label>
                <select class="form-control" name="variants[${variantIndex}][color]">
                    <option value="">Chọn Màu</option>
                    <option value="Trắng">Trắng</option>
                    <option value="Đen">Đen</option>
                    <option value="Xanh">Xanh</option>
                    <option value="Đỏ">Đỏ</option>
                    <option value="Vàng">Vàng</option>
                </select>
            </div>

            <!-- Kích Thước -->
            <div class="form-group">
                <label>Kích Thước</label>
                <div class="size-options">
                    ${[39, 40, 41, 42, 43].map(size => `
                        <div>
                            <input type="checkbox" name="variants[${variantIndex}][sizes][]" value="${size}" class="size-checkbox">
                            <label>${size}</label>
                            <input type="number" class="size-stock" name="variants[${variantIndex}][size_stock][${size}]" value="0" min="0" disabled>
                        </div>
                    `).join('')}
                </div>
            </div>

            <!-- Hình Ảnh Biến Thể -->
            <div class="form-group">
                <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể</label>
                <input type="file" class="form-control" name="variants[${variantIndex}][images][]" multiple>
            </div>

            <!-- Nút Xóa Biến Thể -->
            <button type="button" class="btn btn-danger" onclick="removeVariant(this)">Xóa Biến Thể</button>
        `;

        // Thêm biến thể mới vào container
        variantFieldsContainer.appendChild(newVariant);
        variantIndex++;
    });

    // Xử lý thay đổi checkbox Kích Thước
    document.addEventListener('change', function (event) {
        if (event.target.matches('.size-checkbox')) {
            const sizeInput = event.target.closest('div').querySelector('.size-stock');
            sizeInput.disabled = !event.target.checked;
        }
    });

    // Define the removeVariant function
    window.removeVariant = function(button) {
        const variantElement = button.closest('.variant');
        variantElement.remove();
    };

    // Handle the delete button click event for removing variants
    document.querySelectorAll(".delete-variant").forEach(button => {
        button.addEventListener("click", function () {
            const variantId = this.getAttribute("data-variant-id");
            const productId = this.getAttribute("data-product-id"); // Lấy productId từ data attribute
            
            // Kiểm tra nếu productId không hợp lệ
            if (!productId || !variantId) {
                alert("Có lỗi xảy ra. Product ID hoặc Variant ID không hợp lệ.");
                return;
            }

            // Kiểm tra xác nhận xóa
            if (!confirm("Bạn có chắc muốn xóa biến thể này không?")) return;

            // Gửi yêu cầu xóa qua AJAX với CSRF token
            fetch(`/admin/products/${productId}/variants/${variantId}/delete`, {
                method: "POST", // Laravel không hỗ trợ DELETE tốt => dùng POST
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json",
                }
            })
            .then(response => response.json()) // Chuyển response thành JSON
            .then(data => {
                if (data.success) {
                    alert("✅ " + data.message);

                    // Tải lại trang sau khi xóa thành công
                    location.reload(); // Reload lại trang để biến thể biến mất
                } else {
                    alert("❌ " + data.message);
                }
            })
            .catch(error => console.error("Lỗi khi xóa biến thể:", error));
        });
    });
});

    </script>
    
    
@endsection
