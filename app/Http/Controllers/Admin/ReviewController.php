<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
 public function index()
{
    // Lấy danh sách sản phẩm kèm tổng số đánh giá, có thể phân trang
    $productsWithReviewCount = \App\Models\Product::withCount(['reviews' => function ($query) {
        // Nếu user không phải admin hoặc staff thì chỉ lấy review chưa ẩn
        if (!auth()->user()->hasRole(['admin', 'staff'])) {
            $query->where('is_hidden', false);
        }
    }])
    ->orderBy('reviews_count', 'desc')
    ->paginate(10);

    return view('admin.reviews.index', compact('productsWithReviewCount'));
}
public function show($productId)
{
    $product = \App\Models\Product::with(['reviews.user', 'reviews.variant.attributes.variantAttribute'])->findOrFail($productId);

    // Nếu user không phải admin/staff, chỉ lấy review chưa ẩn
    $reviews = $product->reviews()
        ->when(!auth()->user()->hasRole(['admin', 'staff']), function($query) {
            $query->where('is_hidden', false);
        })
        ->latest()
        ->paginate(5);

    return view('admin.reviews.product_reviews', compact('product', 'reviews'));
}

    public function destroy($id)
    {
        if (! auth()->user()->hasPermission('review.delete')) {
            return redirect()->route('admin.reviews.index')->with('error', 'Bạn không có quyền xóa đánh giá.');
        }
        $review = OrderReview::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }

    public function reply($id)
    {
        $review = OrderReview::findOrFail($id);

        return view('admin.reviews.reply', compact('review'));
    }

    public function storeReply(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string',
        ]);

        $review = OrderReview::findOrFail($id);

        $review->admin_response = $request->response;
        $review->save();

        return redirect()->route('admin.reviews.productReviews', ['productId' => $review->product_id])->with('success', 'Đã gửi phản hồi thành công.');
    }
        public function toggleHidden(OrderReview $review)
        {
            $review->is_hidden = !$review->is_hidden;
            $review->save();

            return redirect()->back()->with('success', 'Cập nhật trạng thái ẩn review thành công.');
        }

}
