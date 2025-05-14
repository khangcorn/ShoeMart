{{-- Kiểm tra nếu không có đánh giá --}}
@if ($reviews->isEmpty())
    <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
@else
    {{-- Hiển thị các đánh giá --}}
    <style>
        .lb-close {
            color: black !important;
            opacity: 1 !important;
            font-size: 30px !important;
            z-index: 9999 !important;
        }
        .review-media {
    display: flex;
    flex-wrap: wrap; /* Đảm bảo các phần tử có thể xuống dòng khi cần */
    gap: 10px; /* Khoảng cách giữa các ảnh */
    justify-content: flex-start; /* Căn chỉnh các ảnh từ bên trái */
}

.review-media img {
    width: 100px; /* Chiều rộng của ảnh */
    height: 100px; /* Chiều cao của ảnh */
    object-fit: cover; /* Giữ tỷ lệ ảnh */
    border-radius: 5px; /* Bo góc ảnh */
}
.review-media video {
    width: 100px;
    height: auto; /* Đảm bảo tỷ lệ video được giữ */
    border-radius: 5px; /* Bo góc video */
}
    </style>

    @foreach ($reviews as $review)
        <div class="review border p-3 mb-3 rounded shadow-sm">
            <div class="review-header d-flex justify-content-between align-items-center mb-2">
                <div class="user-info">
                    <strong>{{ $review->user->name }}</strong>
                </div>
                <div class="rating text-warning">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                    @endfor
                </div>
            </div>

            {{-- Tên sản phẩm và biến thể --}}
            <div class="review-product-info mb-2">
                <strong>Sản phẩm:</strong> {{ $review->orderDetail->product->name ?? 'Không xác định' }}
                @if ($review->orderDetail->variant && $review->orderDetail->variant->attributes)
                    <div>
                        @foreach ($review->orderDetail->variant->attributes as $attrValue)
                            @php
                                $attribute = $attrValue->variantAttribute; // Lấy từ quan hệ
                            @endphp
                            <span class="badge bg-secondary me-1">
                                {{ $attribute->attribute_name }}: {{ $attribute->attribute_value }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

          <div class="review-body">
    <p class="review-comment mb-2">{{ $review->comment }}</p>

    {{-- Media --}}
    <div class="review-media d-flex flex-wrap gap-2">
        @foreach($review->media_paths as $index => $media)
            @php
                $ext = pathinfo($media, PATHINFO_EXTENSION);
                $fileUrl = asset('storage/' . $media);
            @endphp

            @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                <a data-fancybox="gallery-{{ $review->id }}" href="{{ $fileUrl }}">
                    <img src="{{ $fileUrl }}" style="width: 100px; object-fit: cover;" class="rounded">
                </a>
            @elseif (in_array($ext, ['mp4', 'webm', 'mov']))
                <a data-fancybox="gallery-{{ $review->id }}" href="{{ $fileUrl }}" data-type="video">
                    <video width="100" muted class="rounded" controls>
                        <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                    </video>
                </a>
            @endif
        @endforeach
    </div>
</div>

            <span class="review-time text-muted ms-2">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</span>
        </div>
    @endforeach
@endif
