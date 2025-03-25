@extends('admin.layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Thêm Sản Phẩm Mới và Biến Thể</h1>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Main Product Fields -->
            <div>
                <label for="name" class="block text-sm font-medium">Tên Sản Phẩm</label>
                <input type="text" id="name" name="name"
                    class="  text-black w-full p-2 border rounded-lg @error('name') border-red-500 @enderror"
                    value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium">Mô tả</label>
                <input type="text" id="price" name="description"
                    class="  text-black w-full p-2 border rounded-lg @error('description') border-red-500 @enderror"
                    value="{{ old('description') }}">
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="price" class="block text-sm font-medium">Giá</label>
                <input type="number" id="price" name="price"
                    class="  text-black w-full p-2 border rounded-lg @error('price') border-red-500 @enderror"
                    value="{{ old('price') }}">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price_sale" class="block text-sm font-medium">Giá Khuyến Mãi</label>
                <input type="number" id="price_sale" name="price_sale"
                    class="  text-black w-full p-2 border rounded-lg @error('price_sale') border-red-500 @enderror"
                    value="{{ old('price_sale') }}">
                @error('price_sale')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium"> Số Lượng</label>
                <input type="number" name="stock" id="total_stock_input">
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục</label>
                <select class="  text-black form-control" id="category_id" name="category_id">
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="product_images" class="  text-black block text-sm font-medium">Hình Ảnh Sản Phẩm Chính</label>
                <input type="file" id="product_images" name="product_images[]" multiple
                    class="w-full p-2 border rounded-lg @error('product_images') border-red-500 @enderror">
                @error('product_images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <!-- Variant Fields -->
            <div id="variant_fields"></div>
            <button type="button" class="btn btn-primary" id="add_variant_btn">Thêm Biến Thể</button>

            <button type="submit" class="btn btn-success mt-2">Lưu</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addVariantBtn = document.getElementById('add_variant_btn');
            const variantFieldsContainer = document.getElementById('variant_fields');
            let variantIndex = 0;
        
            addVariantBtn.addEventListener('click', function () {
                const newVariant = document.createElement('div');
                newVariant.classList.add('variant', 'mt-3', 'border', 'p-3', 'rounded-lg', 'shadow-md');
        
                newVariant.innerHTML =  `
                    <div class="form-group">
                        <label>Giá</label>
                        <input type="number" class="form-control variant-price" name="variants[${variantIndex}][price]" value="">
                    </div>
                    <div class="form-group">
                        <label>Giá Khuyến Mãi</label>
                        <input type="number" class="form-control variant-sale-price" name="variants[${variantIndex}][price_sale]" value="">
                    </div>
                    <div class="form-group">
                        <label>Số lượng</label>
                        <input type="number" class="form-control variant-stock" name="variants[${variantIndex}][stock]" value="0">
                    </div>
                    <div class="form-group">
                        <label>Màu sắc</label>
                        <select class="form-control variant-color" name="variants[${variantIndex}][color]">
                            <option value="">Chọn màu</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->attribute_value }}">{{ $color->attribute_value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kích cỡ</label>
                        <select class="form-control variant-size" name="variants[${variantIndex}][size]">
                            <option value="">Chọn kích cỡ</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->attribute_value }}">{{ $size->attribute_value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
    <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể (Tối đa 5 ảnh)</label>
    <input type="file" class="form-control variant-image-input" 
        id="variant_images_${variantIndex}" 
        name="variants[${variantIndex}][images][]" multiple accept="image/*">
    <div class="image-preview" id="image_preview_${variantIndex}"></div>
</div>
                    <p class="text-danger error-message d-none" style="display: none;">⚠️ Biến thể với Màu và Size này đã tồn tại!</p>
                    <button type="button" class="btn btn-danger mt-2 remove-variant">Xóa Biến Thể</button>
            `;
        
                // Thêm vào danh sách biến thể
                variantFieldsContainer.appendChild(newVariant);
                variantIndex++;
        
                // 🔥 Ẩn thông báo lỗi bằng cả d-none và display: none
                const errorMsg = newVariant.querySelector('.error-message');
                errorMsg.classList.add('d-none');
                errorMsg.style.display = "none";
        
                // Gán sự kiện kiểm tra khi chọn Màu hoặc Size
                const colorSelect = newVariant.querySelector('.variant-color');
                const sizeSelect = newVariant.querySelector('.variant-size');
        
                colorSelect.addEventListener('change', function () {
                    checkDuplicateVariant();
                });
        
                sizeSelect.addEventListener('change', function () {
                    checkDuplicateVariant();
                });
        
                // Gán sự kiện xóa biến thể
                newVariant.querySelector('.remove-variant').addEventListener('click', function () {
                    newVariant.remove();
                    checkDuplicateVariant(); // Cập nhật lại danh sách sau khi xóa
                });
            });
        
            function checkDuplicateVariant() {
                let existingVariants = new Set();
        
                document.querySelectorAll('.variant').forEach(variant => {
                    const color = variant.querySelector('.variant-color').value;
                    const size = variant.querySelector('.variant-size').value;
                    const errorMsg = variant.querySelector('.error-message');
        
                    // 🔥 Ẩn lỗi mặc định trước khi kiểm tra
                    errorMsg.classList.add('d-none');
                    errorMsg.style.display = "none";
        
                    // Chỉ kiểm tra nếu đã chọn cả Màu & Size
                    if (color && size) {
                        const key = `${color}-${size}`; 
                        if (existingVariants.has(key)) {
                            errorMsg.classList.remove('d-none'); // Hiển thị lỗi nếu trùng
                            errorMsg.style.display = "block";
                        } else {
                            existingVariants.add(key);
                        }
                    }
                });
            }
        
            // Kiểm tra lần cuối trước khi submit form
            document.querySelector('form').addEventListener('submit', function (event) {
                if (document.querySelector('.error-message:not(.d-none)')) {
                    event.preventDefault(); // 🔥 Chặn form submit nếu có lỗi
                    alert('Có biến thể bị trùng Color & Size. Hãy kiểm tra lại!');
                }
            });
        });
        </script>
@endsection