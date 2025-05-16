{{-- Kiểm tra nếu không có đánh giá --}}
@if ($reviews->isEmpty())
    <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
@else
    <style>
    .lb-close {
        color: black !important;
        opacity: 1 !important;
        font-size: 30px !important;
        z-index: 9999 !important;
    }

    .review {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
    }

    .review:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }

    .review-header .avatar {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 12px;
        border: 2px solid #ddd;
    }

    .review-header strong {
        font-size: 16px;
        color: #333;
    }

    .rating .star {
        font-size: 18px;
        color: #ccc;
        margin-left: 1px;
    }

    .rating .star.filled {
        color: #f1c40f;
    }

    .review-comment {
        font-size: 14px;
        line-height: 1.5;
        color: #444;
    }

    .review-time {
        font-size: 12px;
        color: #888;
    }

    .review-media {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .review-media img,
    .review-media video {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
    }

    .shop-response {
        background-color: #f6fdf9;
        border-left: 4px solid #28a745;
        padding: 12px;
        margin-top: 15px;
        border-radius: 5px;
        color: #155724;
        font-size: 14px;
        font-style: italic;
    }

    .shop-response strong {
        display: block;
        margin-bottom: 5px;
        font-style: normal;
        color: #1b5e20;
    }
</style>


    @foreach ($reviews as $review)
        <div class="review border p-3 mb-4 rounded shadow-sm">
            <div class="review-header d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                  <img src="{{ $review->user->avatar ? asset('storage/avatars/' . $review->user->avatar) : asset('images/default-avatar.png') }}"
     alt="Avatar" class="avatar">

                    <strong>{{ $review->user->username }}</strong>
                </div>
                <div class="rating text-warning">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                    @endfor
                </div>
            </div>

            <div class="review-product-info mb-2">
               
                @if ($review->orderDetail->variant && $review->orderDetail->variant->attributes)
                    <div class="mt-1">
                        @foreach ($review->orderDetail->variant->attributes as $attrValue)
                            @php
                                $attribute = $attrValue->variantAttribute;
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

                <div class="review-media d-flex flex-wrap gap-2">
                    @foreach($review->media_paths as $media)
                        @php
                            $ext = pathinfo($media, PATHINFO_EXTENSION);
                            $fileUrl = asset('storage/' . $media);
                        @endphp

                        @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                            <a data-fancybox="gallery-{{ $review->id }}" href="{{ $fileUrl }}">
                                <img src="{{ $fileUrl }}" class="rounded">
                            </a>
                        @elseif (in_array($ext, ['mp4', 'webm', 'mov']))
                            <a data-fancybox="gallery-{{ $review->id }}" href="{{ $fileUrl }}" data-type="video">
                                <video muted controls>
                                    <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                                </video>
                            </a>
                        @endif
                    @endforeach
                </div>

                <span class="review-time text-muted d-block mt-2">{{ $review->created_at->format('d/m/Y H:i') }}</span>

                {{-- Phản hồi từ shop --}}
                @if ($review->admin_response)
                    <div class="shop-response mt-3">
                        <strong>Phản hồi từ shop:</strong><br>
                        {{ $review->admin_response }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif
