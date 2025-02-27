@extends('layout')

@section('content')
    <div class="container">
        <h1>Thêm Sản Phẩm Mới và Biến Thể</h1>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Product Fields -->
            <div class="form-group">
                <label for="name">Tên Sản Phẩm</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Mô Tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="price">Giá</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}">
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="price_sale">Giá Khuyến Mãi</label>
                <input type="number" class="form-control @error('price_sale') is-invalid @enderror" id="price_sale" name="price_sale" value="{{ old('price_sale') }}">
                @error('price_sale')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="stock">Số Lượng</label>
                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}">
                @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục</label>
                <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Variant Fields -->
            <div id="variant_fields"></div> <!-- Thêm container để chứa các nhóm biến thể -->
            <button type="button" class="btn btn-primary" id="add_variant_btn">Thêm Biến Thể</button>

            <button type="submit" class="btn btn-success mt-2">Lưu</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addVariantBtn = document.getElementById('add_variant_btn');
            const variantFieldsContainer = document.getElementById('variant_fields');
            let variantIndex = 0;  // Biến đếm số lượng biến thể đã thêm
            
            addVariantBtn.addEventListener('click', function() {
                // Tạo nhóm biến thể mới
                const newVariant = document.createElement('div');
                newVariant.classList.add('variant', 'mt-3');

                // Tạo HTML cho nhóm biến thể mới
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
                    <div class="form-group">
                        <label for="attribute_name_${variantIndex}">Tên Biến Thể</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][name]" value="">
                    </div>
                    <div class="form-group">
                        <label for="attribute_value_${variantIndex}">Giá trị Biến Thể</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][value]" value="">
                    </div>
                      <div class="form-group">
                        <label for="variant_images">Hình Ảnh Biến Thể</label>
                        <input type="file" class="form-control @error('variants.0.images') is-invalid @enderror" name="variants[0][images][]" multiple>
                        @error('variants.0.images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                `;
                
                // Thêm nhóm biến thể mới vào trong form
                variantFieldsContainer.appendChild(newVariant);

                // Tăng biến đếm để tạo chỉ số mới cho biến thể tiếp theo
                variantIndex++;
            });
        });
    </script>
@endsection
