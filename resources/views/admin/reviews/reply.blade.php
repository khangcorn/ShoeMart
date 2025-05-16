@extends('admin.layout')

@section('content')
<div class="container">
    <h4>Phản hồi đánh giá của {{ $review->user->username }}</h4>

    <form action="{{ route('admin.reviews.reply.store', $review->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="response" class="form-label">Nội dung phản hồi</label>
            <textarea class="form-control" id="response" name="response" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
