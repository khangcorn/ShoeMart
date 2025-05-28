@extends('admin.layout')

@section('content')
<div class="py-4 px-4">
    <h2 class="text-3xl font-bold mb-6">Danh sách danh mục</h2>  
           <div class="flex items-center justify-between">
         @if(auth()->user()->hasPermission('create_categories'))
        <a class="inline-block duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-2 text-sm font-medium text-white focus:ring-3 focus:outline-hidden" href="{{ route('categories.create') }}">Thêm mới danh mục</a>
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
                            echo '<td class="px-2 py-5 border border-gray-300 text-center">';
                            echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level); // Thụt đầu dòng
                            if ($level > 0) echo '↳ '; // Dấu mũi tên cho danh mục con
                            echo e($category->name).'</td>';

                            echo '<td class="px-2 py-5 border border-gray-300 text-center">';
                            if ($category->image_url) {
                                echo '<img src="'.asset('storage/'.$category->image_url).'" class="w-20 h-20 object-cover justify-center text-center items-center mx-auto ">';
                            } else {
                                echo '<span>No Image</span>';
                            }
                            echo '<td class="border border-gray-300  px-2 py-4 items-center text-center">';
echo '  <div class="flex justify-center items-center gap-2">';

// Nút edit
echo '<a href="'.route('categories.edit', $category->category_id).'" class="cursor-pointer text-sm p-1.5 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">';
echo '  <svg width="24px" height="24px" fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
echo '    <path fill-rule="evenodd" d="M14.8024118,6.44526791 L8.69610276,12.549589 C8.29095108,12.9079238 8.04030835,13.4092335 8,13.8678295 L8,16.0029438 L10.0639829,16.004826 C10.5982069,15.9670062 11.0954869,15.7183782 11.4947932,15.2616227 L17.556693,9.19972295 L14.8024118,6.44526791 Z M16.2168556,5.0312846 L18.9709065,7.78550938 L19.8647941,6.89162181 C19.9513987,6.80501747 20.0000526,6.68755666 20.0000526,6.56507948 C20.0000526,6.4426023 19.9513987,6.32514149 19.8647932,6.23853626 L17.7611243,4.13485646 C17.6754884,4.04854589 17.5589355,4 17.43735,4 C17.3157645,4 17.1992116,4.04854589 17.1135757,4.13485646 L16.2168556,5.0312846 Z M22,13 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,4 C2,2.8954305 2.8954305,2 4,2 L11,2 L11,4 L4,4 L4,20 L20,20 L20,13 L22,13 Z M17.43735,2 C18.0920882,2 18.7197259,2.26141978 19.1781068,2.7234227 L21.2790059,4.82432181 C21.7406843,5.28599904 22.0000526,5.91216845 22.0000526,6.56507948 C22.0000526,7.21799052 21.7406843,7.84415992 21.2790068,8.30583626 L12.9575072,16.6237545 C12.2590245,17.4294925 11.2689,17.9245308 10.1346,18.0023295 L6,18.0023295 L6,17.0023295 L6.00324765,13.7873015 C6.08843822,12.7328366 6.57866679,11.7523321 7.32649633,11.0934196 L15.6953877,2.72462818 C16.1563921,2.2608295 16.7833514,2 17.43735,2 Z"></path>';
echo '  </svg>';
echo '</a>';

// Nút delete
echo '<form action="'.route('categories.destroy', $category->category_id).'" method="POST" style="display:inline;">';
echo csrf_field().method_field('DELETE');
echo '  <button type="submit" class="cursor-pointer text-sm p-1.5 rounded-full bg-[#FEF3F2] text-[#D93948] flex items-center justify-center" onclick="return confirm(\'Are you sure you want to delete this category?\')">';
echo '    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
echo '      <path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
echo '    </svg>';
echo '  </button>';
echo '</form>';

echo '  </div>';
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
