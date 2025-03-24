<?php

namespace App\Http\Controllers;


use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
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
    $product = Product::with(['category', 'images'])
                ->findOrFail($id);

    // Lấy ảnh sản phẩm chính (nếu có)
    $productImages = $product->images->where('type', 'main')->first();

    return view('client.products.detail', compact('product', 'productImages'));
}

public function showdetail($id)
{
    // Lấy sản phẩm với các mối quan hệ cần thiết
    $product = Product::with(['category', 'variants.variantAttributeValues', 'variants.images', 'images'])
                     ->findOrFail($id);

    // Kiểm tra nếu sản phẩm có biến thể
    $firstVariant = $product->variants->isNotEmpty() ? $product->variants->first() : null;

    // Lấy ảnh của biến thể đầu tiên, nếu không có thì lấy ảnh của sản phẩm chính
    $variantImages = $firstVariant && $firstVariant->images->isNotEmpty() ? $firstVariant->images : $product->images;

    // Lấy tất cả các kích thước của biến thể đầu tiên (nếu có)
    $sizes = $firstVariant ? $firstVariant->variantAttributeValues->where('attribute_id', 2) : [];

    // Trả về view với các dữ liệu cần thiết
    return view('client.products.detail', compact('product', 'firstVariant', 'variantImages', 'sizes'));
}
public function getVariantDetails(Request $request)
{
    $variant = ProductVariant::with('images', 'sizes') // Assuming sizes are related to variants
        ->where('variant_id', $request->variant_id)
        ->first();

    if ($variant) {
        return response()->json([
            'price' => number_format($variant->price, 0, ',', '.'),
            'sale_price' => $variant->price_sale ? number_format($variant->price_sale, 0, ',', '.') : null,
            'images' => $variant->images,
            'sizes' => $variant->sizes,
        ]);
    }

    return response()->json(['error' => 'Variant not found'], 404);
}







}
