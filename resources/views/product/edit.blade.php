@extends('layout')

@section('content')
<div class="container">
    <h2>Chỉnh sửa sản phẩm</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="form-group">
            <label for="name">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <div class="form-group">
            <label for="price">Mô tả</label>
            <input type="text" name="description" class="form-control" value="{{ $product->description }}" required>
        </div>
        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
        </div>
        <div class="form-group">
            <label for="price">Giá khuyến mại</label>
            <input type="number" name="price_sale" class="form-control" value="{{ $product->price_sale }}" required>
        </div>
        <div class="form-group">
            <label for="price">Stock</label>
            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
        </div>

        <div class="form-group">
            <label for="category_id">Danh mục</label>
            <select name="category_id" class="form-control">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Cập nhật</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
    </form>
</div>
@endsection
