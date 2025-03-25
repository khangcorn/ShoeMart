@extends('admin.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6">Add New Category</h2>

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block font-medium">Category Name</label>
            <input type="text" name="name" class="w-full p-2 border rounded @error('name') border-red-500 @enderror" value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="parent_id" class="block font-medium">Parent Category</label>
            <select name="parent_id" class="w-full p-2 border rounded @error('parent_id') border-red-500 @enderror">
                <option value="">Select Parent Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" {{ old('parent_id') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="image_url" class="block font-medium">Category Image</label>
            <input type="file" name="image_url" class="w-full p-2 border rounded @error('image_url') border-red-500 @enderror">
            @error('image_url')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
        <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
    </form>
</div>
@endsection
