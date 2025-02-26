@extends('layout')

@section('content')
    <div class="container">
        <h1>Thêm Biến Thể Sản Phẩm</h1>

        <form action="{{ route('product_variants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="product_id">Sản phẩm</label>
                <select class="form-control @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                    <option value="">Chọn Sản Phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="variants">
                <div class="variant">
                    <div class="form-group">
                        <label for="variant_price">Giá</label>
                        <input type="number" class="form-control @error('variants.0.price') is-invalid @enderror" name="variants[0][price]" value="{{ old('variants.0.price') }}">
                        @error('variants.0.price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="variant_price_sale">Giá Khuyến Mãi</label>
                        <input type="number" class="form-control @error('variants.0.price_sale') is-invalid @enderror" name="variants[0][price_sale]" value="{{ old('variants.0.price_sale') }}">
                        @error('variants.0.price_sale')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                 

                    <div class="form-group">
                        <label for="variant_stock">Số Lượng</label>
                        <input type="number" class="form-control @error('variants.0.stock') is-invalid @enderror" name="variants[0][stock]" value="{{ old('variants.0.stock') }}">
                        @error('variants.0.stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="attribute_name">Tên Biến Thể</label>
                        <input type="text" class="form-control @error('variants.0.attributes.0.name') is-invalid @enderror" name="variants[0][attributes][0][name]" value="{{ old('variants.0.attributes.0.name') }}">
                        @error('variants.0.attributes.0.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="attribute_value">Giá trị Biến Thể</label>
                        <input type="text" class="form-control @error('variants.0.attributes.0.value') is-invalid @enderror" name="variants[0][attributes][0][value]" value="{{ old('variants.0.attributes.0.value') }}">
                        @error('variants.0.attributes.0.value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="variant_images">Hình Ảnh Biến Thể</label>
                        <input type="file" class="form-control @error('variants.0.images') is-invalid @enderror" name="variants[0][images][]" multiple>
                        @error('variants.0.images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success mt-2">Lưu</button>
            <a href="{{ route('product_variants.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>
@endsection
