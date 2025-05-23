@extends('admin.layout')

@section('content')
<style>
    
</style>
<div class="container py-4">
    <div class="card shadow rounded-4 border-0">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="mb-0">Phản hồi đánh giá của <strong>{{ $review->user->username }}</strong></h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reviews.reply.store', $review->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="response" class="form-label fw-semibold">Nội dung phản hồi</label>
                    <textarea class="form-control" id="response" name="response" rows="4" required placeholder="Nhập nội dung phản hồi..."></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.reviews.productReviews', ['productId' => $review->product_id]) }}" class="btn btn-outline-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
