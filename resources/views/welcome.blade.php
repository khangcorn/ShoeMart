
@extends('layout')

@section('content')
    <div class="container">
        <h1>Thêm Sản Phẩm Mới</h1>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Tên Sản Phẩm</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="description">Mô Tả</label>
                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="price">Giá</label>
                <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
            </div>

            <div class="form-group">
                <label for="price_sale">Giá Khuyến Mãi</label>
                <input type="number" class="form-control" id="price_sale" name="price_sale" value="{{ old('price_sale') }}">
            </div>

            <div class="form-group">
                <label for="stock">Số Lượng</label>
                <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock') }}" required>
            </div>
            <div class="form-group">
                <label for="images">Hình Ảnh</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple>
                @error('images')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="variants">Biến Thể</label>
                <div id="variants">
                    <div class="variant">
                        <div class="form-group">
                            <label for="variant_price">Giá</label>
                            <input type="number" class="form-control" name="variants[0][price]" required>
                        </div>
                        <div class="form-group">
                            <label for="variant_price_sale">Giá Khuyến Mãi</label>
                            <input type="number" class="form-control" name="variants[0][price_sale]">
                        </div>
                        <div class="form-group">
                            <label for="variant_stock">Số Lượng</label>
                            <input type="number" class="form-control" name="variants[0][stock]" required>
                        </div>
                        <div class="form-group">
                            @endsection

 