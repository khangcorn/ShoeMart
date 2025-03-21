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
                    class="w-full p-2 border rounded-lg @error('name') border-red-500 @enderror" value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium">Mô tả</label>
                <input type="text" id="price" name="description"
                    class="w-full p-2 border rounded-lg @error('description') border-red-500 @enderror"
                    value="{{ old('description') }}">
                @error('description')
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
                <label class="block text-sm font-medium">Tổng Số Lượng</label>
                <p id="total_stock" class="font-bold text-lg">0</p>
                <input type="hidden" name="stock" id="total_stock_input">
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục</label>
                <select class="form-control" id="category_id" name="category_id">
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="product_images" class="block text-sm font-medium">Hình Ảnh Sản Phẩm Chính</label>
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
                newVariant.classList.add('variant', 'mt-3');

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

                    <div class="form-group">
                        <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể</label>
                        <input type="file" class="form-control" name="variants[${variantIndex}][images][]" multiple>
                    </div>

                    <button type="button" class="btn btn-danger" onclick="removeVariant(this)">Xóa Biến Thể</button>
                `;
                variantFieldsContainer.appendChild(newVariant);
                variantIndex++;
            });

            document.addEventListener('change', function (event) {
                if (event.target.matches('.size-checkbox')) {
                    const sizeInput = event.target.nextElementSibling.nextElementSibling;
                    sizeInput.disabled = !event.target.checked;
                    updateTotalStock();
                }
            });

            document.addEventListener('input', function (event) {
                if (event.target.matches('.size-stock')) {
                    updateTotalStock();
                }
            });
        });

        function updateTotalStock() {
            let totalStock = 0;
            document.querySelectorAll('.size-stock').forEach(input => {
                if (!input.disabled) {
                    totalStock += parseInt(input.value) || 0;
                }
            });
            document.getElementById('total_stock').innerText = totalStock;
            document.getElementById('total_stock_input').value = totalStock;
        }

        function removeVariant(button) {
            button.parentElement.remove();
            updateTotalStock();
        }
    </script>
@endsection