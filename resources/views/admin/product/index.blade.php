@extends('admin.layout')

@section('content')
    @if (session('success'))
        <div id="error-messages" class="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div id="alert-error" class="alert alert-danger"
            style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 25px; border-radius: 5px; background-color: #f44336; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            {{ session('error') }}
        </div>

        <script>
            // Ẩn thông báo sau 5 giây (5000ms)
            setTimeout(() => {
                const alert = document.getElementById('alert-error');
                if (alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500); // Xóa phần tử sau khi mờ dần
                }
            }, 5000);
        </script>
    @endif

    <style>
        /* CSS nếu cần thêm */
        /* Đảm bảo thông báo xuất hiện ở đầu trang */
        .mb-4 {
            position: fixed;
            top: 10px;
            /* Khoảng cách từ trên */
            left: 50%;
            transform: translateX(-50%);
            /* Căn giữa thông báo */
            z-index: 9999;
            /* Đảm bảo thông báo luôn nằm trên các phần tử khác */
            width: 80%;
            /* Độ rộng của thông báo */
            max-width: 600px;
            /* Giới hạn độ rộng */
        }

        #button {
            position: absolute;
            top: 10px;
            /* Điều chỉnh khoảng cách từ trên */
            right: 10px;
            /* Điều chỉnh khoảng cách từ bên phải */
            background: transparent;
            /* Đảm bảo nút không có nền */
            border: none;
            /* Bỏ đường viền */
            font-size: 20px;
            /* Điều chỉnh kích thước icon */
            cursor: pointer;
            /* Thêm con trỏ chuột khi hover */
        }

        .success-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            background-color: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            max-width: 90%;
            width: 500px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeInSlideDown 0.5s ease-out;
        }

        /* Nút đóng */
        .success-alert .close-btn {
            background: none;
            border: none;
            color: #065f46;
            font-size: 22px;
            cursor: pointer;
            margin-left: 16px;
            line-height: 1;
            transition: color 0.2s;
        }

        .success-alert .close-btn:hover {
            color: #034732;
        }

        /* Hiệu ứng xuất hiện */
        @keyframes fadeInSlideDown {
            0% {
                opacity: 0;
                transform: translate(-50%, -20px);
            }

            100% {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
    </style>

    <div class="py-4 px-4">
        <h2 class="text-3xl font-bold mb-6">Danh sách sản phẩm </h2> 
        <div class="flex  items-center justify-between">
            @if (auth()->user()->hasPermission('create_products'))
                <a href="{{ route('products.create') }}"
                    class="inline-block duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-2 text-sm font-medium text-white focus:ring-3 focus:outline-hidden">
                   Thêm mới sản phẩm
                </a>
            @endif




            <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">

                <div>
                    <div class="relative text-gray-700">
                        <input type="text" name="search" placeholder="Tìm kiếm sản phẩm" value="{{ request()->search }}"
                            class=" border  w-40 text-xs  border-gray-300 px-2 py-2 rounded-md">
                        <button type="submit" class=" absolute top-2 right-3 "> <svg
                                class="  fill-gray-500 dark:fill-gray-300" width="18" height="18" viewBox="0 0 20 20"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                                    fill=""></path>
                            </svg></button>
                    </div>
                </div>
                <section class="w-40  bg-white border border-gray-300 rounded-md ">
                    <div class="">
                        <select name="category" class="block w-full px-2 py-2 rounded-md text-xs text-gray-700 "
                            onchange="this.form.submit()">
                            <option value="">Sắp xếp theo danh mục</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request()->category == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </section>

                <section class="w-40 border border-gray-300  bg-white rounded-md     ">
                    <div class="">
                        <select class="block w-full px-2 py-2 text-xs text-gray-700 rounded-md "
                            onchange="window.location.href=this.value">
                            <option value="" selected disabled>Sắp xếp theo giá tiền</option>
                            <option
                                value="{{ route('products.index', array_merge(request()->query(), ['sort' => 'asc'])) }}">
                                Giá giảm dần</option>
                            <option
                                value="{{ route('products.index', array_merge(request()->query(), ['sort' => 'desc'])) }}">
                                Giá tăng dần</option>
                        </select>
                    </div>
                </section>






            </form>





        </div>
        <div class="py-2 flex items-center gap-2">
            @if (request()->search)
                <div class="items-center flex gap-2">
                    <p
                        class="flex items-center gap-2 text-black px-2 py-1 border border-gray-300   rounded-full">
                        Keyword: {{ request()->search }}
                        <svg class="w-5 h-5 p-1 cursor-pointer rounded-full border-red-500 border" viewBox="-0.5 0 25 25"
                            fill="none" xmlns="http://www.w3.org/2000/svg"
                            onclick="window.location.href='{{ route('products.index', request()->except('search')) }}'">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M3 21.32L21 3.32001" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path d="M3 3.32001L21 21.32" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </g>
                        </svg>
                    </p>
                </div>
            @endif

            @if (request()->category)
                <div class="items-center flex gap-2">
                    <p
                        class="flex items-center gap-2 text-black px-2 py-1 border border-gray-300   rounded-full">
                        Danh mục:
                        {{ $categories->find(request()->category)->name ?? '' }}
                        <svg class="w-5 h-5 p-1 cursor-pointer rounded-full border-red-500 border" viewBox="-0.5 0 25 25"
                            fill="none" xmlns="http://www.w3.org/2000/svg"
                            onclick="window.location.href='{{ route('products.index', request()->except('category')) }}'">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M3 21.32L21 3.32001" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path d="M3 3.32001L21 21.32" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </g>
                        </svg>
                    </p>
                </div>
            @endif

            @if (request()->sort)
                <div class="items-center flex gap-2">
                    <p
                        class="flex items-center gap-2 text-black px-2 py-1 border border-gray-300   rounded-full">
                        Giá:
                        {{ request()->sort == 'asc' ? 'Price Decrease' : 'Price Increase' }}
                        <svg class="w-5 h-5 p-1 cursor-pointer rounded-full border-red-500 border" viewBox="-0.5 0 25 25"
                            fill="none" xmlns="http://www.w3.org/2000/svg"
                            onclick="window.location.href='{{ route('products.index', request()->except('sort')) }}'">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M3 21.32L21 3.32001" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path d="M3 3.32001L21 21.32" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
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
                        class="px-2 py-5 border border-gray-300  items-center text-center font-semibold">
                        #</th>
                    <th
                        class="px-2 py-5 border border-gray-300  items-center text-center font-semibold">
                        Tên sản phẩm</th>
                    <th
                        class="px-4 py-5 border border-gray-300  items-center text-center font-semibold">
                        <span class="text-gray-500">Giá gốc</span> / Giá giảm
                    </th>
                    <th
                        class="px-2 py-5 border border-gray-300  items-center text-center font-semibold">
                        Số lượng</th>
                    <th
                        class="px-2 py-5 border border-gray-300  items-center text-center font-semibold">
                        Ảnh </th>
                    {{-- <th class="px-2 py-4 border border-gray-300  items-center text-center font-semibold">Color</th> --}}
                    <th
                        class="px-2 py-5 border border-gray-300  items-center text-center font-semibold">
                        Danh mục</th>
                    <th
                        class="px-2 py-5 border border-gray-300  items-center text-center font-semibold">
                        Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td
                            class="font-semibold border border-gray-300  px-2 py-5 items-center text-center">
                            {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                        </td>

                        <td class="border border-gray-300  px-2 py-5 items-center text-center">
                            {{ $product->name }}
                        </td>
                        <td
                            class="border relative border-gray-300  px-4 py-5 items-center text-center">
                            @php
                                $hasVariants = $product->variants && $product->variants->count() > 0;

                                if ($hasVariants) {
                                    $lowestVariant = $product->variants
                                        ->sortBy(function ($variant) {
                                            return $variant->price_sale > 0 ? $variant->price_sale : $variant->price;
                                        })
                                        ->first();

                                    $originalPrice = $lowestVariant->price;
                                    $salePrice = $lowestVariant->price_sale;
                                } else {
                                    $originalPrice = $product->price;
                                    $salePrice = $product->price_sale;
                                }
                            @endphp

                            @if ($salePrice && $salePrice > 0)
                                <span
                                    class="line-through text-gray-500">{{ number_format($originalPrice, 0, ',', '.') }}</span>
                                /
                                {{ number_format($salePrice, 0, ',', '.') }}
                                <span class="underline">đ</span>
                                <p class="text-[11px] text-white bg-red-500 px-1 rounded-full absolute top-1 right-1">
                                    {{ round((($originalPrice - $salePrice) / $originalPrice) * 100, 2) }}%
                                </p>
                            @else
                                {{ number_format($originalPrice, 0, ',', '.') }}
                                <span class="underline">đ</span>
                            @endif
                        </td>







                        <td class="border border-gray-300   px-2 py-5 items-center text-center">
                            {{ $product->stock }}</td>
                        <td class="border border-gray-300  px-2 py-5 items-center text-center">
                            @if ($product->mainImage)
                                <img src="{{ asset('storage/' . $product->mainImage->image_url) }}" width="100">
                            @else
                                <span class="text-gray-500">Không có ảnh</span>
                            @endif
                        </td>

                        <td class="border border-gray-300   px-2 py-4 items-center text-center">
                            {{ $product->category->name ?? 'Không có danh mục' }}</td>
                        <td
                            class="border border-gray-300   px-2 py-4 text-center items-center   justify-center gap-2">
                            <div class="flex items-center justify-center gap-2">
                            @if (auth()->user()->hasPermission('edit_products'))
                            <a
                            class="cursor-pointer text-sm p-1.5 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center"
                            href="{{ route('products.edit', $product->product_id) }}"
                          >
                            <svg width="24px" height="24px" fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" d="M14.8024118,6.44526791 L8.69610276,12.549589 C8.29095108,12.9079238 8.04030835,13.4092335 8,13.8678295 L8,16.0029438 L10.0639829,16.004826 C10.5982069,15.9670062 11.0954869,15.7183782 11.4947932,15.2616227 L17.556693,9.19972295 L14.8024118,6.44526791 Z M16.2168556,5.0312846 L18.9709065,7.78550938 L19.8647941,6.89162181 C19.9513987,6.80501747 20.0000526,6.68755666 20.0000526,6.56507948 C20.0000526,6.4426023 19.9513987,6.32514149 19.8647932,6.23853626 L17.7611243,4.13485646 C17.6754884,4.04854589 17.5589355,4 17.43735,4 C17.3157645,4 17.1992116,4.04854589 17.1135757,4.13485646 L16.2168556,5.0312846 Z M22,13 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,4 C2,2.8954305 2.8954305,2 4,2 L11,2 L11,4 L4,4 L4,20 L20,20 L20,13 L22,13 Z M17.43735,2 C18.0920882,2 18.7197259,2.26141978 19.1781068,2.7234227 L21.2790059,4.82432181 C21.7406843,5.28599904 22.0000526,5.91216845 22.0000526,6.56507948 C22.0000526,7.21799052 21.7406843,7.84415992 21.2790068,8.30583626 L12.9575072,16.6237545 C12.2590245,17.4294925 11.2689,17.9245308 10.1346,18.0023295 L6,18.0023295 L6,17.0023295 L6.00324765,13.7873015 C6.08843822,12.7328366 6.57866679,11.7523321 7.32649633,11.0934196 L15.6953877,2.72462818 C16.1563921,2.2608295 16.7833514,2 17.43735,2 Z"></path> </g>
                            </svg>
                          </a>
                      
                            @endif
                            @if (auth()->user()->hasPermission('delete_products'))
                            <form
                            action="{{ route('products.destroy', $product->product_id) }}"
                            method="POST"
                            onsubmit="return confirm('Xóa sản phẩm này?')"
                          >
                            @csrf
                            @method('DELETE')
                            <button
                              type="submit"
                              class="cursor-pointer text-sm p-1.5 rounded-full bg-[#FEF3F2] text-[#D93948] flex items-center justify-center"
                            >
                              <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="1"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g>
                              </svg>
                            </button>
                          </form>
                            @endif
                            <a
                            class="cursor-pointer text-sm p-1.5 rounded-full bg-[#ECFDF3] text-[#03A27E] flex items-center justify-center"
                            href="{{ route('products.show', ['product' => $product->product_id]) }}"
                          >
                            <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                <g transform="translate(1, 4)" fill="#000000">
                                    <path d="M20.92,7.6 C18.9,2.91 15.1,0 11,0 C6.9,0 3.1,2.91 1.08,7.6 C0.968686852,7.85505046 0.968686852,8.14494954 1.08,8.4 C3.1,13.09 6.9,16 11,16 C15.1,16 18.9,13.09 20.92,8.4 C21.0313131,8.14494954 21.0313131,7.85505046 20.92,7.6 Z M11,14 C7.83,14 4.83,11.71 3.1,8 C4.83,4.29 7.83,2 11,2 C14.17,2 17.17,4.29 18.9,8 C17.17,11.71 14.17,14 11,14 Z M11,4 C8.790861,4 7,5.790861 7,8 C7,10.209139 8.790861,12 11,12 C13.209139,12 15,10.209139 15,8 C15,6.93913404 14.5785726,5.92171839 13.8284271,5.17157288 C13.0782816,4.42142736 12.060866,4 11,4 Z M11,10 C9.8954305,10 9,9.1045695 9,8 C9,6.8954305 9.8954305,6 11,6 C12.1045695,6 13,6.8954305 13,8 C13,9.1045695 12.1045695,10 11,10 Z" />
                                  </g>
                            </svg>
                          </a>

                          </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
  <div class="mt-6 flex justify-center">
        {{ $products->links('pagination::tailwind') }}
    </div>




    </div>
    <script>
        // Kiểm tra trong localStorage xem có thông báo không
        const successMessage = localStorage.getItem('success_message');
        if (successMessage) {
            // Tạo phần tử thông báo
            const messageContainer = document.createElement('div');
            messageContainer.classList.add('mb-4', 'bg-green-100', 'border', 'border-green-300', 'text-green-700', 'px-4',
                'py-3', 'rounded', 'relative');
            messageContainer.textContent = successMessage;

            // Tạo nút đóng thông báo
            const closeButton = document.createElement('button');
            closeButton.innerHTML = '&times;';
            closeButton.classList.add('absolute', 'top-2', 'right-2', 'text-green-700', 'hover:text-green-900');
            closeButton.onclick = () => messageContainer.remove();

            // Thêm nút vào phần tử thông báo
            messageContainer.appendChild(closeButton);

            // Thêm thông báo vào body hoặc phần tử thích hợp
            document.body.appendChild(messageContainer);

            // Xóa thông báo khỏi localStorage sau khi hiển thị
            localStorage.removeItem('success_message');
        }
        if (document.getElementById('error-messages')) {
            setTimeout(function() {
                document.getElementById('error-messages').style.display = 'none';
            }, 5000); // 5000ms = 5 giây
        }
    </script>
@endsection
