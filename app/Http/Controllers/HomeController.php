<?php

namespace App\Http\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
{
    $products = Product::with('category', 'images')->paginate(10);
    return view('client.home', compact('products'));
}
public function show($id)
{
    // Lấy sản phẩm với các mối quan hệ cần thiết
    $product = Product::with(['category', 'variants.variantAttributeValues', 'variants.images', 'images'])
                ->findOrFail($id);

    // Kiểm tra nếu có biến thể
    $firstVariant = $product->variants->isNotEmpty() ? $product->variants->first() : null;

    // Lấy ảnh của biến thể đầu tiên, nếu không có thì lấy ảnh của sản phẩm
    $variantImages = $firstVariant && $firstVariant->images->isNotEmpty() ? $firstVariant->images : $product->images;

    // Lấy tất cả các kích thước của biến thể đầu tiên (nếu có)
    $sizes = $firstVariant ? $firstVariant->variantAttributeValues->where('attribute_id', 2) : [];

    return view('client.products.detail', compact('product', 'firstVariant', 'variantImages', 'sizes'));
}





}
