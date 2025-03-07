@extends('admin.layout')

@section('content')
    <div class="container">
        <form action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Product Fields -->
            <div class="form-group">
                <label for="name">Tên Sản Phẩm</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Mô Tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="price">Giá</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}">
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="price_sale">Giá Khuyến Mãi</label>
                <input type="number" class="form-control @error('price_sale') is-invalid @enderror" id="price_sale" name="price_sale" value="{{ old('price_sale', $product->price_sale) }}">
                @error('price_sale')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="stock">Số Lượng</label>
                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}">
                @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục</label>
                <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Variant Fields -->
            <!-- Variant Fields -->
            <div id="variants">
                @foreach ($product->variants as $index => $variant)
                    <div class="variant mt-3">
                        <hr>
                        
                        <!-- Ẩn ID biến thể để gửi lên request -->
                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->variant_id }}">
            
                        <div class="form-group">
                            <label for="variant_price_{{ $index }}">Giá</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][price]" value="{{ old('variants.' . $index . '.price', $variant->price) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="variant_price_sale_{{ $index }}">Giá Khuyến Mãi</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][price_sale]" value="{{ old('variants.' . $index . '.price_sale', $variant->price_sale) }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="variant_stock_{{ $index }}">Số Lượng</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][stock]" value="{{ old('variants.' . $index . '.stock', $variant->stock) }}" required>
                        </div>
            
                        <!-- Attribute Fields -->
                        @foreach ($variant->attributes as $attributeIndex => $attribute)
                            <div class="form-group">
                                <label for="attribute_name_{{ $index }}_{{ $attributeIndex }}">Tên biến thể</label>
                                <input type="text" class="form-control" name="variants[{{ $index }}][attributes][{{ $attributeIndex }}][name]" value="{{ old('variants.' . $index . '.attributes.' . $attributeIndex . '.name', $attribute->attribute_name) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="attribute_value_{{ $index }}_{{ $attributeIndex }}">Giá trị biến thể</label>
                                <input type="text" class="form-control" name="variants[{{ $index }}][attributes][{{ $attributeIndex }}][value]" value="{{ old('variants.' . $index . '.attributes.' . $attributeIndex . '.value', $attribute->attribute_value) }}" required>
                            </div>
                        @endforeach
            
                        <!-- Variant Images -->
                        <div class="form-group">
                            <label for="variant_images_{{ $index }}">Hình Ảnh Biến Thể</label>
                            <input type="file" class="form-control" name="variants[{{ $index }}][images][]" multiple>
                            @if ($variant->images->isNotEmpty())
                            <div class="mt-2">
                                @foreach ($variant->images as $image)
                                    <img src="{{ asset('storage/' . ltrim($image->image_url, '/storage/')) }}" alt="Hình ảnh biến thể" width="100">
                                @endforeach
                            </div>
                        @endif
                        
                        </div>
            
                        <!-- Delete Button -->
                        <div class="form-group">
                            <button type="button" class="btn btn-danger delete-variant" data-index="{{ $index }}">Xóa Biến Thể</button>
                            <input type="hidden" name="variants[{{ $index }}][delete]" value="0">
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button type="button" class="btn btn-primary mt-2" id="add-variant">Thêm Biến Thể</button>


            <button type="submit" class="btn btn-success mt-2">Cập nhật</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle delete button click
            document.querySelectorAll('.delete-variant').forEach(button => {
                button.addEventListener('click', function () {
                    const index = this.getAttribute('data-index');
                    const hiddenInput = document.querySelector(`input[name="variants[${index}][delete]"]`);
                    if (hiddenInput) {
                        hiddenInput.value = '1'; // Mark this variant for deletion
                        const variantDiv = this.closest('.variant');
                        variantDiv.style.display = 'none'; // Hide the variant from the form
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let variantIndex = {{ $product->variants->count() }}; // Đếm số biến thể có sẵn
    
            // Xóa biến thể
            document.querySelectorAll('.delete-variant').forEach(button => {
                button.addEventListener('click', function () {
                    const index = this.getAttribute('data-index');
                    const hiddenInput = document.querySelector(`input[name="variants[${index}][delete]"]`);
                    if (hiddenInput) {
                        hiddenInput.value = '1';
                        this.closest('.variant').style.display = 'none';
                    }
                });
            });
    
            // Thêm biến thể mới
            document.getElementById('add-variant').addEventListener('click', function () {
                const variantsContainer = document.getElementById('variants');
    
                // Tạo HTML cho biến thể mới
                const variantHtml = `
                    <div class="variant mt-3">
                        <hr>
                        <input type="hidden" name="variants[${variantIndex}][id]" value="">
                        <div class="form-group">
                            <label for="variant_price_${variantIndex}">Giá</label>
                            <input type="number" class="form-control" name="variants[${variantIndex}][price]" required>
                        </div>
                        <div class="form-group">
                            <label for="variant_price_sale_${variantIndex}">Giá Khuyến Mãi</label>
                            <input type="number" class="form-control" name="variants[${variantIndex}][price_sale]">
                        </div>
                        <div class="form-group">
                            <label for="variant_stock_${variantIndex}">Số Lượng</label>
                            <input type="number" class="form-control" name="variants[${variantIndex}][stock]" required>
                        </div>
                         <div class="form-group">
                        <label for="attribute_name_${variantIndex}">Tên Biến Thể</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][name]" value="">
                    </div>
                    <div class="form-group">
                        <label for="attribute_value_${variantIndex}">Giá trị Biến Thể</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][value]" value="">
                    </div>
                        
                        <div class="form-group">
                            <label>Hình Ảnh Biến Thể</label>
                            <input type="file" class="form-control" name="variants[${variantIndex}][images][]" multiple>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-danger delete-variant" data-index="${variantIndex}">Xóa Biến Thể</button>
                            <input type="hidden" name="variants[${variantIndex}][delete]" value="0">
                        </div>
                    </div>`;
    
                // Chèn HTML vào danh sách biến thể
                variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
    
                // Gán sự kiện "Xóa" cho biến thể mới
                document.querySelector(`.delete-variant[data-index="${variantIndex}"]`).addEventListener('click', function () {
                    this.closest('.variant').remove();
                });
    
                variantIndex++; // Tăng index để tránh trùng lặp
            });
        });
    </script>
    
@endsection
