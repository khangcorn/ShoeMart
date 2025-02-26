@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Thêm Sản Phẩm Mới</h1>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-medium">Tên Sản Phẩm</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" 
                    class="w-full p-2 border rounded @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block font-medium">Mô Tả</label>
                <textarea id="description" name="description" 
                    class="w-full p-2 border rounded @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block font-medium">Giá</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" 
                    class="w-full p-2 border rounded @error('price') border-red-500 @enderror">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price_sale" class="block font-medium">Giá Khuyến Mãi</label>
                <input type="number" id="price_sale" name="price_sale" value="{{ old('price_sale') }}" 
                    class="w-full p-2 border rounded @error('price_sale') border-red-500 @enderror">
                @error('price_sale')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="block font-medium">Số Lượng</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}" 
                    class="w-full p-2 border rounded @error('stock') border-red-500 @enderror">
                @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block font-medium">Danh Mục</label>
                <select id="category_id" name="category_id" 
                    class="w-full p-2 border rounded @error('category_id') border-red-500 @enderror">
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Lưu</button>
                <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Quay lại</a>
            </div>
        </form>
    </div>
@endsection
