@extends('layout')

@section('content')
    <div class="container">
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
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
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Variant Fields -->
            <div id="variants">
                @foreach ($product->variants as $index => $variant)
                    <div class="variant mt-3">
                        <hr>
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
                                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Hình ảnh biến thể" width="100">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-success mt-2">Cập nhật</button>
            <a href="{{ route('product_variants.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>
@endsection
