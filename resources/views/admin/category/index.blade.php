@extends('admin.layout')

@section('content')
<div class="py-4 px-4">
    <div class="flex items-center justify-between">
        <a class="inline-block duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-2 text-sm font-medium text-white focus:ring-3 focus:outline-hidden" href="{{ route('categories.create') }}">Add Category</a>
    </div>
    <div class="py-2"></div>
    <table class="w-full">
        <thead>
            <tr>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">#</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Category Name</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Image</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td class="px-2 py-5 border border-gray-300 dark:border-gray-700 text-center">{{ $category->category_id }}</td>
                <td class="px-2 py-5 border border-gray-300 dark:border-gray-700 text-center">{{ $category->name }}</td>
                <td class="px-2 py-5 border border-gray-300 dark:border-gray-700 text-center">
                    @if($category->image_url)
                        <img src="{{ asset('storage/'.$category->image_url) }}" alt="Category Image" class="w-20 h-20 object-cover">
                    @else
                        <span>No Image</span>
                    @endif
                </td>
                <td class="px-2 py-5 border border-gray-300 dark:border-gray-700 text-center">
                    <a href="{{ route('categories.edit', $category->category_id) }}" class="cursor-pointer text-sm px-2 font-semibold rounded-full bg-yellow-100 text-yellow-600">Edit</a>
                    
                    <form action="{{ route('categories.destroy', $category->category_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cursor-pointer text-sm px-2 font-semibold rounded-full bg-[#FEF3F2] text-[#D93948]" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
