@extends('admin.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6">Edit Category</h2>

    <form action="{{ route('categories.update', $category->category_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Tên danh mục -->
        <div>
            <label for="name" class="block font-medium">Category Name</label>
            <input type="text" name="name" class="w-full p-2 border rounded @error('name') border-red-500 @enderror" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="name" class="block font-medium">Description</label>
            <input type="text" name="name" class="w-full p-2 border rounded @error('name') border-red-500 @enderror" value="{{ old('name', $category->description) }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Chọn danh mục cha -->
        <div class="mt-4">
            <label for="parent_id" class="block font-medium">Parent Category</label>
            <select name="parent_id" class="w-full p-2 border rounded @error('parent_id') border-red-500 @enderror">
                <option value="">Select Parent Category (Optional)</option>
                @foreach($categories as $parentCategory)
                    <option value="{{ $parentCategory->category_id }}" 
                        {{ $parentCategory->category_id == old('parent_id', $category->parent_id) ? 'selected' : '' }}>
                        {{ $parentCategory->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ảnh danh mục -->
        <div class="mt-4">
            <label for="image_url" class="block font-medium">Category Image</label>
            <input type="file" name="image_url" class="w-full p-2 border rounded @error('image_url') border-red-500 @enderror">
            @error('image_url')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <!-- Hiển thị ảnh hiện tại nếu có -->
            @if($category->image_url)
                <div class="mt-2">
                    <p>Current Image:</p>
                    <img src="{{ asset('storage/' . $category->image_url) }}" alt="Category Image" class="w-40 h-40 object-cover mt-2">
                </div>
            @endif
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Update</button>
        <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded mt-4">Back</a>
    </form>
</div>
@endsection
