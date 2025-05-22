@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto my-8 px-4">
    <h4 class="mb-6 text-2xl font-semibold text-gray-100">Danh sách đánh giá sản phẩm</h4>

    <table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
        <thead class="bg-gray-100 text-gray-700">
            <tr class="border-b border-gray-300">
                <th class="border px-4 py-3">Sản phẩm</th>
                <th class="border px-4 py-3">Tổng số đánh giá</th>
                <th class="border px-4 py-3">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($productsWithReviewCount as $product)
                <tr class="hover:bg-blue-50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-medium text-left">
                        <a href="{{ route('products.detail', $product->product_id) }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ $product->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-semibold">
                        {{ $product->reviews_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.reviews.productReviews', $product->product_id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                            Xem đánh giá
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-6 text-gray-500 italic">Không có sản phẩm nào có đánh giá.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6 flex justify-center">
        {{ $productsWithReviewCount->links('pagination::tailwind') }}
    </div>
</div>
@endsection
