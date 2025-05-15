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

<div class="container">
    <h4 class="mb-4">Danh sách đánh giá sản phẩm</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Người dùng</th>
                    <th>Sản phẩm</th>
                    <th>Biến thể</th>
                    <th>Đánh giá</th>
                    <th>Bình luận</th>
                    <th>Media</th>
                    <th>Thời gian</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>{{ $review->user->username }}</td>
                        <td>{{ $review->product->name }}</td>
                        <td>
                            @if($review->variant && $review->variant->attributes)
                                @foreach($review->variant->attributes as $attr)
                                    <span class="badge bg-secondary">{{ $attr->variantAttribute->attribute_name }}: {{ $attr->variantAttribute->attribute_value }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-warning">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                            @endfor
                        </td>
                        <td>{{ $review->comment }}</td>
                        <td>
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
                        <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                        <td>
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
    </div>

    <div class="mt-4">
        {{ $reviews->links('pagination::tailwind') }}
    </div>
</div>
@endsection
