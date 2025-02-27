@extends('layout')

@section('content')
    <div class="py-4 px-4">

      <div class="flex items-center justify-between">
        <a href="{{ route('products.create') }}"
            class="inline-block duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-3 text-sm font-medium text-white focus:ring-3 focus:outline-hidden">
            New Product
        </a>
        <form method="GET" action="{{ route('products.index') }}" class="flex gap-2 mb-4">
          <input type="text" name="search" placeholder="Tìm kiếm..." value="{{ request()->search }}" class="border p-2 rounded">
          
          <select name="category" class="border p-2 rounded">
              <option value="">Tất cả danh mục</option>
              @foreach ($categories as $category)
                  <option value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                      {{ $category->name }}
                  </option>
              @endforeach
          </select>
      
          @if(request()->search)
          <span class="text-blue-600">Tìm kiếm: {{ request()->search }}</span>
      @endif
      
      @if(request()->category)
          <span class="text-blue-600">Danh mục: {{ $categories->find(request()->category)->name ?? '' }}</span>
      @endif
      
      <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'asc'])) }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
        Giá tăng dần
    </a>
    <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'desc'])) }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
        Giá giảm dần
    </a>
    
      
      
          <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
              Lọc
          </button>
      </form>
      
        <div class="relative inline-block text-left group">
            <button
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-2.5 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                <svg class="fill-white stroke-current dark:fill-gray-800" width="20" height="20" viewBox="0 0 20 20"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.29004 5.90393H17.7067" stroke="" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path d="M17.7075 14.0961H2.29085" stroke="" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path
                        d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z"
                        fill="" stroke="" stroke-width="1.5"></path>
                    <path
                        d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z"
                        fill="" stroke="" stroke-width="1.5"></path>
                </svg>
            </button>
    
            <div
                class="absolute right-0 z-10 w-40 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 hidden group-hover:block">
                <div class="py-1">
                    <a href="{{ route('products.index', ['sort' => 'asc']) }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Giá tăng dần
                    </a>
                    <a href="{{ route('products.index', ['sort' => 'desc']) }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Giá giảm dần
                    </a>
                </div>
            </div>
        </div>
    </div>
    
        <table class="w-full mt-4">
            <thead class="">
                <tr>
                    <th
                        class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">
                        #</th>
                    <th
                        class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">
                        Name</th>
                    <th
                        class="px-4 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">
                        <span class="text-gray-500">Old</span> / New Price</th>
                    <th
                        class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">
                        Stock</th>
                    <th
                        class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">
                        Thumbnail</th>
                    {{-- <th class="px-2 py-4 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Color</th> --}}
                    <th
                        class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">
                        Category</th>
                    <th
                        class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">
                        Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                      <td class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                        {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                    </td>
                    
                        <td class="border border-gray-300 dark:border-gray-700  px-2 py-5 items-center text-center">
                            {{ $product->name }}</td>
                        <td class="border relative border-gray-300 dark:border-gray-700 px-4 py-5 items-center text-center">
                            <span
                                class="line-through text-gray-500">{{ number_format($product->price, 0, ',', '.') }}</span>
                            /
                            {{ $product->price_sale ? number_format($product->price_sale, 0, ',', '.') : 'Không có' }} <span
                                class="underline">vnđ</span>
                            <p class="text-xs text-white bg-red-500 px-1.5 rounded-full absolute top-1 right-1">
                                @if ($product->price_sale && $product->price > 0)
                                    {{ round((($product->price - $product->price_sale) / $product->price) * 100, 2) }}%
                                @else
                                    N/A
                                @endif
                            </p>
                        </td>




                        <td class="border border-gray-300 dark:border-gray-700  px-2 py-5 items-center text-center">
                            {{ $product->stock }}</td>
                        <td class="border border-gray-300 dark:border-gray-700  px-2 py-5 items-center text-center">
                            <img src="{{ asset('./storage/images' . $product->image_url) }}" width="100">
                        </td>

                        {{-- <td class="border border-gray-300 dark:border-gray-700  px-2 py-4 items-center text-center">
                    @if ($product->variants->isNotEmpty())
                        @foreach ($product->variants as $variant)
                            @foreach ($variant->attributes as $attribute)
                                @if ($attribute->attribute_name == 'Color')
                                    {{ $attribute->attribute_value }}<br>
                                @endif 
                            @endforeach
                        @endforeach
                    @else
                        No color
                    @endif
                </td> --}}
                        <td class="border border-gray-300 dark:border-gray-700  px-2 py-4 items-center text-center">
                            {{ $product->category->name ?? 'Không có danh mục' }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-700  px-2 py-4 text-center  justify-center gap-2">

                            <a class="cursor-pointer text-sm px-2 font-semibold rounded-full bg-yellow-100 text-yellow-600"
                                href="{{ route('products.edit', $product->id) }}">Edit</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                style="display:inline;">
                                @csrf @method('DELETE')
                                <button
                                    class="cursor-pointer text-sm px-2 font-semibold rounded-full  bg-[#FEF3F2] text-[#D93948]"
                                    onclick="return confirm('Xóa sản phẩm này?')">Delete</button>
                            </form>
                            <a class="cursor-pointer text-sm px-2 font-semibold rounded-full bg-[#ECFDF3] text-[#03A27E]"
                                href="{{ route('products.edit', $product->id) }}">View</a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
       <div class="mt-4">
        <ol class="flex justify-center gap-1 text-xs font-medium">
          <!-- Nút Previous -->
          <li>
              <a href="{{ $products->previousPageUrl() }}" 
                 class="inline-flex  size-8 items-center justify-center rounded-lg border dark:text-white dark:bg-[#1F2937] border-gray-300 dark:border-gray-700 bg-white text-gray-900 {{ $products->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                  <span class="sr-only">Prev Page</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-3" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
              </a>
          </li>
      
          <!-- Hiển thị danh sách trang -->
          @foreach(range(1, $products->lastPage()) as $i)
              @if ($i == $products->currentPage())
                  <li class="block  size-8 rounded-lg border-blue-600 bg-blue-600 text-center leading-8 text-white">
                      {{ $i }}
                  </li>
              @else
                  <li>
                      <a href="{{ $products->url($i) }}" 
                         class="block  size-8 rounded-lg border dark:border-gray-700 dark:text-white dark:bg-[#1F2937] border-gray-300 bg-white text-center leading-8 text-gray-900 hover:bg-gray-200">
                          {{ $i }}
                      </a>
                  </li>
              @endif
          @endforeach
      
          <!-- Nút Next -->
          <li>
              <a href="{{ $products->nextPageUrl() }}" 
                 class="inline-flex  size-8 items-center justify-center rounded-lg dark:text-white border border-gray-300 dark:bg-[#1F2937] dark:border-gray-700 bg-white text-gray-900 {{ $products->hasMorePages() ? '' : 'opacity-50 pointer-events-none' }}">
                  <span class="sr-only">Next Page</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-3" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                  </svg>
              </a>
          </li>
      </ol>
       </div>
      
      
      
      
    </div>
@endsection
