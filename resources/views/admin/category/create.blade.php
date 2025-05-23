@extends('admin.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6">Thêm mới danh mục</h2>

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div class="space-y-2">
            <label for="name" class="block font-medium">Tên danh mục</label>
            <input type="text" name="name" class="w-full p-2 border rounded @error('name') border-red-500 @enderror" value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="parent_id" class="block font-medium">Danh mục cha</label>
            <select name="parent_id" class="w-full p-2 border rounded @error('parent_id') border-red-500 @enderror">
                <option value="">Chọn danh mục cha</option>
                @foreach($categories as $category)
                    <!-- Chỉ cho phép chọn danh mục cha nếu nó không phải là danh mục con -->
                    @if($category->parent_id == null)
                        <option value="{{ $category->category_id }}" {{ old('parent_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endif
                @endforeach
            </select>
            @error('parent_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="description" class="block font-medium">Mô tả</label>
            <textarea name="description" class="w-full p-2 border rounded @error('description') border-red-500 @enderror" rows="4">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="image_url" class="block font-medium">Ảnh danh mục</label>
            <input type="file" name="image_url" class="w-full p-2 border rounded @error('image_url') border-red-500 @enderror">
            @error('image_url')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

       <div class="space-x-2">
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Lưu</button>
        <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Quay lại</a>
       </div>
    </form>
</div>
@endsection
