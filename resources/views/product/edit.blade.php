@extends('layout')

@section('content')
<div class="container mx-auto p-6">
    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

        <div>
            <label for="category_id" class="block text-sm font-medium">Category</label>
            <select id="category_id" name="category_id" class="text-black mt-1 block w-full border rounded-lg p-2 @error('category_id') border-red-500 @enderror">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg">Update</button>
        <a href="{{ route('product_variants.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">Back</a>
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
