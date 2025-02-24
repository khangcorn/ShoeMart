@extends('layout')

@section('content')
<div class="container px-4">
    <h2>Product List</h2>
    <a href="{{ route('products.create') }}" class="inline-block transition duration-300 rounded-lg border border-indigo-600 bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-transparent hover:text-indigo-600 focus:ring-3 focus:outline-hidden">Upload New Product</a>
    
    <table class="w-full mt-3">
        <thead class="">
            <tr>
                <th class="px-2 py-4 border border-gray-300 items-center text-center" >#</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center" >Name</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Description</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Old Price</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">New Price</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Discount</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Stock</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Image</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Color</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Category</th>
                <th class="px-2 py-4 border border-gray-300 items-center text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">{{ $product->id }}</td>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">{{ $product->name }}</td>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">{{ $product->description }}</td>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">{{ $product->price }}</td>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">{{ $product->price_sale ?? 'Không có' }}</td>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">
                    @if($product->price_sale && $product->price > 0)
                        {{ round((($product->price - $product->price_sale) / $product->price) * 100, 2) }} %
                    @else
                        N/A
                    @endif
                </td>
                
                <td class="border border-gray-300 px-2 py-4 items-center text-center">{{ $product->stock }}</td>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">
                    <img src="{{ asset('./storage/images' . $product->image_url) }}"  width="100">
                </td>
                
                <td class="border border-gray-300 px-2 py-4 items-center text-center">
                    @if($product->variants->isNotEmpty())
                        @foreach($product->variants as $variant)
                            @foreach($variant->attributes as $attribute)
                                @if($attribute->attribute_name == 'Color')
                                    {{ $attribute->attribute_value }}<br>
                                @endif
                            @endforeach
                        @endforeach
                    @else
                        Chưa có màu
                    @endif
                </td>
                <td class="border border-gray-300 px-2 py-4 items-center text-center">{{ $product->category->name ?? 'Không có danh mục' }}</td>
                <td class="border border-gray-300 flex gap-2 items-center  px-2 py-4  text-center">
                    <a  class="cursor-pointer border p-1.5 rounded-full border-green-500 bg-green-500" href="{{ route('products.edit', $product->id) }}"><svg class="" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="cursor-pointer border p-1.5 rounded-full border-red-500 bg-red-500" onclick="return confirm('Xóa sản phẩm này?')"><svg class="" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9.1709 4C9.58273 2.83481 10.694 2 12.0002 2C13.3064 2 14.4177 2.83481 14.8295 4" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"></path> <path d="M20.5001 6H3.5" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"></path> <path d="M18.8332 8.5L18.3732 15.3991C18.1962 18.054 18.1077 19.3815 17.2427 20.1907C16.3777 21 15.0473 21 12.3865 21H11.6132C8.95235 21 7.62195 21 6.75694 20.1907C5.89194 19.3815 5.80344 18.054 5.62644 15.3991L5.1665 8.5" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"></path> <path d="M9.5 11L10 16" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"></path> <path d="M14.5 11L14 16" stroke="#FFF" stroke-width="1.5" stroke-linecap="round"></path> </g></svg></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
