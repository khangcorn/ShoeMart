@extends('admin.layout')

@section('content')
<div class="py-4 px-4">
    <div  class="flex  items-center justify-between">
        <a class="inline-block duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-2 text-sm font-medium text-white focus:ring-3 focus:outline-hidden" href="{{ route('categories.create') }}">Add Category</a>

    </div>
    <div class="py-2">

    </div>
    <table class=" w-full">
        <thead>
            <tr>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">#</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Category Name</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Action</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->category_id }}</td>
                <td>{{ $category->name }}</td>
                <td>
                    <a href="{{ route('categories.edit', $category->category_id) }}" class="btn btn-warning">Sửa</a>
                    <form action="{{ route('categories.destroy', $category->category_id) }}" method="POST" style="display:inline;">
                <td class="border border-gray-300 dark:border-gray-700  px-2 py-5 items-center text-center">{{ $category->id }}</td>
                <td class="border border-gray-300 dark:border-gray-700  px-2 py-5 items-center text-center">{{ $category->name }}</td>
                <td class="border border-gray-300 dark:border-gray-700  px-2 py-5 items-center text-center">
                    <a  class="cursor-pointer text-sm px-2 font-semibold rounded-full bg-yellow-100 text-yellow-600" href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">Edit</a>

                        @csrf @method('DELETE')
                        <button  class="cursor-pointer text-sm px-2 font-semibold rounded-full  bg-[#FEF3F2] text-[#D93948]" onclick="return confirm('Xóa danh mục này?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
