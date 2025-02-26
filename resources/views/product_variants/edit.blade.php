@extends('layout')

@section('content')
    <div class="container">
        <form action="{{ route('product_variants.update', $variant->variant_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="product_id">Sản phẩm</label>
                <select class="form-control" id="product_id" name="product_id" required disabled>
                    <option value="">Chọn Sản Phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id', $variant->product_id) == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="variants">
                <div class="variant">
                    <hr>
                    <div class="form-group">
                        <label for="variant_price">Giá</label>
                        <input type="number" class="form-control" name="variants[0][price]" value="{{ old('variants.0.price', $variant->price) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="variant_price_sale">Giá Khuyến Mãi</label>
                        <input type="number" class="form-control" name="variants[0][price_sale]" value="{{ old('variants.0.price_sale', $variant->price_sale) }}">
                    </div>
                    <div class="form-group">
                        <label for="variant_stock">Số Lượng</label>
                        <input type="number" class="form-control" name="variants[0][stock]" value="{{ old('variants.0.stock', $variant->stock) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="attribute_name">Tên biến thể</label>
                        <input type="text" class="form-control" name="variants[0][attributes][0][name]" value="{{ old('variants.0.attributes.0.name', optional($variant->attributes->where('attribute_name', 'color')->first())->attribute_name ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="attribute_value">Giá trị biến thể</label>
                        <input type="text" class="form-control" name="variants[0][attributes][0][value]" value="{{ old('variants.0.attributes.0.value', optional($variant->attributes->where('attribute_name', 'color')->first())->attribute_value ?? '') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="variant_images">Hình Ảnh Biến Thể</label>
                        <input type="file" class="form-control" name="variants[0][images][]" multiple>
                        @if($variant->images)
                            <div class="mt-2">
                                @foreach($variant->images as $image)
                                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="Hình ảnh biến thể" width="100">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success mt-2">Cập nhật</button>
            <a href="{{ route('product_variants.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>
@endsection
