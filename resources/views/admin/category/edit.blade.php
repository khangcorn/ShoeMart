@extends('admin.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6">Chỉnh sửa danh mục</h2>

    <form action="{{ route('categories.update', $category->category_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Tên danh mục -->
        <div class="space-y-2 ">
            <label for="name" class="block font-medium">Tên danh mục</label>
            <input type="text" name="name" class=" text-black w-full p-2 border rounded @error('name') border-red-500 @enderror" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="space-y-2 mt-4">
            <label for="name" class="block font-medium">Mô tả danh mục</label>
            <input type="text" name="description" class=" text-black w-full p-2 border rounded @error('description') border-red-500 @enderror" value="{{ old('description', $category->description) }}" required>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Chọn danh mục cha -->
        <div class="mt-4 space-y-2">
            <label for="parent_id" class="block font-medium">Danh mục cha</label>
            <select name="parent_id" class=" text-black w-full p-2 border rounded @error('parent_id') border-red-500 @enderror">
                <option value="">Chọn danh mục cha </option>
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
        <div class="mt-4 space-y-2">
            <label for="image_url" class="block font-medium">Ảnh danh mục</label>
            <input type="file" name="image_url" class="w-full p-2 border rounded @error('image_url') border-red-500 @enderror">
            @error('image_url')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <!-- Hiển thị ảnh hiện tại nếu có -->
            @if($category->image_url)
                <div class="mt-2">
                    <p>Ảnh danh mục hiện tại:</p>
                    <img src="{{ asset('storage/' . $category->image_url) }}" alt="Category Image" class="w-30 h-30 object-cover mt-2">
                </div>
            @endif
        </div>

       <div class="space-x-2">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Cập nhật</button>
        <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded mt-4">Quay lại</a>
       </div>
    </form>
</div>
@endsection
