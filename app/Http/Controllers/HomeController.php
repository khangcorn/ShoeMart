<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    public function home()
    {
        $products = Product::with('category', 'images')->paginate(10);
        $user = Auth::user();

        return view('client.home', compact(['products', 'user']));
    }

    public function show($id)
    {
        // Lấy sản phẩm với các mối quan hệ cần thiết (chỉ lấy ảnh sản phẩm chính)
        $product = Product::with(['category', 'images'])->findOrFail($id);

        // Lấy ảnh sản phẩm chính (nếu có)
        $productImages = $product->images->where('type', 'main')->first();

        return view('client.products.detail', compact('product', 'productImages'));
    }

public function showDetail($productId)
{
    // Lấy sản phẩm cùng các relation
    $product = Product::with([
        'variants.variantAttributeValues.variantAttribute',
        'images',
        'category',
    ])->findOrFail($productId);

    // Lấy tất cả màu sắc và kích thước
    $colors = $product->variants->flatMap(function ($variant) {
        return $variant->variantAttributeValues->where('variantAttribute.attribute_name', 'Color')->pluck('variantAttribute.attribute_value');
    })->unique();

    $sizes = $product->variants->flatMap(function ($variant) {
        return $variant->variantAttributeValues->where('variantAttribute.attribute_name', 'Size')->pluck('variantAttribute.attribute_value');
    })->unique();

    // Lấy đánh giá
    $reviews = $product->orderReviews()
        ->where('is_hidden', false)
        ->with([
            'user',
            'orderDetail.product',
            'orderDetail.variant.attributes.variantAttribute',
        ])->get();

    // Lấy danh sách product_id đã yêu thích của user hiện tại (nếu có)
    $wishlistedProductIds = [];
    if (auth()->check()) {
        $wishlistedProductIds = Wishlist::where('user_id', auth()->id())
            ->pluck('product_id')
            ->toArray();
    }

    return view('client.products.detail', compact('product', 'colors', 'sizes', 'reviews', 'wishlistedProductIds'));
}


    public function indexVoucher(Request $request)
    {
        // Lưu URL trước đó vào session
        session(['previous_url' => $request->headers->get('referer')]);

        // Lấy danh sách mã giảm giá
        $coupons = Coupon::orderBy('created_at', 'desc')->get();

        return view('client.vouchers.index', compact('coupons'));
    }

    // ✅ **Thêm phương thức để load tất cả sản phẩm**
    public function getall()
    {

        // Lấy tất cả sản phẩm cùng với danh mục và hình ảnh
        $products = Product::with('category', 'images')->get();

        // Trả về view hiển thị tất cả sản phẩm
        return view('client.products.all', compact('products'));

    }
}
