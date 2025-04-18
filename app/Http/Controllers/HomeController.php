<?php

namespace App\Http\Controllers;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

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
        // Lấy sản phẩm theo ID, bao gồm các biến thể, ảnh, danh mục, và các thuộc tính của biến thể
        $product = Product::with([
            'variants.variantAttributeValues.variantAttribute', // Lấy các thuộc tính (size, color) của biến thể
            'images', // Lấy ảnh của sản phẩm
            'category' // Lấy danh mục của sản phẩm
        ])->findOrFail($productId);

        // Lấy tất cả màu sắc và kích thước của các biến thể
        $colors = $product->variants->flatMap(function ($variant) {
            return $variant->variantAttributeValues->where('variantAttribute.attribute_name', 'Color')->pluck('variantAttribute.attribute_value');
        })->unique();

        $sizes = $product->variants->flatMap(function ($variant) {
            return $variant->variantAttributeValues->where('variantAttribute.attribute_name', 'Size')->pluck('variantAttribute.attribute_value');
        })->unique();

        // Trả về view với thông tin sản phẩm và các giá trị màu sắc, kích thước
        return view('client.products.detail', compact('product', 'colors', 'sizes'));
    }
    public function indexVoucher()
    {
        // Lấy tất cả các mã giảm giá đang hoạt động
        $coupons = Coupon::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Trả về view với danh sách mã giảm giá
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
