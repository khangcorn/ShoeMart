@extends('admin.layout')

@section('content')
<div class="container mx-auto py-6 px-4">
    <div class="bg-white shadow-md rounded-2xl border border-gray-200">
        <div class="bg-blue-600 text-white px-6 py-4 rounded-t-2xl">
            <h2 class="text-lg font-semibold">Phản hồi đánh giá của <strong>{{ $review->user->username }}</strong></h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.reviews.reply.store', $review->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="response" class="block text-sm font-medium text-gray-700 mb-1">Nội dung phản hồi</label>
                    <textarea id="response" name="response" rows="4" required
                        placeholder="Nhập nội dung phản hồi..."
                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3 text-sm resize-none">
                    </textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.reviews.productReviews', ['productId' => $review->product_id]) }}"
                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                        Quay lại
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                        Gửi phản hồi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
