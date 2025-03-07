@extends('admin.layout')

@section('content')
<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-6">Thêm Sản Phẩm Mới và Biến Thể</h1>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block font-medium">Tên Sản Phẩm</label>
            <input type="text" id="name" name="name" class="w-full p-2 border rounded text-black @error('name') border-red-500 @enderror" value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

       

        <div>
            <label for="price" class="block font-medium">Giá</label>
            <input type="number" id="price" name="price" class="w-full p-2 border rounded text-black @error('price') border-red-500 @enderror" value="{{ old('price') }}">
            @error('price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price_sale" class="block font-medium">Giá Khuyến Mãi</label>
            <input type="number" id="price_sale" name="price_sale" class="w-full p-2 border rounded text-black @error('price_sale') border-red-500 @enderror" value="{{ old('price_sale') }}">
            @error('price_sale')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock" class="block font-medium">Số Lượng</label>
            <input type="number" id="stock" name="stock" class="w-full p-2 border rounded text-black @error('stock') border-red-500 @enderror" value="{{ old('stock') }}">
            @error('stock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
            <div class="form-group">
                <label for="category_id">Danh Mục</label>
                <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                    <option value="">Chọn Danh Mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Variant Fields -->
            <div id="variant_fields"></div> <!-- Thêm container để chứa các nhóm biến thể -->
            <button type="button" class="btn btn-primary" id="add_variant_btn">Thêm Biến Thể</button>

            <button type="submit" class="btn btn-success mt-2">Lưu</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addVariantBtn = document.getElementById('add_variant_btn');
            const variantFieldsContainer = document.getElementById('variant_fields');
            let variantIndex = 0;  // Biến đếm số lượng biến thể đã thêm
            
            addVariantBtn.addEventListener('click', function() {
                // Tạo nhóm biến thể mới
                const newVariant = document.createElement('div');
                newVariant.classList.add('variant', 'mt-3');

                // Tạo HTML cho nhóm biến thể mới
                newVariant.innerHTML = `
                    <div class="form-group">
                        <label for="variant_price_${variantIndex}">Giá</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][price]" value="">
                    </div>
                    <div class="form-group">
                        <label for="variant_price_sale_${variantIndex}">Giá Khuyến Mãi</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][price_sale]" value="">
                    </div>
                    <div class="form-group">
                        <label for="variant_stock_${variantIndex}">Số Lượng</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][stock]" value="">
                    </div>
                    <div class="form-group">
                        <label for="attribute_name_${variantIndex}">Tên Biến Thể</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][name]" value="">
                    </div>
                    <div class="form-group">
                        <label for="attribute_value_${variantIndex}">Giá trị Biến Thể</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][value]" value="">
                    </div>
                 <div class="form-group">
    <label for="variant_images_${variantIndex}">Hình Ảnh Biến Thể</label>
    <input type="file" class="form-control variant-images" name="variants[${variantIndex}][images][]" multiple data-index="${variantIndex}">
    <div id="variant_images_preview_${variantIndex}" class="mt-2"></div>

        <div>
            <label for="category_id" class="block font-medium">Danh Mục</label>
            <select id="category_id" name="category_id" class="w-full p-2 border rounded text-black @error('category_id') border-red-500 @enderror">
                <option value="">Chọn Danh Mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="description" class="block font-medium">Mô Tả</label>
            <textarea id="description" name="description" class="w-full p-2 border rounded text-black @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div id="variant_fields"></div>
        <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded" id="add_variant_btn">Thêm Biến Thể</button>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Lưu</button>
        <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Quay lại</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addVariantBtn = document.getElementById('add_variant_btn');
        const variantFieldsContainer = document.getElementById('variant_fields');
        let variantIndex = 0;

        addVariantBtn.addEventListener('click', function() {
            const newVariant = document.createElement('div');
            newVariant.classList.add('space-y-4', 'mt-4', 'p-4', 'border', 'rounded');
            newVariant.innerHTML = `
                <div>
                    <label class="block font-medium">Giá</label>
                    <input type="number" name="variants[${variantIndex}][price]" class="w-full p-2 border rounded text-black">
                </div>
                <div>
                    <label class="block font-medium">Tên Biến Thể</label>
                    <input type="text" name="variants[${variantIndex}][attributes][0][name]" class="w-full p-2 border rounded text-black">
                </div>
                <div>
                    <label class="block font-medium">Hình Ảnh</label>
                    <input type="file" name="variants[${variantIndex}][images][]" multiple class="w-full p-2 border rounded text-black">
                </div>
            `;
            variantFieldsContainer.appendChild(newVariant);
            variantIndex++;
        });
    });
</script>
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
