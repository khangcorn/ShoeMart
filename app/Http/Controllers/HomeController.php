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
    // Lấy sản phẩm với các quan hệ: category, variants, attributes, images
    $products = Product::with(['category', 'variants.attributes', 'images'])->findOrFail($id);
    
    // Trả về view cho client và truyền dữ liệu sản phẩm
    return view('client.products.detail', compact('products'));
}



}
