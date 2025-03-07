@extends('admin.layout')

@section('content')
    <div class="container">
        <form action="{{ route('products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

        <!-- Product Fields -->
        <div>
            <label for="name" class="block text-sm font-medium">Product Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="text-black mt-1 block w-full border rounded-lg p-2 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium">Description</label>
            <textarea id="description" name="description" class="text-black mt-1 block w-full border rounded-lg p-2 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price" class="block text-sm font-medium">Price</label>
            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" class="text-black mt-1 block w-full border rounded-lg p-2 @error('price') border-red-500 @enderror">
            @error('price')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price_sale" class="block text-sm font-medium">Sale Price</label>
            <input type="number" id="price_sale" name="price_sale" value="{{ old('price_sale', $product->price_sale) }}" class="text-black mt-1 block w-full border rounded-lg p-2 @error('price_sale') border-red-500 @enderror">
            @error('price_sale')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock" class="block text-sm font-medium">Stock</label>
            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" class="text-black mt-1 block w-full border rounded-lg p-2 @error('stock') border-red-500 @enderror">
            @error('stock')
                <p class="text-red-500 text-sm">{{ $message }}</p>
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
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>



            <button type="submit" class="btn btn-success mt-2">Cập nhật</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>

        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg">Update</button>
        <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">Back</a>
    </form>
    <script>
        tinymce.init({
            selector: '#description',
            plugins: 'advlist autolink lists link image charmap print preview anchor',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat',
            menubar: false,
            height: 300
        });
    </script>
</div>
@endsection
