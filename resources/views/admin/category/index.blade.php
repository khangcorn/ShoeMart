@extends('admin.layout')

@section('content')
<div class="py-4 px-4">

    <div class="flex items-center justify-between">
         @if(auth()->user()->hasPermission('create_categories'))
        <a class="inline-block duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-2 text-sm font-medium text-white focus:ring-3 focus:outline-hidden" href="{{ route('categories.create') }}">Add Category</a>
        @endif
    </div>

    <div class="py-2"></div>

    <table class="w-full">
        <thead>
            <tr>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">#</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Category Name</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Image</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                function renderCategoryTree($categories, $parentId = null, $level = 0) {
                    foreach ($categories as $category) {
                        if ($category->parent_id == $parentId) {
                            echo '<tr>';
                            echo '<td class="px-2 py-5 border border-gray-300 text-center">'.$category->category_id.'</td>';
                            echo '<td class="px-2 py-5 border border-gray-300 text-left">';
                            echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level); // Thụt đầu dòng
                            if ($level > 0) echo '↳ '; // Dấu mũi tên cho danh mục con
                            echo e($category->name).'</td>';

                            echo '<td class="px-2 py-5 border border-gray-300 text-center">';
                            if ($category->image_url) {
                                echo '<img src="'.asset('storage/'.$category->image_url).'" class="w-20 h-20 object-cover">';
                            } else {
                                echo '<span>No Image</span>';
                            }
                            echo '</td>';

                            echo '<td class="px-2 py-5 border border-gray-300 text-center">';
                            if (auth()->user()->hasPermission('edit_categories')) {
                                echo '<a href="'.route('categories.edit', $category->category_id).'" class="cursor-pointer text-sm px-2 font-semibold rounded-full bg-yellow-100 text-yellow-600">Edit</a> ';
                            }
                            if (auth()->user()->hasPermission('delete_categories')) {
                                echo '<form action="'.route('categories.destroy', $category->category_id).'" method="POST" style="display:inline;">'
                                    .csrf_field()
                                    .method_field('DELETE');
                                echo '<button type="submit" class="cursor-pointer text-sm px-2 font-semibold rounded-full bg-[#FEF3F2] text-[#D93948]" onclick="return confirm(\'Are you sure you want to delete this category?\')">Delete</button>';
                                echo '</form>';
                            }
                            echo '</td>';
                            echo '</tr>';

                            // Đệ quy để hiển thị danh mục con
                            renderCategoryTree($categories, $category->category_id, $level + 1);
                        }
                    }
                }
            @endphp

            @php renderCategoryTree($categories); @endphp
        </tbody>
    </table>
</div>
@endsection
