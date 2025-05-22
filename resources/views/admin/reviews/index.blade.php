@extends('admin.layout')

@section('content')
<style>
    /* Căn giữa text và hình ảnh */
    .table td, .table th {
        vertical-align: middle;
    }
    .table img, .table video {
        object-fit: cover;
    }

    /* Nền trắng và chữ đen */
    body {
        background-color: white !important;
        color: black !important;
    }

    /* Bảng sáng */
    .table {
        background-color: white !important;
        color: black !important;
    }

    /* Tiêu đề bảng nền đậm */
    .table th {
        background-color: #343a40 !important;
        color: white !important;
    }

    /* Viền bảng */
    .table td, .table th {
        border-color: #dee2e6 !important;
    }

    /* Ảnh và video trong bảng */
    .table img, .table video {
        object-fit: cover;
    }

    /* Nút thao tác */
    .btn-action {
        margin-right: 5px;
    }

    /* Nút phản hồi */
    .btn-feedback {
        background-color: #0d6efd;
        color: white;
        border: none;
        padding: 4px 10px;
        font-size: 0.85rem;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    .btn-feedback:hover {
        background-color: #0b5ed7;
        color: white;
        text-decoration: none;
    }
</style>
@if(session('success'))
        <div class=" bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded relative" id="flash-message" >
            {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.remove();
            }
        }, 5000); 
    </script>
@endif

<div class="px-4 py-4">



        <table class="w-full">
            <thead class="table-dark">
                <tr>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Người dùng</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Sản phẩm</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Biến thể</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Đánh giá</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Bình luận</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Media</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Thời gian</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $review->user->username }}</td>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $review->product->name }}</td>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                            @if($review->variant && $review->variant->attributes)
                                @foreach($review->variant->attributes as $attr)
                                    <span class="badge bg-secondary">{{ $attr->variantAttribute->attribute_name }}: {{ $attr->variantAttribute->attribute_value }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-warning">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                            @endfor
                        </td>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $review->comment }}</td>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                @foreach($review->media_paths as $media)
                                    @php
                                        $ext = pathinfo($media, PATHINFO_EXTENSION);
                                        $url = asset('storage/' . $media);
                                    @endphp

                                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <a href="{{ $url }}" data-fancybox="review-{{ $review->id }}">
                                            <img src="{{ $url }}" width="60" height="60" class="rounded border">
                                        </a>
                                    @elseif(in_array($ext, ['mp4', 'webm', 'mov']))
                                        <a href="{{ $url }}" data-fancybox="review-{{ $review->id }}" data-type="video">
                                            <video width="60" height="60" muted class="rounded border">
                                                <source src="{{ $url }}" type="video/{{ $ext }}">
                                            </video>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $review->created_at->format('d/m/Y H:i') }}</td>
                        <td  class="font-semibold border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                            <!-- Nút phản hồi -->
                            <a href="{{ route('admin.reviews.reply', $review->id) }}" class="btn-feedback btn-action">Phản hồi</a>

                            <!-- Nút xóa -->
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-action">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted">Không có đánh giá nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>


    <div class="">
        {{ $reviews->links('pagination::tailwind') }}
    </div>
</div>
@endsection
