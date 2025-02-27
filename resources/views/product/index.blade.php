@extends('layout')

@section('content')
    <div class="mt-2 px-4">

        <div class="flex items-center justify-between">
            <a href="{{ route('products.create') }}"
                class="inline-block duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-2 text-sm font-medium text-white focus:ring-3 focus:outline-hidden">
                New Product
            </a>


            <div class="relative inline-block text-left group">

                <div class="flex items-center gap-2">
                    <form method="GET" action="{{ route('products.index') }}" class="flex gap-2 mb-4">

                        <div>
                            <input type="text" name="search" placeholder="Search product" value="{{ request()->search }}"
                                class="border p-2 rounded">
                            <button type="submit" class="bg-blue-500 text-white rounded-r-full">Tìm kiếm</button>
                        </div>
                        <section class="w-40  bg-white rounded-md shadow-sm ring-1 ring-black ring-opacity-5">
                            <div class="px-1 py-1">
                                <select name="category" class="block w-full px-4 py-2 text-xs text-gray-700 "
                                    onchange="this.form.submit()">
                                    <option value="">Sort by Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request()->category == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </section>

                        <section class="w-40  bg-white rounded-md shadow-sm ring-1 ring-black ring-opacity-5 ">
                            <div class="px-1 py-1">
                                <select class="block w-full px-4 py-2 text-xs text-gray-700 "
                                    onchange="window.location.href=this.value">
                                    <option value="" selected disabled>Sort by Price</option>
                                    <option
                                        value="{{ route('products.index', array_merge(request()->query(), ['sort' => 'asc'])) }}">
                                        Price Decrease</option>
                                    <option
                                        value="{{ route('products.index', array_merge(request()->query(), ['sort' => 'desc'])) }}">
                                        Price Increase</option>
                                </select>
                            </div>
                        </section>






                    </form>



                </div>
            </div>
        </div>
        <div class="py-2 flex items-center gap-2">
          @if (request()->search)
          <div class="items-center flex gap-2">
            <p class="flex items-center gap-2 text-black px-2 py-1 border border-black rounded-full">Keyword: {{ request()->search }}
              <svg class="w-5 h-5 p-1 cursor-pointer rounded-full border-red-500 border" viewBox="-0.5 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="window.location.href='{{ route('products.index', request()->except('search')) }}'">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                  <path d="M3 21.32L21 3.32001" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M3 3.32001L21 21.32" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </g>
              </svg>
            </p>
          </div>
          @endif

            @if (request()->category)
            <div class="items-center flex gap-2">
              <p class="flex items-center gap-2 text-black px-2 py-1 border border-black rounded-full">Category:
                {{ $categories->find(request()->category)->name ?? '' }}
                <svg class="w-5 h-5 p-1 cursor-pointer rounded-full border-red-500 border" viewBox="-0.5 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="window.location.href='{{ route('products.index', request()->except('category')) }}'">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                    <path d="M3 21.32L21 3.32001" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M3 3.32001L21 21.32" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
                </svg>
              </p>
            </div>
            @endif

            @if (request()->sort)
            <div class="items-center flex gap-2">
              <p class="flex items-center gap-2 text-black px-2 py-1 border border-black rounded-full">Price:
                {{ request()->sort == 'asc' ? 'Price Decrease' : 'Price Increase' }}
                <svg class="w-5 h-5 p-1 cursor-pointer rounded-full border-red-500 border" viewBox="-0.5 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="window.location.href='{{ route('products.index', request()->except('sort')) }}'">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                    <path d="M3 21.32L21 3.32001" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M3 3.32001L21 21.32" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
                </svg>
              </p>
            </div>
            @endif

        </div>
        <table class="w-full">
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
                        <span class="text-gray-500">Old</span> / New Price
                    </th>
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
                        <td
                            class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                            {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                        </td>

                        <td class="border border-gray-300 dark:border-gray-700  px-2 py-5 items-center text-center">
                            {{ $product->name }}</td>
                        <td class="border relative border-gray-300 dark:border-gray-700 px-4 py-5 items-center text-center">
                            <span
                                class="line-through text-gray-500">{{ number_format($product->price, 0, ',', '.') }}</span>
                            /
                            {{ $product->price_sale ? number_format($product->price_sale, 0, ',', '.') : 'Không có' }}
                            <span class="underline">vnđ</span>
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
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </li>

                <!-- Hiển thị danh sách trang -->
                @foreach (range(1, $products->lastPage()) as $i)
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
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </li>
            </ol>
        </div>




    </div>
@endsection
