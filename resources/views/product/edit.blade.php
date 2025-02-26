@extends('layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Chỉnh sửa sản phẩm</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        
        <div>
            <label for="name" class="block font-medium">Tên sản phẩm</label>
            <input type="text" name="name" class="w-full p-2 border rounded" value="{{ $product->name }}" required>
        </div>

        <div>
            <label for="description" class="block font-medium">Mô tả</label>
            <input type="text" name="description" class="w-full p-2 border rounded" value="{{ $product->description }}" required>
        </div>
        <div>
            <label for="price" class="block font-medium">Giá</label>
            <input type="number" name="price" class="w-full p-2 border rounded" value="{{ $product->price }}" required>
        </div>
        <div>
            <label for="price_sale" class="block font-medium">Giá khuyến mại</label>
            <input type="number" name="price_sale" class="w-full p-2 border rounded" value="{{ $product->price_sale }}" required>
        </div>
        <div>
            <label for="stock" class="block font-medium">Stock</label>
            <input type="number" name="stock" class="w-full p-2 border rounded" value="{{ $product->stock }}" required>
        </div>

        <div>
            <label for="category_id" class="block font-medium">Danh mục</label>
            <select name="category_id" class="w-full p-2 border rounded">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Cập nhật</button>
            <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Quay lại</a>
        </div>
    </form>
</div>
@endsection
