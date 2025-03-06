@extends('layout')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Thêm Sản Phẩm Mới và Biến Thể</h1>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium">Tên Sản Phẩm</label>
            <input type="text" id="name" name="name" class="w-full p-2 border rounded-lg @error('name') border-red-500 @enderror" value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

       

        <div>
            <label for="price" class="block text-sm font-medium">Giá</label>
            <input type="number" id="price" name="price" class="w-full p-2 border rounded-lg @error('price') border-red-500 @enderror" value="{{ old('price') }}">
            @error('price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price_sale" class="block text-sm font-medium">Giá Khuyến Mãi</label>
            <input type="number" id="price_sale" name="price_sale" class="w-full p-2 border rounded-lg @error('price_sale') border-red-500 @enderror" value="{{ old('price_sale') }}">
            @error('price_sale')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock" class="block text-sm font-medium">Số Lượng</label>
            <input type="number" id="stock" name="stock" class="w-full p-2 border rounded-lg @error('stock') border-red-500 @enderror" value="{{ old('stock') }}">
            @error('stock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium">Danh Mục</label>
            <select id="category_id" name="category_id" class="w-full p-2 border rounded-lg @error('category_id') border-red-500 @enderror">
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
        <div>
            <label for="description" class="block text-sm font-medium">Mô Tả</label>
            <textarea id="description" name="description" class="w-full p-2 border rounded-lg">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div id="variant_fields"></div>
        <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded-lg" id="add_variant_btn">Thêm Biến Thể</button>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Lưu</button>
        <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Quay lại</a>
    </form>
</div>


<script src="https://cdn.tiny.cloud/1/1phku9urb4xfwze6015t4jh6zj41vrde6zmb5pb76yty0zdp/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#description',
        plugins: 'advlist autolink lists link image charmap print preview anchor',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat',
        menubar: false,
        height: 300
    });
</script>
@endsection
