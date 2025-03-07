@extends('admin.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6">Edit Category</h2>

    <form action="{{ route('categories.update', $category->category_id) }}" method="POST">
        @csrf @method('PUT')
        <div>
            <label for="name" class="block font-medium">Category Name</label>
            <input type="text" name="name" class="w-full p-2 border rounded" value="{{ $category->name }}" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
    </form>
</div>
@endsection
