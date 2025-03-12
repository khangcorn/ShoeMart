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
                    class="w-full p-2 border rounded-lg @error('name') border-red-500 @enderror"
                    value="{{ old('name', $product->name) }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div>
                    <label for="price" class="block text-sm font-medium">Giá</label>
                    <input type="number" id="price" name="price"
                        class="w-full p-2 border rounded-lg @error('price') border-red-500 @enderror"
                        value="{{ old('price', $product->price) }}">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
    
                <div>
                    <label for="price_sale" class="block text-sm font-medium">Giá Khuyến Mãi</label>
                    <input type="number" id="price_sale" name="price_sale"
                        class="w-full p-2 border rounded-lg @error('price_sale') border-red-500 @enderror"
                        value="{{ old('price_sale', $product->price_sale) }}">
                    @error('price_sale')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
    
    
                <div class="form-group">
                    <label for="category_id">Danh Mục</label>
                    <select class="form-control" id="category_id" name="category_id">
                        <option value="">Chọn Danh Mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ $category->category_id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
    
    
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
                        <input type="number" class="form-control" name="variants[{{ $index }}][price]" value="{{ $variant->price }}">
                    </div>
            
                    <div class="form-group">
                        <label>Giá Khuyến Mãi</label>
                        <input type="number" class="form-control" name="variants[{{ $index }}][price_sale]" value="{{ $variant->price_sale }}">
                    </div>
                    <div class="form-group">
                        <label>Màu Sắc</label>
                        <select class="form-control" name="variants[{{ $index }}][color]">
                            <option value="">Chọn Màu</option>
                            @foreach (['Trắng', 'Đen', 'Xanh', 'Đỏ', 'Vàng'] as $color)
                                <option value="{{ $color }}" 
                                    {{ $variant->variantAttributeValues->where('attribute_id', 1)->where('attribute_value', $color)->isNotEmpty() ? 'selected' : '' }}>
                                    {{ $color }}
                                </option>
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
                                    <input type="number" class="size-stock p-1 border rounded-lg" 
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
                                    <img src="{{ asset($image->image_url) }}" class="w-full h-20 object-cover rounded-lg border">
                                    <input type="checkbox" name="variants[{{ $index }}][remove_images][]" value="{{ $image->image_id }}"> Xóa
                                </div>
                            @endforeach
                        </div>
                        <input type="file" class="form-control mt-2" name="variants[{{ $index }}][images][]" multiple>
                    </div>
            
                    <button type="button" class="btn btn-danger" onclick="removeVariant(this)">Xóa Biến Thể</button>
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
        // Kích hoạt hoặc vô hiệu hóa input số lượng khi checkbox size được chọn
        document.querySelectorAll('.size-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                let input = this.closest('div').querySelector('.size-stock');
                input.disabled = !this.checked;
            });
        });

        // Xóa biến thể
        function removeVariant(button) {
            button.closest('.variant').remove();
        }

        // Thêm biến thể mới
        document.getElementById('add_variant_button').addEventListener('click', function() {
            const newVariant = document.createElement('div');
            newVariant.classList.add('variant', 'mt-3', 'border', 'p-4', 'rounded-lg', 'shadow-md');
            newVariant.innerHTML = `
    <div class="form-group">
        <label>Giá</label>
        <input type="number" class="form-control" name="variants[][price]" value="">
    </div>
`;

            document.getElementById('variant_fields').appendChild(newVariant);
        });
    </script>
@endsection
