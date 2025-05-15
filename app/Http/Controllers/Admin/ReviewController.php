<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = OrderReview::with(['user', 'product', 'variant.attributes.variantAttribute', 'orderDetail'])
                    ->latest()
                    ->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy($id)
    {
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
    // Lưu phản hồi (tùy bạn lưu ở đâu, ví dụ trong cột `admin_response` của bảng reviews)
    $review->admin_response = $request->response;
    $review->save();

    return redirect()->route('admin.reviews.index')->with('success', 'Đã gửi phản hồi thành công.');
}

}
