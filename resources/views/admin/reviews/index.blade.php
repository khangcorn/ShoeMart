@extends('admin.layout')

@section('content')
<style>
    .star-rating {
  font-size: 1.2rem;  /* Tăng kích thước sao */
  color: #d1d5db;     /* Màu xám nhạt cho sao chưa đánh */
  letter-spacing: 2px; /* Khoảng cách giữa các sao */
  display: inline-block;
}

.star-rating .filled {
  color: #fbbf24; /* Màu vàng cho sao được đánh */
}

</style>
<div class="max-w-7xl mx-auto my-8 px-4">
    <h4 class="mb-6 text-2xl font-semibold text-gray-800">Danh sách đánh giá sản phẩm</h4>

    @if(session('success'))
        <div id="flash-message" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            {{ session('success') }}
        </div>
    @elseif(session('info'))
        <div id="flash-message" class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
            {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div id="flash-error" class="mb-4 bg-red-500 text-white p-4 rounded">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(() => {
                const flashError = document.getElementById('flash-error');
                if (flashError) flashError.remove();
            }, 5000);
        </script>
    @endif

   
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr class="border-b border-gray-300">
                    <th class="border px-4 py-3">Người dùng</th>
                    <th class="border px-4 py-3">Sản phẩm</th>
                    <th class="border px-4 py-3">Biến thể</th>
                    <th class="border px-4 py-3">Đánh giá</th>
                    <th class="border px-4 py-3">Bình luận</th>
                    <th class="border px-4 py-3">Media</th>
                    <th class="border px-4 py-3">Thời gian</th>
                    <th class="border px-4 py-3">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                    <tr class="hover:bg-blue-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-medium">{{ $review->user->username }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $review->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            @if($review->variant && $review->variant->attributes)
                                @foreach($review->variant->attributes as $attr)
                                    <span class="inline-block bg-gray-200 text-gray-800 text-xs font-semibold mr-1 px-2.5 py-0.5 rounded">
                                        {{ $attr->variantAttribute->attribute_name }}: {{ $attr->variantAttribute->attribute_value }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-400 italic">Không có</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            <span class="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <span class="filled">&#9733;</span>
                                @else
                                    <span>&#9734;</span>
                                @endif
                            @endfor
                            </span>
                        </td>


                        <td class="px-6 py-4 whitespace-normal text-gray-700 max-w-xs">{{ $review->comment }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach($review->media_paths as $media)
                                    @php
                                        $ext = pathinfo($media, PATHINFO_EXTENSION);
                                        $url = asset('storage/' . $media);
                                    @endphp

                                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <a href="{{ $url }}" data-fancybox="review-{{ $review->id }}">
                                            <img src="{{ $url }}" alt="media" class="w-14 h-14 object-cover rounded border border-gray-300 shadow-sm" />
                                        </a>
                                    @elseif(in_array($ext, ['mp4', 'webm', 'mov']))
                                        <a href="{{ $url }}" data-fancybox="review-{{ $review->id }}" data-type="video">
                                            <video class="w-14 h-14 rounded border border-gray-300 shadow-sm" muted>
                                                <source src="{{ $url }}" type="video/{{ $ext }}">
                                            </video>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $review->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($review->admin_response)
                                <span class="text-green-700 font-semibold text-sm mb-2 inline-block">Đã phản hồi</span>
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium transition">Xóa</button>
                                </form>
                            @else
                                <a href="{{ route('admin.reviews.reply', $review->id) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-medium mb-2 transition">Phản hồi</a>
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium transition">Xóa</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-gray-500 italic">Không có đánh giá nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-center">
        {{ $reviews->links('pagination::tailwind') }}
    </div>
</div>

<script>
    setTimeout(() => {
        const flash = document.getElementById('flash-message');
        if (flash) flash.style.display = 'none';
    }, 5000);
</script>
@endsection
